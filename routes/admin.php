<?php

use \App\Controller\Admin;

// ADICIONANADO A ROTA HOME DE ADMINISTRAÇÃO
$router->get("/admin", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ()
    {
        return new Response(200, Admin\Home::getHome());
    }
]);

// ADICIONANDO A ROTA DE CADASTRO DE ASSISTENTE
$router->get("/admin/new", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ()
    {
        return new Response(200, Admin\NewAssistant::getNewAssistant());
    }
]);

// ADICIONANDO A ROTA DE CADASTRO DE ASSISTENTE (POST)
$router->post("/admin/new", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request)
    {
        return new Response(200, Admin\NewAssistant::setNewAssistant($request));
    }
]);

?>