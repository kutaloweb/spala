<?php

namespace App\Repositories;

use App\User;
use App\Profile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Notifications\Activation;
use Illuminate\Validation\ValidationException;

class UserRepository
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var RoleRepository
     */
    protected $role;

    /**
     * Instantiate a new Repository instance.
     *
     * @param User $user
     * @param RoleRepository $role
     */
    public function __construct(User $user, RoleRepository $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * Get all users with profile and roles
     *
     * @return array
     */
    public function getAll()
    {
        return $this->user->with('profile', 'roles')->get();
    }

    /**
     * Count users
     *
     * @return int
     */
    public function count()
    {
        return $this->user->count();
    }

    /**
     * Count users registered between dates
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return int
     */
    public function countDateBetween($start_date, $end_date)
    {
        return $this->user->createdAtDateBetween(['start_date' => $start_date, 'end_date' => $end_date])->count();
    }

    /**
     * Find user by Id
     *
     * @param int|null $id
     *
     * @return User
     * @throws ValidationException
     */
    public function findOrFail($id = null)
    {
        $user = $this->user->with('profile', 'roles')->find($id);

        if (!$user) {
            throw ValidationException::withMessages(['message' => trans('user.could_not_find')]);
        }

        return $user;
    }

    /**
     * Find user by email
     *
     * @param string|null $email
     *
     * @return User
     */
    public function findByEmail($email = null)
    {
        return $this->user->with('profile', 'roles')->filterByEmail($email)->first();
    }

    /**
     * Find user by activation token
     *
     * @param string $token
     *
     * @return User
     */
    public function findByActivationToken($token = null)
    {
        return $this->user->with('profile', 'roles')->whereActivationToken($token)->first();
    }

    /**
     * List users except authenticated one
     *
     * @return array
     */
    public function listByNameAndEmailExceptAuthUser()
    {
        return $this->user->where('id', '!=', \Auth::user()->id)->get()->pluck('name_with_email', 'id')->all();
    }

    /**
     * Paginate all users using given params.
     *
     * @param array $params
     *
     * @return LengthAwarePaginator
     */
    public function paginate($params = array())
    {
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $first_name = isset($params['first_name']) ? $params['first_name'] : null;
        $last_name = isset($params['last_name']) ? $params['last_name'] : null;
        $email = isset($params['email']) ? $params['email'] : null;
        $role_id = isset($params['role_id']) ? $params['role_id'] : null;
        $status = isset($params['status']) ? $params['status'] : null;
        $created_at_start_date = isset($params['created_at_start_date']) ? $params['created_at_start_date'] : null;
        $created_at_end_date = isset($params['created_at_end_date']) ? $params['created_at_end_date'] : null;

        $query = $this->user->with('profile', 'roles')
            ->filterByFirstName($first_name)
            ->filterByLastName($last_name)
            ->filterByEmail($email)
            ->filterByRoleId($role_id)
            ->filterByStatus($status)
            ->createdAtDateBetween([
                'start_date' => $created_at_start_date,
                'end_date' => $created_at_end_date
            ]);

        if ($sort_by === 'first_name') {
            $query->select('users.*', \DB::raw('(select first_name from profiles where users.id = profiles.user_id) as first_name'))->orderBy('first_name', $order);
        } elseif ($sort_by === 'last_name') {
            $query->select('users.*', \DB::raw('(select last_name from profiles where users.id = profiles.user_id) as last_name'))->orderBy('last_name', $order);
        } else {
            $query->orderBy($sort_by, $order);
        }

        return $query->paginate($page_length);
    }

    /**
     * Create a new user.
     *
     * @param array $params
     * @param int $register
     *
     * @return User
     */
    public function create($params, $register = 0)
    {
        $user = $this->user->forceCreate($this->formatParams($params, 'register'));

        if ($register) {
            $role_id = $this->role->findByName(config('system.default_role.' . ($this->count() > 1 ? 'user' : 'admin')))->id;
        } else {
            $role_id = $this->role->findByName(config('system.default_role.user'))->id;
        }

        $this->assignRole($user, $role_id);
        $profile = $this->associateProfile($user);
        $this->updateProfile($profile, $params);

        if ($register && config('config.email_verification')) {
            $user->notify(new Activation($user));
        }

        return $user;
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $action
     *
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'email' => isset($params['email']) ? $params['email'] : null,
            'status' => 'activated',
            'password' => isset($params['password']) ? bcrypt($params['password']) : null,
            'activation_token' => Str::uuid()
        ];

        if ($action === 'register') {
            if (config('config.email_verification')) {
                $formatted['status'] = 'pending_activation';
            } elseif (config('config.account_approval')) {
                $formatted['status'] = 'pending_approval';
            }
        }

        return $formatted;
    }

    /**
     * Assign role to user.
     *
     * @param $user
     * @param int $role_id
     * @param string $action
     */
    private function assignRole($user, $role_id, $action = 'attach')
    {
        if ($action === 'attach') {
            $user->assignRole($this->role->listNameById($role_id));
        } else {
            $user->roles()->sync($role_id);
        }
    }

    /**
     * Associate user to profile.
     *
     * @param User $user
     *
     * @return Profile
     */
    private function associateProfile($user)
    {
        $profile = new Profile;
        $user->profile()->save($profile);

        return $profile;
    }

    /**
     * Update user profile.
     *
     * @param Profile
     *
     * @param array $params
     */
    public function updateProfile($profile, $params = [])
    {
        $profile->first_name = isset($params['first_name']) ? $params['first_name'] : $profile->first_name;
        $profile->last_name = isset($params['last_name']) ? $params['last_name'] : $profile->last_name;
        $profile->address_line_1 = isset($params['address_line_1']) ? $params['address_line_1'] : $profile->address_line_1;
        $profile->address_line_2 = isset($params['address_line_2']) ? $params['address_line_2'] : $profile->address_line_2;
        $profile->city = isset($params['city']) ? $params['city'] : $profile->city;
        $profile->state = isset($params['state']) ? $params['state'] : $profile->state;
        $profile->zipcode = isset($params['zipcode']) ? $params['zipcode'] : $profile->zipcode;
        $profile->country_id = isset($params['country_id']) ? $params['country_id'] : $profile->country_id;
        $profile->gender = isset($params['gender']) ? $params['gender'] : $profile->gender;
        $profile->phone = isset($params['phone']) ? $params['phone'] : $profile->phone;
        $profile->date_of_birth = isset($params['date_of_birth']) ? ($params['date_of_birth'] ?: null) : $profile->date_of_birth;
        $profile->save();
    }

    /**
     * Update given user.
     *
     * @param User $user
     * @param array $params
     *
     * @return User
     */
    public function update(User $user, $params = [])
    {
        $this->updateProfile($user->profile, $params);
        if (isset($params['role_id'])) {
            $this->assignRole($user, $params['role_id'], 'sync');
        }

        return $user;
    }

    /**
     * Delete user.
     *
     * @param User $user
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(User $user)
    {
        try {
            return $user->delete();
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1451) {
                throw ValidationException::withMessages(['message' => trans('user.integrity_constraint_violation')]);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Update given user status.
     *
     * @param User $user
     * @param string $status
     *
     * @return User
     * @throws ValidationException
     */
    public function status(User $user, $status = null)
    {
        if (!in_array($status, ['activated', 'pending_activation', 'pending_approval', 'banned', 'disapproved'])) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $user->status = $status;
        $user->save();

        return $user;
    }

    /**
     * Force reset user password.
     *
     * @param User $user
     * @param string|null $password
     *
     * @return User
     */
    public function forceResetPassword(User $user, $password = null)
    {
        $user->password = bcrypt($password);
        $user->save();

        return $user;
    }
}
