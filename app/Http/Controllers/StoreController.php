<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\CreateStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Services\Store\StoreService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __construct(
        protected StoreService $storeService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = (int) $request->query('per_page', 15);
        // $search = $request->query('search', '');
        [$stores, $meta] = $this->storeService->getAllStores($user, $perPage);

        $data = [
            'stores' => $stores,
            'meta' => $meta,
        ];

        $message = 'stores loaded successfully';

        return $this->response(200, $message, $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStoreRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $response = $this->storeService->createStore($user, $data);

        return $this->response(201, 'store created sucessfully', $response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $data = $this->storeService->getStore($user, $id);
        $message = 'store found successfully';

        return $this->response(200, $message, $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, string $id)
    {
        $data = $request->validated();
        $user = $request->user();
        $response = $this->storeService->updateStore($user, $id, $data);

        return $this->response(200, 'store updated successfully', $response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $this->storeService->deleteStore($user, $id);

        return $this->response(200, 'stored deleted');
    }
}
