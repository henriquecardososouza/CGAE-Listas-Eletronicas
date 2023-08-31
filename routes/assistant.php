<?php

use \App\Http\Response;
use \App\Controller\Assistant;

// INCLUINDO AS ROTAS DE CADA MÓDULO
include __DIR__."/assistant modules/signature.php";
include __DIR__."/assistant modules/student.php";

// ADICIONANDO A ROTA DO PAINEL DE ASSISTENTE
$router->get("/assistant", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Assistant\Home::getHome());
    }
]);

// ADICIONANDO A ROTA DE PERFIL
$router->get("/assistant/profile", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Assistant\Profile::getProfile());
    }
]);

// ADICIONANDO A ROTA DE EDIÇÃO DE PERFIL
$router->get("/assistant/edit", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Assistant\EditProfile::getEditProfile($request));
    }
]);

// ADICIONANDO A ROTA DE EDIÇÃO DE PERFIL (POST)
$router->post("/assistant/edit", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Assistant\EditProfile::setEditProfile($request));
    }
]);