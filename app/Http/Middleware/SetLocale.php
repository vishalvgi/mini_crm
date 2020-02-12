<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use App;
use Session;

class SetLocale {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        App::setlocale(Session::get('locale'));
        return $next($request);
    }

}
