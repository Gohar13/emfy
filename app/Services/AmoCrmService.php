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
    const ACCESS_TOKEN_FILE_NAME = 'token_info.json';

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

        $noteModel = new CommonNote();
        $noteModel->setEntityId($hookData['id'])->setText($this->getNoteData($actionType, $hookData));
        $leadNotesService = $this->amoApiClient->notes($entityType);
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

        Storage::disk('private')->put(self::ACCESS_TOKEN_FILE_NAME, json_encode($data, true));
    }

    /**
     * @throws FileNotFoundException
     */
    public function getTokenFromFile(): AccessToken
    {
        $accessToken = json_decode(Storage::disk('private')->get(self::ACCESS_TOKEN_FILE_NAME), true);

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
            $this->setAccessToken($accessToken);
        } else {

            $newAccessToken = $this->amoApiClient->getOAuthClient()->getAccessTokenByRefreshToken($accessToken);
            $this->saveTokenInFile($newAccessToken);
            $this->setAccessToken($newAccessToken);
        }
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     */
    private function getNoteData($actionType, $data): string
    {
        $data =  match($actionType) {
            self::ENTITY_ACTION_ADDED => [
                'название' => $data['name'],
                'ответственный' => $this->amoApiClient->users()->getOne($data['responsible_user_id'])->getName(),
                'время_добавления_карточки' => date('Y-m-d H:i:s', $data['created_at']),
            ],
            self::ENTITY_ACTION_UPDATED => [
                'название' => $data['name'],
                'ответственный' => $this->amoApiClient->users()->getOne($data['responsible_user_id'])->getName(),
                'время_изменения' => date('Y-m-d H:i:s', $data['updated_at']),
            ]
        };

        return implode(', ', array_map(function ($key, $value) {
            return str_replace('_', ' ', mb_strtoupper($key)) . ': ' . $value;
        }, array_keys($data), $data));
    }
}
