<?php

namespace App\Http\Controllers\Webhook\Amo;

use App\Http\Controllers\Controller;
use App\Services\AmoCrmService;

class AmoController extends Controller
{
    protected AmoCrmService $amoService;

    public function __construct(AmoCrmService $amoService)
    {
        $this->amoService = $amoService;
    }
}
