<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AmoCrmService implements CrmServiceInterface
{
    public function __construct(public AmoCRMApiClient $apiClient)
    {

    }

    public function updateContact()
    {
        dd($this->apiClient->contacts()->get());
    }

    public function updateDeal()
    {

    }
}
