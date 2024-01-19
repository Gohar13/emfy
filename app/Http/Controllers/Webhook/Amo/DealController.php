<?php

namespace App\Http\Controllers\Webhook\Amo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DealController extends AmoController
{
    public function added(Request $request)
    {
        Log::info('Deal:added '.json_encode($request->toArray(), true));
    }

    public function updated(Request $request)
    {
        Log::info('Deal:updated '.json_encode($request->toArray(), true));
    }
}
