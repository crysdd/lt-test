<?php

namespace App\Http\Services\V1;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUserAndToken($request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('api_token')->plainTextToken;

        return $token;
    }

    public function checkUserPassword($password, $user_password)
    {
        if (Hash::check($password, $user_password)) {
            return true;
        }
        return false;
    }
}
