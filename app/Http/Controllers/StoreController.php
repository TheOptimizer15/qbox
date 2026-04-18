<?php

namespace App\Http\Controllers;

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
        $search = $request->query('search', '');
        [$stores, $meta] = $this->storeService->getAllStores($user, $perPage);
        $this->data = [
            'stores' => $stores,
            'meta' => $meta,
        ];

        $this->message = 'stores loaded successfully';

        return $this->response(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->data = $this->storeService->getStore($id);
        $this->message = 'store found successfully';
        return $this->response(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
