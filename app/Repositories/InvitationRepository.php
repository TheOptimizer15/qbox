<?php

namespace App\Repositories;

use App\Common\Repository\BaseRepository;
use App\Enums\TenantRole;
use App\Models\Invitation;
use App\Models\User;

/**
 * @extends BaseRepository<Invitation>
 */

class InvitationRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        Invitation $invitation
    )
    {   
        $this->model = $invitation;
    }

    public function createInvitation(string $storeId, $name, TenantRole $role, string $email, string $phoneNumber, User $invitedBy){
        $invitation = $this->model->newInstance();

        $invitation->store_id = $storeId;
        $invitation->name = $name;
        $invitation->role = $role;
        $invitation->invitation_id = \Str::random(40);
        $invitation->email = $email;
        $invitation->phone_number = $phoneNumber;
        $invitation->expires_at = now()->addHours(5);
        $invitation->invited_by = $invitedBy->id;

        $invitation->save();
        return $invitation;
    }
}
