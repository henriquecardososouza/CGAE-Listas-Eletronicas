<?php

use \App\Controller\Page;
use \App\Http\Response;

// INCLUINDO AS ROTAS GERAIS DO SITE
include __DIR__."/student.php";
include __DIR__."/assistant.php";

// ADICIONANDO A ROTA DE LOGIN
$router->get("/login", [
    "middlewares" => [
        "without-login"
    ],

    function ()
    {
        return new Response(200, Page\Login::getLogin());
    }
]);

// ADICIONANDO A ROTA DE LOGIN (POST)
$router->post("/login", [
    "middlewares" => [
        "without-login"
    ],

    function ($request)
    {
        return new Response(200, Page\Login::setLogin($request));
    }
]);

// ADCIONANDO A ROTA DE LOGOUT
$router->get("/logout", [
    "middlewares" => [
        "require-login"
    ],

    function ($request)
    {
        return new Response(200, Page\Login::setLogout($request));
    }
]);