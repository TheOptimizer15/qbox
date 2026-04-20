<?php

namespace App\Services\Store;

use App\Enums\TenantRole;
use App\Exceptions\BadRequestException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\Invitation;
use App\Models\User;
use App\Repositories\InvitationRepository;
use App\Repositories\UserRepository;
use App\Services\SMS\SmsService;

/**
 * This service allows a company owner to manage the tenants of a given store
 * The service uses the sms service provider interface to send sms to invite a user
 * The user has now the possibility to join a store or decline the inivation
 */
class StoreTenantService
{
    public function __construct(
        protected InvitationRepository $invitationRepository,
        protected UserRepository $userRepository,
        protected SmsService $smsService
    ) {}

    public function inviteTenant(array $data)
    {
        $invitation = $this->invitationRepository->createInvitation(
            $data['store_id'],
            $data['name'],
            $data['role'],
            $data['email'],
            $data['phone_number']
        );

        $name = $invitation->name ?? '';

        $role = match ($invitation->role) {
            TenantRole::MANAGER => 'gestionnaire',
            TenantRole::CASHIER => 'caissier',
            TenantRole::ASSISTANT => 'assitant'
        };

        $storeName = $invitation->store->name;

        $message = "Bonjour $name, vous avez été invité en tant que $role pour la boutique".
                    ":$storeName. Acceptez l'inivation via ce lien: ";

        $this->smsService->send($invitation->phone_number, $message);

        return $invitation;
    }

    public function acceptInvitation($invitationId, array $userData)
    {
        $invitation = $this->invitationRepository->findById($invitationId);

        if (! $invitation) {
            throw new NotFoundException('inivation not found or expired');
        }

        if ($invitation->isExpired()) {
            throw new ForbiddenException('invalid or expired inivation');
        }

        // if invitation is accepted take user role from the invitation and pass it to the user
        $user = $this->userRepository->create([
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'phone_number' => $userData['phone_number'],
            'password' => $userData['password'],
            'role' => $invitation->role,
            'store_id' => $invitation->store_id,
        ]);

        
        return $user;
    }

    public function denyInvitation($invitationId)
    {
        $invitation = $this->invitationRepository->findById($invitationId);

        if (! $invitation) {
            throw new NotFoundException('inivation not found or expired');
        }

        if ($invitation->isExpired()) {
            throw new BadRequestException('invitation already expired');
        }

        return $this->invitationRepository->update($invitation, [
            'expires_at' => now()->subMinutes(5),
        ]);
    }

    public function cancelInviation($invitationId)
    {
        $invitation = $this->invitationRepository->findById($invitationId);

        if (! $invitation) {
            throw new NotFoundException('inivation not found or expired');
        }

        if ($invitation->isExpired()) {
            throw new BadRequestException('invitation already expired');
        }

        return $this->invitationRepository->delete($invitation);
    }
}
