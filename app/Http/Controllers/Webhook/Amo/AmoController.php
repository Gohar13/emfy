<?php

namespace App\Http\Controllers\Webhook\Amo;

use App\Http\Controllers\Controller;
use App\Services\AmoCrmService;
use App\Services\CrmServiceInterface;

class AmoController extends Controller
{
    public function __construct(public AmoCrmService $service)
    {

    }

    public function getToken(): void
    {
        dd(11);
        $accessToken = $this->service->apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

        if (!$accessToken->hasExpired()) {
            saveToken([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $this->service->apiClient->getAccountBaseDomain(),
            ]);
        }

       // $ownerDetails = $this->service->apiClient->getOAuthClient()->getResourceOwner($accessToken);
    }

}
