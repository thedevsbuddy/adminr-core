<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Route;


/**
 * Get the coreui native svg icon(s)
 *
 * @param $id
 * @return string
 */
if(!function_exists('coreUiIcon')){
    function coreUiIcon($id){
        return asset('vendor/liquid-lite/coreui/free.svg#'.$id);
    }
}


/**
 * @param array $routes
 * @return boolean
 */
if (!function_exists('activeRoutes')) {
    function activeRoutes($routes)
    {
        return in_array(Route::currentRouteName(), $routes);
    }
}

/**
 * @param String $route
 * @return boolean
 */
if (!function_exists('activeRoute')) {
    function activeRoute($route)
    {
        return Route::currentRouteName() == $route;
    }
}


/**
 * Return provided value if current route
 * matches with the provided route name
 *
 * @param String $route
 * @param mixed $return
 * @param mixed $fallback
 * @return boolean
 */
if (!function_exists('returnIfRoute')) {
    function returnIfRoute($route, $return, $fallback = null)
    {
        if(Route::currentRouteName() == $route){
            return $return;
        } else {
            return $fallback;
        }
    }
}


/**
 * Return provided value if current route
 * matches with the provided route name
 *
 * @param String $route
 * @param mixed $return
 * @param mixed $fallback
 * @return boolean
 */
if (!function_exists('returnIfRoutes')) {
    function returnIfRoutes($routes, $return, $fallback = null)
    {
        if(in_array(Route::currentRouteName(), $routes)){
            return $return;
        } else {
            return $fallback;
        }
    }
}



/**
 * Returns the setting value by the
 * option provided
 *
 * @param String $option
 */
if (!function_exists('getSetting')) {
    function getSetting($option)
    {
        return Setting::where('option', $option)->first()
            ? Setting::where('option', $option)->first()->value
            : null;
    }
}

const LIQUID_VERSION = "0.1.0";