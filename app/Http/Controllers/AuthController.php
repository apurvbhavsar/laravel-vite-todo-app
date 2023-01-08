<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return $this->validationError($validateUser->errors(), 'validation error');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ], 201);

            $data = [
                'token' => $user->createToken("API_TOKEN")->plainTextToken
            ];
            return $this->success($data, 'User Created Successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return $this->validationError($validateUser->errors(), 'validation error');
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return $this->error('Email & Password does not exist.', 401);
            }

            $user = User::where('email', $request->email)->first();

            $data = [
                'token' => $user->createToken("API_TOKEN")->plainTextToken
            ];
            return $this->success($data, 'Logged In Successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
}
