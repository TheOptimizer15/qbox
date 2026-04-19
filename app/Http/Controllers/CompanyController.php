<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CreateCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
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

    public function store(CreateCompanyRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $response = $this->companyService->createCompany($user, $data);
        $message = 'company created successfully';

        return $this->response(201, $message, $response);
    }

    public function update(UpdateCompanyRequest $request){
        $user = $request->user();
        $data = $request->validated();
        $response = $this->companyService->updateName($user, $data['name']);
        return $this->response(200, 'company name update', $response);
    }

    public function destroy($id){
       $this->companyService->deleteCompany($id);
        return $this->response(200, 'company deleted');
    }
}
