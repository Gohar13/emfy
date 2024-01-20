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

class LeadController extends AmoController
{
    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws FileNotFoundException
     */
    public function added(Request $request)
    {
        $hookData = $request->get('leads')['add'][0];
        $this->amoService->updateEntity(EntityTypesInterface::LEADS, AmoCrmService::ENTITY_ACTION_ADDED, $hookData);
        Log::info('Deal:added '.json_encode($hookData, true));
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws FileNotFoundException
     */
    public function updated(Request $request)
    {
        $hookData = $request->get('leads')['update'][0];
        $this->amoService->updateEntity(EntityTypesInterface::LEADS, AmoCrmService::ENTITY_ACTION_UPDATED, $hookData);
        Log::info('Deal:updated '.json_encode($hookData, true));
    }
}
