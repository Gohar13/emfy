<?php

namespace App\Http\Controllers\Webhook\Amo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends AmoController
{
    public function added(Request $request)
    {
        dd($this->service->updateContact());
        Log::info('Contact:added '.json_encode($request->toArray(), true));
    }

    public function updated(Request $request)
    {
        Log::info('Contact:updated '.json_encode($request->toArray(), true));
    }
}
