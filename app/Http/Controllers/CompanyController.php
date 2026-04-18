<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CreateCompanyRequest;
use App\Services\Company\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyService $companyService
    ) {}

    public function index(Request $request)
    {
        $data = $this->companyService->getCompany($request->user());
        $message = 'your company has been loaded';

        return $this->response(200, $message, $data);
    }

    public function create(CreateCompanyRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $data = $this->companyService->createCompany($user, $data);
        $message = 'company created successfully';

        return $this->response(200, $message, $data);
    }
}
