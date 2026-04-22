<?php

namespace App\Http\Controllers;

use App\Services\Store\StoreTenantService;
use Illuminate\Http\Request;

class StoreTenantController extends Controller
{
    public function __construct(
        protected StoreTenantService $storeTenantService
    ) {}

    public function getAllTenants(Request $request, ?string $storeId)
    {
        $perPage = $request->query('per_page');
        $user = $request->user();
        $response = $this->storeTenantService->tenants($user, $storeId);

        return $this->response(200, 'tenants loaded successfully', $response);
    }

    public function blockTenant(Request $request, $tenantId)
    {
        $owner = $request->user();
        $response = $this->storeTenantService->block($owner, $tenantId);

        return $this->response(200, 'tenant blocked', $response);
    }

    public function unblockTenant(Request $request, $tenantId)
    {
        $owner = $request->user();
        $response = $this->storeTenantService->unblock($owner, $tenantId);

        return $this->response(200, 'tenant unblocked', $response);
        
    }
}
