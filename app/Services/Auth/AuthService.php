<?php

namespace App\Services\Auth;

use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function login($phoneNumber, $password)
    {

        $user = $this->userRepository->getByPhoneNumber($phoneNumber);

        if (! $user) {
            throw new UnauthorizedException('incoorect credentials');
        }

        /**
         * @var User $user
         */

        if(!$user->isActive()){
            throw new ForbiddenException('you have been blocked and cannot use your account');
        }

        if (! Hash::check($password, $user->password)) {
            throw new UnauthorizedException('incorrect credentials');
        }

        $accessToken = $user->createToken('auth', [$user->role], now()->addDay())->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $accessToken,
        ];
    }

    public function register($firstName, $lastName, $phone_number, $password)
    {
        $user = $this->userRepository->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => $phone_number,
            'password' => Hash::make($password),
            'role' => 'owner',
        ]);

        /**
         * @var User $user
         */
        return [
            'user' => $user,
            'access_token' => $user->createToken('auth', ["$user->role"], now()->addDay())->plainTextToken,
        ];
    }

    public function logout(User $user)
    {
        return $user->tokens()->delete();
    }
}
