<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $message = 'logged in successfully';
        $responseData = $this->authService->login($data['phone_number'], $data['password']);

       return $this->response(200, $message, $responseData);
    }

    public function signup(SignupRequest $request){
        $data = $request->validated();
        $message = 'account created successfully';
        $responseData = $this->authService->register(
            $data['first_name'],
            $data['last_name'],
            $data['phone_number'],
            $data['password'],
        );

        return $this->response(201, $message, $responseData);
    }

    public function logout(Request $request){
        $user = $request->user();
        $this->authService->logout($user);
        
        return $this->response(200, 'logged out successfully');
    }
}
