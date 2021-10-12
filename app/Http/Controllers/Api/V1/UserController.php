<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\GetApiTokenRequest;
use App\Http\Requests\V1\UserCreateRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Services\V1\UserService;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a paginated listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::paginate());
    }

    /**
     * Create new User with API token.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(UserCreateRequest $request, UserService $user_service)
    {
        $token = $user_service->createUserAndToken($request);

        return response(['token' => $token, 'token_type' => 'Bearer'], 201);
    }

    /**
     * Get API Token
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getApiToken(GetApiTokenRequest $request, UserService $user_service)
    {
        $user = User::getUserFromEmail($request->email);
        if (! $user instanceof User) {
            return response(['errors' => ['user not found']], 404);
        }
        if (! $user_service->checkUserPassword($request->password, $user->password)) {
            return response(['errors' => ['incorrect password']], 403);
        }
        $token = $user->createToken('api_token')->plainTextToken;

        return response(['token' => $token, 'token_type' => 'Bearer']);
    }
}
