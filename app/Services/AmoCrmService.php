<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Models\NoteType\CommonNote;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AmoCrmService implements CrmServiceInterface
{
    protected AmoCRMApiClient $amoApiClient;

    const ENTITY_ACTION_ADDED = 'add';
    const ENTITY_ACTION_UPDATED = 'update';

    public function __construct(AmoCRMApiClient $amoApiClient)
    {
        $this->amoApiClient = $amoApiClient;
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws FileNotFoundException
     */
    public function updateEntity(string $entityType, $actionType, array $hookData): void
    {
        $this->getToken();

        $leadNotesService = $this->amoApiClient->notes($entityType);

        $noteModel = new CommonNote();
        $noteModel->setEntityId($hookData['id'])->setText($actionType);
        $leadNotesService->addOne($noteModel);
    }


    private function setAccessToken(AccessTokenInterface $accessToken): void
    {
        $this->amoApiClient
            ->setAccessToken($accessToken)
            ->setAccountBaseDomain(config('amo.base_domain'))
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    $this->saveTokenInFile($accessToken);
                }
            );
    }

    private function saveTokenInFile(AccessTokenInterface $accessToken): void
    {
        $data = [
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
            'baseDomain' => config('amo.base_domain'),
        ];

        Storage::disk('private')->put('token_info.json', json_encode($data, true));
    }

    /**
     * @throws FileNotFoundException
     */
    public function getTokenFromFile(): AccessToken
    {
        $accessToken = json_decode(Storage::disk('private')->get('token_info.json'), true);

        return new AccessToken([
            'access_token' => $accessToken['accessToken'],
            'refresh_token' => $accessToken['refreshToken'],
            'expires' => $accessToken['expires'],
            'baseDomain' => $accessToken['baseDomain'],
        ]);
    }

    /**
     * @throws AmoCRMoAuthApiException
     * @throws FileNotFoundException
     */
    private function getToken(): void
    {
        $accessToken = $this->getTokenFromFile();

        if (!$accessToken->hasExpired()) {

            $this->saveTokenInFile($accessToken);

            $this->setAccessToken($accessToken);
        } else {

            $this->amoApiClient->getOAuthClient()->getAccessTokenByRefreshToken($accessToken->getRefreshToken());
        }
    }
}
