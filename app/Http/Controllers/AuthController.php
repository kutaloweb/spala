<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Repositories\AuthRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\RegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\ActivityLogRepository;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var AuthRepository
     */
    protected $repo;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'user';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param AuthRepository $repo
     * @param UserRepository $user
     * @param ActivityLogRepository $activity
     */
    public function __construct(Request $request, AuthRepository $repo, UserRepository $user, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->user = $user;
        $this->activity = $activity;
    }

    /**
     * Authenticate user
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function authenticate(LoginRequest $request)
    {
        $auth = $this->repo->auth($this->request->all());

        $authUser = $auth['user'];
        $token = $auth['token'];
        $two_factor_code = $auth['two_factor_code'];

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $authUser->id,
            'user_id' => $authUser->id,
            'activity' => 'logged_in'
        ]);

        return $this->success([
            'message' => trans('auth.logged_in'),
            'token' => $token,
            'user' => $authUser,
            'two_factor_code' => $two_factor_code
        ]);
    }

    /**
     * Check whether user is authenticated or not
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function check()
    {
        return $this->success($this->repo->check());
    }

    /**
     * Logout user
     *
     * @return Response|JsonResponse
     */
    public function logout()
    {
        $authUser = Auth::user();

        try {
            $token = JWTAuth::getToken();
        } catch (JWTException $e) {
            return $this->error($e->getMessage());
        }

        JWTAuth::invalidate($token);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $authUser->id,
            'user_id' => $authUser->id,
            'activity' => 'logged_out'
        ]);

        return $this->success(['message' => trans('auth.logged_out')]);
    }

    /**
     * Create user
     *
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(RegisterRequest $request)
    {
        $this->repo->validateRegistrationStatus();

        $this->user->create($this->request->all(), 1);

        return $this->success(['message' => trans('auth.account_created')]);
    }

    /**
     * Activate new user
     *
     * @param $activationToken
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function activate($activationToken)
    {
        $this->repo->activate($activationToken);

        return $this->success(['message' => trans('auth.account_activated')]);
    }

    /**
     * Request password reset token for user
     *
     * @param PasswordRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function password(PasswordRequest $request)
    {
        $this->repo->password($this->request->all());

        return $this->success(['message' => trans('passwords.sent')]);
    }

    /**
     * Validate user password
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function validatePasswordReset()
    {
        $this->repo->validateResetPasswordToken(request('token'));

        return $this->success(['message' => '']);
    }

    /**
     * Reset user password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function reset(ResetPasswordRequest $request)
    {
        $this->repo->reset($this->request->all());

        return $this->success(['message' => trans('passwords.reset')]);
    }

    /**
     * Change user password
     *
     * @param ChangePasswordRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $this->repo->validateCurrentPassword(request('current_password'));

        $this->repo->resetPassword(request('new_password'));

        $this->activity->record([
            'module' => $this->module,
            'module_id' => Auth::user()->id,
            'sub_module' => 'password',
            'activity' => 'resetted'
        ]);

        return $this->success(['message' => trans('passwords.change')]);
    }

    /**
     * Verify password during Screen Lock
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function lock(LoginRequest $request)
    {
        $this->repo->validateCurrentPassword(request('password'));

        $this->activity->record([
            'module' => $this->module,
            'module_id' => Auth::user()->id,
            'sub_module' => 'screen',
            'activity' => 'unlocked'
        ]);

        return $this->success(['message' => trans('auth.lock_screen_verified')]);
    }
}
