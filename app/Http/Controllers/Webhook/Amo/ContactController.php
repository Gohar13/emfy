<?php

namespace App\Http\Controllers\Webhook\Amo;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use App\Services\AmoCrmService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends AmoController
{
    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws FileNotFoundException
     */
    public function added(Request $request)
    {
        $hookData = $request->get('contacts')['add'][0];
        $this->amoService->updateEntity(EntityTypesInterface::CONTACTS, AmoCrmService::ENTITY_ACTION_ADDED, $hookData);
        Log::info('Contact:added '.json_encode($request->toArray(), true));
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws FileNotFoundException
     * @throws AmoCRMoAuthApiException
     */
    public function updated(Request $request)
    {
        $hookData = $request->get('contacts')['update'][0];
        $this->amoService->updateEntity(EntityTypesInterface::CONTACTS, AmoCrmService::ENTITY_ACTION_ADDED, $hookData);
        Log::info('Contact:updated '.json_encode($request->toArray(), true));
    }
}
