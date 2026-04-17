<?php

namespace App\Services\Auth;

use App\Exceptions\ForbiddenException;
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
            throw new ForbiddenException('login failed check your credentials');
        }

        /**
         * @var User $user
         */
        if (! Hash::check($password, $user->password)) {
            throw new ForbiddenException('login failed check your credentials');
        }

        $accessToken = $user->createToken('auth', [$user->role])->plainTextToken;

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
            'access_token' => $user->createToken('auth', ["$user->role"])->plainTextToken,
        ];
    }

    public function logout(User $user)
    {
        return $user->tokens()->delete();
    }
}
