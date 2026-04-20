<?php

namespace App\Services\Invitation;

use App\Enums\TenantRole;
use App\Events\InvitationCreatedEvent;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Repositories\InvitationRepository;
use App\Repositories\StoreRepository;

/**
 * Handles invitation for stores
 * Only allowed to company owners
 */
class InvitationService
{
    public function __construct(
        protected InvitationRepository $invitationRepository,
        protected StoreRepository $storeRepository
    ) {}

    /**
     * Invite a user by sending him an sms through his phone number
     * Assign him a role and a store he will be allowed to access
     *
     * @param array{
     * name?: string,
     * store_id: string,
     * role: TenantRole,
     * email: string,
     * phone_number: string,
     * invited_by: User
     * } $data
     */
    public function invite(array $data)
    {
        $this->isStoreOwner($data['invited_by'], $data['store_id']);

        $invitation = $this->invitationRepository->createInvitation(
            $data['store_id'],
            $data['name'],
            $data['role'],
            $data['email'],
            $data['phone_number'],
            $data['invited_by']
        );

        /**
         * @var array{
         * name: string,
         * phone_number: string,
         * email: string
         * } $invitationEventData
         */
        $invitationEventData = [
            'name' => $invitation->name,
            'phone_number' => $invitation->phone_number,
            'email' => $invitation->email,
        ];

        InvitationCreatedEvent::dispatch($invitationEventData);

        return $invitation;
    }

    public function accept() {}

    public function deny() {}

    public function cancel() {}

    /**
     * Asserts user is the owner of the company to which the store belongs
     * @param User  $user
     * @param string  
     * @return void
     * @throws ForbiddenException
     * @throws NotFoundException
    */
    private function isStoreOwner(User $user, string $storeId){
        $store = $this->storeRepository->findById($storeId);
        if(!$store){
            throw new NotFoundException('store not found cannot create invitation');
        }

        $owner = $store->company->owner;

        if($owner->id !== $user->id){
            throw new ForbiddenException('you cannot create an invitation for a store you do not own');
        }
    }
}
