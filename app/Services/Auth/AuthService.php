<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

/**
 * Handles authentication operations including login, registration, and logout.
 */
class AuthService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    /**
     * Authenticate a user with phone number and password.
     *
     * Validates the user's credentials and account status before
     * issuing a Sanctum personal access token valid for 24 hours.
     *
     * @param  string  $phoneNumber  The user's phone number.
     * @param  string  $password  The user's plain-text password.
     * @return array{user: User, access_token: string}
     *
     * @throws UnauthorizedException  If the phone number is not found or the password is incorrect.
     * @throws ForbiddenException  If the user's account has been blocked.
     */
    public function login(string $phoneNumber, string $password): array
    {
        $user = $this->userRepository->getByPhoneNumber($phoneNumber);

        if (! $user) {
            throw new UnauthorizedException('incorrect credentials');
        }

        /** @var User $user */

        if (! $user->isActive()) {
            throw new ForbiddenException('you have been blocked and cannot use your account');
        }

        if (! Hash::check($password, $user->password)) {
            throw new UnauthorizedException('incorrect credentials');
        }

        $accessToken = $user->createToken('auth', ["*"], now()->addDay())->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $accessToken,
        ];
    }

    /**
     * Register a new user account with the owner role.
     *
     * Creates the user record and issues a Sanctum personal access token
     * valid for 24 hours.
     *
     * @param  string  $firstName  The user's first name.
     * @param  string  $lastName  The user's last name.
     * @param  string  $phoneNumber  The user's phone number (must be unique).
     * @param  string  $password  The user's plain-text password (hashed via model cast).
     * @return array{user: User, access_token: string}
     */
    public function register(string $firstName, string $lastName, string $phoneNumber, string $password): array
    {
        $user = $this->userRepository->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => $phoneNumber,
            'password' => $password,
            'role' => UserRole::OWNER,
        ]);

        /** @var User $user */

        return [
            'user' => $user,
            'access_token' => $user->createToken('auth', ["*"], now()->addDay())->plainTextToken,
        ];
    }

    /**
     * Log out the user by revoking all of their access tokens.
     *
     * @param  User  $user  The authenticated user to log out.
     * @return int  The number of tokens deleted.
     */
    public function logout(User $user): int
    {
        return $user->tokens()->delete();
    }
}
