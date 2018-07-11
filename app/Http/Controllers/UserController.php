<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserProfileRequest;
use App\Repositories\ActivityLogRepository;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UserRepository
     */
    protected $repo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var RoleRepository
     */
    protected $role;

    /**
     * @var string
     */
    protected $module = 'user';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param UserRepository $repo
     * @param ActivityLogRepository $activity
     * @param RoleRepository $role
     */
    public function __construct(Request $request, UserRepository $repo, ActivityLogRepository $activity, RoleRepository $role)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->role = $role;
    }

    /**
     * Get pre-requisites for user module
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function preRequisite()
    {
        $this->authorize('create', User::class);

        $countries = generateSelect(config('country'));
        $genders = generateSelectVueTranslated(config('list.gender'));
        $roles = generateSelectVue($this->role->listExceptName([config('system.default_role.admin')]));

        return $this->success(compact('countries', 'roles', 'genders'));
    }

    /**
     * Get all users
     *
     * @return LengthAwarePaginator
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('view', User::class);

        return $this->repo->paginate($this->request->all());
    }

    /**
     * Store user
     *
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(RegisterRequest $request)
    {
        $this->authorize('create', User::class);

        $user = $this->repo->create($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'created'
        ]);

        return $this->success(['message' => trans('user.added')]);
    }

    /**
     * Get current user.
     *
     * @return User
     * @throws ValidationException
     */
    public function detail()
    {
        return $this->repo->findOrFail(Auth::user()->id);
    }

    /**
     * Get user details
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function show($id)
    {
        $this->authorize('view', User::class);

        $user = $this->repo->findOrFail($id);
        $selected_roles = generateSelectVue($user->roles()->pluck('name', 'id')->all());
        $roles = $user->roles()->pluck('id')->all();

        return $this->success(compact('user', 'selected_roles', 'roles'));
    }

    /**
     * Update user
     *
     * @param UserProfileRequest $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(UserProfileRequest $request, $id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('update', $user);

        $this->repo->update($user, $this->request->all());

        return $this->success(['message' => trans('user.profile_updated')]);
    }

    /**
     * Update user status
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function updateStatus($id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('update', $user);

        $this->repo->status($user, request('status'));

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('user.profile_updated')]);
    }

    /**
     * Update user contact info
     *
     * @param UserProfileRequest $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function updateContact(UserProfileRequest $request, $id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('update', $user);

        $user = $this->repo->update($user, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('user.profile_updated')]);
    }

    /**
     * Change user password
     *
     * @param ChangePasswordRequest $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function forceResetPassword(ChangePasswordRequest $request, $id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('forceResetUserPassword', $user);

        $user = $this->repo->forceResetPassword($user, request('new_password'));

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('passwords.change')]);
    }

    /**
     * Update user profile
     *
     * @param UserProfileRequest $request
     *
     * @return JsonResponse
     */
    public function updateProfile(UserProfileRequest $request)
    {
        $auth_user = Auth::user();

        $this->repo->update($auth_user, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $auth_user->id,
            'sub_module' => 'profile',
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('user.profile_updated')]);
    }

    /**
     * Update user avatar
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function uploadAvatar($id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('avatar', $user);

        $image_path = config('system.upload_path.avatar') . '/';
        $profile = $user->profile;
        $image = $profile->avatar;

        if ($image && File::exists($image)) {
            File::delete($image);
        }

        $extension = request()->file('image')->getClientOriginalExtension();
        $filename = uniqid();
        request()->file('image')->move($image_path, $filename . "." . $extension);
        $img = \Image::make($image_path . $filename . "." . $extension);
        $img->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($image_path . $filename . "." . $extension);
        $profile->avatar = $image_path . $filename . "." . $extension;
        $profile->save();

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'sub_module' => 'avatar',
            'activity' => 'uploaded'
        ]);

        return $this->success(['message' => trans('user.avatar_uploaded'), 'image' => $image_path . $filename . "." . $extension]);
    }

    /**
     * Remove user avatar
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function removeAvatar($id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('avatar', $user);

        $profile = $user->profile;
        $image = $profile->avatar;

        if (!$image) {
            return $this->error(['message' => trans('user.no_avatar_uploaded')]);
        }
        if (File::exists($image)) {
            File::delete($image);
        }

        $profile->avatar = null;
        $profile->save();

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'sub_module' => 'avatar',
            'activity' => 'removed'
        ]);

        return $this->success(['message' => trans('user.avatar_removed')]);
    }

    /**
     * Delete user
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws \Exception
     */
    public function destroy($id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('delete', $user);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($user);

        return $this->success(['message' => trans('user.deleted')]);
    }
}
