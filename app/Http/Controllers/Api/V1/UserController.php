<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\GetApiTokenRequest;
use App\Http\Requests\V1\UserCreateRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    public function create(UserCreateRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('api_token')->plainTextToken;

        return response(['token' => $token, 'token_type' => 'Bearer'], 201);
    }

    /**
     * Get API Token
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getApiToken(GetApiTokenRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! Hash::check($request->password, $user->password)) {
            return response(['errors' => ['incorrect password']], 403);
        }
        $token = $user->createToken('api_token')->plainTextToken;

        return response(['token' => $token, 'token_type' => 'Bearer']);
    }
}
