<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\Invitation\AcceptInvitationRequest;
use App\Http\Requests\Invitation\CreateInvitationRequest;
use App\Services\Invitation\InvitationService;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function __construct(
        protected InvitationService $invitationService
    ) {}

    /**
     * Send a store invitation to a user.
     */
    public function invite(CreateInvitationRequest $request)
    {
        $inputs = $request->validated();
        $role = $request->enum('role', UserRole::class);

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

    /**
     * Accept an invitation and create the tenant account.
     */
    public function accept(AcceptInvitationRequest $request, string $id)
    {
        $inputs = $request->validated();
        $invitationData = [...$inputs, 'invitation_id' => $id];

        [$user, $invitation] = $this->invitationService->accept($invitationData);

        return $this->response(201, 'invitation accepted', [
            'user' => $user,
            'invitation' => $invitation,
        ]);
    }

    /**
     * Deny an invitation.
     */
    public function deny(string $id)
    {
        $response = $this->invitationService->deny($id);

        return $this->response(200, 'invitation denied', $response);
    }

    /**
     * Cancel an invitation (owner only).
     */
    public function cancel(Request $request, string $id)
    {
        $user = $request->user();
        $this->invitationService->cancel($user, $id);

        return $this->response(200, 'invitation cancelled');
    }
}
