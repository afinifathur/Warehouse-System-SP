<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WmsWarehouseContextMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !session()->has('active_warehouse_id')) {
            $defaultWarehouse = auth()->user()->warehouses()->first();
            if ($defaultWarehouse) {
                session()->put('active_warehouse_id', $defaultWarehouse->id);
                session()->put('active_warehouse_code', $defaultWarehouse->code);
                session()->put('active_warehouse_name', $defaultWarehouse->name);
            }
        }

        return $next($request);
    }
}
