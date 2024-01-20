<?php

namespace App\Http\Controllers\Webhook\Amo;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use App\Services\AmoCrmService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;

class LeadController extends AmoController
{
    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws FileNotFoundException
     */
    public function added(Request $request): void
    {
        $this->amoService->updateEntity(
            EntityTypesInterface::LEADS,
            AmoCrmService::ENTITY_ACTION_ADDED,
            $request->get('leads')['add'][0]
        );
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws FileNotFoundException
     */
    public function updated(Request $request): void
    {
        $this->amoService->updateEntity(
            EntityTypesInterface::LEADS,
            AmoCrmService::ENTITY_ACTION_UPDATED,
            $request->get('leads')['update'][0]
        );
    }
}
