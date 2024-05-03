<?php

use function Laravel\Folio\{middleware, name};

if(!isset($_GET['preview']) || (isset($_GET['preview']) && $_GET['preview'] != true) || !app()->isLocal()){
    middleware(['guest']);
}

// dd(trim(Request::path(), '/'));
// foreach(config('devdojo.auth.pages') as $page){
//     // if(trim(Request::path(), '/') == )
//     if(trim($page['url'], '/') == trim(Request::path(), '/')){
//         name($page['route_name']);
//     }
// }
