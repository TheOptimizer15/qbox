<?php

namespace App\Http\Controllers;

use App\Enums\TenantRole;
use App\Http\Requests\Invitation\CreateInvitationRequest;
use App\Services\Invitation\InvitationService;

class InvitationController extends Controller
{
    public function __construct(
        protected InvitationService $invitationService
    ) {}

    public function invite(CreateInvitationRequest $request)
    {
        $inputs = $request->validated();
        $role = $request->enum('role', TenantRole::class);
        $invitation = $this->invitationService->invite([
            'email' => $inputs['email'],
            'name' => $inputs['name'],
            'phone_number' => $inputs['phone_number'],
            'role' => $role,
            'store_id' => $inputs['store_id'],
            'invited_by' => $request->user(),
        ]);

        return $this->response(201, 'invitation sent', $invitation);

    }
}
