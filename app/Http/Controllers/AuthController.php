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
        $this->message = 'logged in successfully';
        $this->data = $this->authService->login($data['phone_number'], $data['password']);

       return $this->response(200);
    }

    public function signup(SignupRequest $request){
        $data = $request->validated();
        $this->message = 'account created successfully';
        $this->data = $this->authService->register(
            $data['first_name'],
            $data['last_name'],
            $data['phone_number'],
            $data['password'],
        );

        return $this->response(200);
    }

    public function logout(Request $request){
        $user = $request->user();
        $this->authService->logout($user);
        
        return $this->response();
    }
}
