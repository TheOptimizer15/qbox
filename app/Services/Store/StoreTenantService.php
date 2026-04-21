<?php

namespace App\Services\Store;

use App\Repositories\InvitationRepository;
use App\Repositories\UserRepository;
use App\Services\Sms\SmsService;

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

   
}
