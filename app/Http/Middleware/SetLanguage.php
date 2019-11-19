<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class SetLanguage
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
        if (!Cookie::has('language')) {
            // Set the language to the default language.
            return $next($request);
        }

        $language = Cookie::get('language');
        $availableLanguages = array('nl', 'en');

        if (array_search($language, $availableLanguages) > -1) {
            if (App::getLocale() == $language) {
                return $next($request);
            }

            App::setLocale($language);
        }
        return $next($request);
    }
}
