<?php

namespace App\Http\Middleware;

// use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;


class CustomThrottleMiddleware extends ThrottleRequests
{
    protected function resolveRequestSignature($request)
    {
        // Log::channel("throttle")->info("Attempting over limit, user IP: {$request->ip()}.");
        // parent::resolveRequestSignature($request);
        

    }
}