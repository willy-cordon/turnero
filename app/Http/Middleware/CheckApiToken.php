<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $code=404;
        $message="Not Found";
        if(!empty($request->route('api_token'))){
            if(!empty($request->route('client_id'))){
                $client = Client::find($request->route('client_id'));
                if(!empty($client)){
                    if ($client->api_token == $request->route('api_token')){
                        return $next($request);
                    }else{
                        $message="Invalid Token";
                        $code= 401;
                    }
                }
            }
        }
        return response()->json(['message' => $message, 'code'=>$code], $code);
    }
}
