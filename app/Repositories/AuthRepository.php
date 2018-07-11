<?php

namespace App\Repositories;

use App\User;
use Carbon\Carbon;
use App\Events\UserLogin;
use Illuminate\Support\Str;
use App\Notifications\Activated;
use App\Notifications\PasswordReset;
use App\Notifications\PasswordResetted;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;

class AuthRepository
{
    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @var TwoFactorSecurityRepository
     */
    protected $two_factor;

    /**
     * @var ConfigurationRepository
     */
    protected $config;

    /**
     * Instantiate a new Repository instance.
     *
     * @param UserRepository $user
     * @param TwoFactorSecurityRepository $two_factor
     * @param ConfigurationRepository $config
     */
    public function __construct(UserRepository $user, TwoFactorSecurityRepository $two_factor, ConfigurationRepository $config)
    {
        $this->user = $user;
        $this->two_factor = $two_factor;
        $this->config = $config;
    }

    /**
     * Authenticate an user.
     *
     * @param array $params
     *
     * @return array
     * @throws ValidationException
     * @throws \Exception
     */
    public function auth($params = array())
    {
        $token = $this->validateLogin($params);
        $auth_user = $this->user->findByEmail($params['email']);
        $this->validateStatus($auth_user);

        event(new UserLogin($auth_user));

        $two_factor_code = $this->two_factor->set($auth_user);

        return [
            'token' => $token,
            'user' => $auth_user,
            'two_factor_code' => $two_factor_code
        ];
    }

    /**
     * Validate login credentials.
     *
     * @param array $params
     *
     * @return mixed
     * @throws ValidationException
     * @throws \Exception
     */
    public function validateLogin($params = array())
    {
        $email = isset($params['email']) ? $params['email'] : null;
        $password = isset($params['password']) ? $params['password'] : null;

        try {
            if (!$token = \JWTAuth::attempt(['email' => $email, 'password' => $password])) {
                throw ValidationException::withMessages(['email' => trans('auth.failed')]);
            }
        } catch (JWTException $e) {
            throw ValidationException::withMessages(['email' => trans('general.something_wrong')]);
        }

        return $token;
    }

    /**
     * Validate authenticated user status.
     *
     * @param User $auth_user
     *
     * @throws ValidationException
     */
    public function validateStatus($auth_user)
    {
        if ($auth_user->status === 'pending_activation') {
            throw ValidationException::withMessages(['email' => trans('auth.pending_activation')]);
        }
        if ($auth_user->status === 'pending_approval') {
            throw ValidationException::withMessages(['email' => trans('auth.pending_approval')]);
        }
        if ($auth_user->status === 'disapproved') {
            throw ValidationException::withMessages(['email' => trans('auth.not_activated')]);
        }
        if ($auth_user->status === 'banned') {
            throw ValidationException::withMessages(['email' => trans('auth.account_banned')]);
        }
        if ($auth_user->status != 'activated') {
            throw ValidationException::withMessages(['email' => trans('auth.not_activated')]);
        }
        if (!$auth_user->hasPermissionTo('enable-login')) {
            throw ValidationException::withMessages(['email' => trans('auth.login_permission_disabled')]);
        }
    }

    /**
     * Validate auth token.
     *
     * @return array
     * @throws ValidationException
     */
    public function check()
    {
        $public_config = config('system.public_config');
        $configuration = $this->config->getSelectedByName($public_config);

        try {
            \JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return ['authenticated' => false, 'config' => $configuration];
        }

        $authenticated = true;
        $configuration = $this->config->getAll();
        $configuration['post_max_size'] = getPostMaxSize();
        $configuration['pagination'] = config('system.pagination');
        $auth_user = $this->user->findOrFail(\Auth::user()->id);
        $user_roles = $auth_user->getRoleNames();
        $permissions = $auth_user->getAllPermissions();
        $default_role = config('system.default_role');

        return [
            'authenticated' => $authenticated,
            'config' => $configuration,
            'user' => $auth_user,
            'user_roles' => $user_roles,
            'permissions' => $permissions,
            'default_role' => $default_role
        ];
    }

    /**
     * Check for registration availability.
     *
     * @throws ValidationException
     */
    public function validateRegistrationStatus()
    {
        if (!config('config.registration')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Check for email verification availability.
     *
     * @throws ValidationException
     */
    public function validateEmailVerificationStatus()
    {
        if (!config('config.email_verification')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Check for account approval availability.
     *
     * @throws ValidationException
     */
    public function validateAccountApprovalStatus()
    {
        if (!config('config.account_approval')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Check for reset password availability.
     *
     * @throws ValidationException
     */
    public function validateResetPasswordStatus()
    {
        if (!config('config.reset_password')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Activate user's account.
     *
     * @param string|null $activation_token
     *
     * @throws ValidationException
     */
    public function activate($activation_token = null)
    {
        $this->validateRegistrationStatus();
        $this->validateEmailVerificationStatus();
        $user = $this->user->findByActivationToken($activation_token);

        if (!$user) {
            throw ValidationException::withMessages(['message' => trans('auth.invalid_token')]);
        }
        if ($user->status === 'activated') {
            throw ValidationException::withMessages(['message' => trans('auth.account_already_activated')]);
        }
        if ($user->status != 'pending_activation') {
            throw ValidationException::withMessages(['message' => trans('auth.invalid_token')]);
        }

        $user->status = (config('config.account_approval') ? 'pending_approval' : 'activated');
        $user->save();
        $user->notify(new Activated($user));
    }

    /**
     * Validate user for reset password.
     *
     * @param string|null $email
     *
     * @return User
     * @throws ValidationException
     */
    public function validateUserAndStatusForResetPassword($email = null)
    {
        $user = $this->user->findByEmail($email);

        if (!$user) {
            throw ValidationException::withMessages(['email' => trans('passwords.user')]);
        }
        if ($user->status != 'activated') {
            throw ValidationException::withMessages(['email' => trans('passwords.account_not_activated')]);
        }

        return $user;
    }

    /**
     * Request password reset token of user.
     *
     * @param array $params
     * @throws ValidationException
     */
    public function password($params = [])
    {
        $this->validateResetPasswordStatus();

        $user = $this->validateUserAndStatusForResetPassword($params['email']);

        $token = Str::uuid();
        \DB::table('password_resets')->insert([
            'email' => $params['email'],
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $user->notify(new PasswordReset($user, $token));
    }

    /**
     * Validate reset password token.
     *
     * @param string $token
     * @param string|null $email
     * @throws ValidationException
     */
    public function validateResetPasswordToken($token, $email = null)
    {
        if ($email) {
            $reset = \DB::table('password_resets')->where('email', '=', $email)->where('token', '=', $token)->first();
        } else {
            $reset = \DB::table('password_resets')->where('token', '=', $token)->first();
        }

        if (!$reset) {
            throw ValidationException::withMessages(['message' => trans('passwords.token')]);
        }

        if (date("Y-m-d H:i:s", strtotime($reset->created_at . "+" . config('config.reset_password_token_lifetime') . " minutes")) < date('Y-m-d H:i:s')) {
            throw ValidationException::withMessages(['email' => trans('passwords.token_expired')]);
        }
    }

    /**
     * Reset user password.
     *
     * @param $params array
     *
     * @throws ValidationException
     */
    public function reset($params = [])
    {
        $this->validateResetPasswordStatus();

        $user = $this->validateUserAndStatusForResetPassword($params['email']);

        $this->validateResetPasswordToken($params['token'], $params['email']);

        $this->resetPassword($params['password'], $user);

        \DB::table('password_resets')->where('email', '=', $params['email'])->where('token', '=', $params['token'])->delete();

        $user->notify(new PasswordResetted($user));
    }

    /**
     * Update user password.
     *
     * @param string $password
     * @param User $user
     */
    public function resetPassword($password, $user = null)
    {
        $user = ($user) ?: \Auth::user();
        $user->password = bcrypt($password);
        $user->save();
    }

    /**
     * Validate current password of user.
     *
     * @param string $password
     * @throws ValidationException
     */
    public function validateCurrentPassword($password)
    {
        if (!\Hash::check($password, \Auth::user()->password)) {
            throw ValidationException::withMessages(['message' => trans('passwords.password_mismatch')]);
        }
    }
}
