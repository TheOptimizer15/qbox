<?php

namespace App\Listeners\Store;

use App\Events\Auth\UserCreatedEvent;
use App\Repositories\StoreRepository;

class CreateStore
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected StoreRepository $storeRepository
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreatedEvent $event): void {
        $this->storeRepository->create([
            'owner_id' => $event->user->id,
            'name' => $event->storeName
        ]);
    }
}
