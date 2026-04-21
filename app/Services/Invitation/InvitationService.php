<?php

namespace App\Services\Invitation;

use App\Enums\UserRole;
use App\Events\InvitationCreatedEvent;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\Invitation;
use App\Models\Store;
use App\Models\User;
use App\Repositories\InvitationRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;

/**
 * Handles invitation for stores
 * Only allowed to company owners
 */
class InvitationService
{
    public function __construct(
        protected InvitationRepository $invitationRepository,
        protected StoreRepository $storeRepository,
        protected UserRepository $userRepository
    ) {}

    /**
     * Invite a user by sending him an sms through his phone number
     * Assign him a role and a store he will be allowed to access
     *
     * @param array{
     * name?: string,
     * store_id: string,
     * role: UserRole,
     * email: string,
     * phone_number: string,
     * invited_by: User
     * } $data
     * 
     * @return Invitation
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

        $store = $invitation->store->name;

        /**
         * @var array{
         * name: string,
         * phone_number: string,
         * email: string,
         * role: UserRole,
         * store: string
         * } $invitationEventData
         */
        $invitationEventData = [
            'name' => $invitation->name,
            'phone_number' => $invitation->phone_number,
            'email' => $invitation->email,
            'role' => $data['role'],
            'store' => $store
        ];

        InvitationCreatedEvent::dispatch($invitationEventData);

        return $invitation->load(['store']);
    }

    /**
     * Accept invitation from the user being invited
     * @param array {
     * phone_number: string,
     * passowrd: string,
     * first_name: string,
     * last_name: string,
     * invitation_id: string
     * } $invitationData
     * @return array {
     * User,
     * Invitation
     * }
     */ 
    public function accept(array $invitationData) {
        $invitation = $this->invitationRepository->getInvitation($invitationData['invitation_id']);

        if(!$invitation) throw new NotFoundException('invitation invalid, expired, or not found');

        unset($invitationData['invitation_id']);

        $userData = [
            'first_name' => $invitationData['first_name'],
            'last_name' => $invitationData['last_name'],
            'phone_number' => $invitationData['phone_number'],
            'password' => $invitationData['password'],
            'role' => $invitation->role,
            'store_id' => $invitation->store_id,
            'is_active' => true
        ];

        $user = $this->createTenant($userData);

        $invitation->accept();
        
        return [
            $user,
            $invitation->refresh()
        ];
    }

    /**
     * Deny invitation 
     * @param string $invitationId
    */
    public function deny(string $invitationId) {
         $invitation = $this->invitationRepository->getInvitation($invitationId);

        if(!$invitation) throw new NotFoundException('invitation invalid, expired, or not found');

        $invitation->deny();
        
        return $invitation->refresh();
    }

    /**
     * Cancel the invitation by deleting from the db
     * @param User $user
     * @param string  $invitationId
     * @return boolean
    */
    public function cancel(User $user, string $invitationId) {
        $invitation = $this->invitationRepository->findById($invitationId);

        if(!$invitation) throw new NotFoundException('invitation invalid, expired, or not found');

        $this->isInvitationOwner($user, $invitation);
        $this->invitationRepository->delete($invitation);
        return true;

    }

    /**
     * Asserts user is the owner of the company to which the store belongs
     * @param User  $user
     * @param string $storeId  
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

    /**
     * Uses the user repository to create tenant from the invitaion
     * @param array{
     * first_name: string,
     * last_name: string,
     * phone_number: string,
     * password: string,
     * role: UserRole
     * } $userData
     * @return User
    */
    private function createTenant(array $userData){
       return $this->userRepository->create($userData);
    }

    /**
     * Asserts the invitation belongs to a owner
     * @param User $user
     * @param Invitation $invitation
     * @throws ForbiddenException
     * @return void
    */

    private function isInvitationOwner(User $user, Invitation $invitation){
        if($invitation->invitedBy->id !== $user->id){
            throw new ForbiddenException('cannot edit or delete this invitation');
        }
    }
}
