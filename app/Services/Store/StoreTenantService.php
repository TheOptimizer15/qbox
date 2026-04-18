<?php

namespace App\Services\Store;

/**
 * This service uses sms service and send the web link to the user and then creates it
 * The user has now the possibility to join a store or decline the inivation
 */
class StoreTenantService
{
    public function __construct(
        
    )
    {
        
    }
    
    public function inviteTenant(array $data){

    }

    public function acceptInvitation($invitationId){

    }

    public function denyInvitation($invitationId){}

    public function cancelInviation($invitationId){}

    
}
