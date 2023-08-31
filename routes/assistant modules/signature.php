<?php

use \App\Http\Response;
use \App\Controller\Assistant\Modules\Signatures;

// ADICIONANDO A ROTA DE ASSINATURAS
$router->get("/assistant/signatures", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Signatures\Lists::getLists($request));
    }
]);

// ADICIONANDO A ROTA DE ASSINATURAS (POST)
$router->post("/assistant/signatures", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Signatures\Lists::setLists($request));
    }
]);

// ADICIONANDO A ROTA DE CADASTRO DE ASSINATURA
$router->get("/assistant/signatures/new", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Signatures\NewSignature::getNewSignature());
    }
]);

// ADICIONANDO A ROTA DE CADASTRO DE ASSINATURA (POST)
$router->post("/assistant/signatures/new", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Signatures\NewSignature::setNewSignature($request));
    }
]);

// ADICIONANDO A ROTA DE EDIÇÃO DE ASSINATURA
$router->get("/assistant/signatures/{list}/{id}/edit", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $list, $id) {
        return new Response(200, Signatures\Signature::getEditSignature($request, $list, $id));
    }
]);

// ADICIONANDO A ROTA DE EDIÇÃO DE ASSINATURA (POST)
$router->post("/assistant/signatures/{list}/{id}/edit", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $list, $id)
    {
        return new Response(200, Signatures\Signature::getEditSignature($request, $list, $id));
    }
]);

// ADICIONANDO A ROTA DE FECHAMENTO DE ASSINATURAS
$router->get("/assistant/signatures/{list}/{id}/close", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Signatures\Signature::getCloseSignature());
    }
]);

// ADICIONANDO A ROTA DE FECHAMENTO DE ASSINATURAS (POST)
$router->post("/assistant/signatures/{list}/{id}/close", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $list, $id)
    {
        return new Response(200, Signatures\Signature::setCloseSignature($request, $list, $id));
    }
]);

// ADICIONANDO A ROTA DE CONSULTA DE ASSINATURA
$router->get("/assistant/signatures/{list}/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $list, $id)
    {
        return new Response(200, Signatures\Signature::getSignature($request, $list, $id));
    }
]);

?>