<?php

use \App\Http\Response;
use \App\Controller\Pages;

$router->get("/lists/goandback", [
    "middlewares" => [
        "recover-cookies",
        "require-user-login"
    ],

    function () {
        return new Response(200, Pages\Listas\VaiVolta::getVaiVolta());
    }
]);

$router->post("/lists/goandback", [
    "middlewares" => [
        "recover-cookies",
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Listas\VaiVolta::setVaiVolta($request));
    }
]);

$router->get("/lists/exit", [
    "middlewares" => [
        "recover-cookies",
        "require-user-login"
    ],

    function () {
        return new Response(200, Pages\Listas\Saida::getSaida());
    }
]);

$router->post("/lists/exit", [
    "middlewares" => [
        "recover-cookies",
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Listas\Saida::setSaida($request));
    }
]);

$router->get("/lists/overnightstay", [
    "middlewares" => [
        "recover-cookies",
        "require-user-login"
    ],

    function () {
        return new Response(200, Pages\Listas\Pernoite::getPernoite());
    }
]);

$router->post("/lists/overnightstay", [
    "middlewares" => [
        "recover-cookies",
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Listas\Pernoite::setPernoite($request));
    }
]);

?>