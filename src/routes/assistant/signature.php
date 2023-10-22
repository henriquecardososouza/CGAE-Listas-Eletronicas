<?php

use \App\Http\Response;
use \App\Controller\Assistant\Signatures;

// ADICIONANDO A ROTA DE ASSINATURAS
$router->get("/ass/listas", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Signatures\Signatures::getSignatures());
    }
]);

// ADICIONANDO A ROTA DE ASSINATURAS (POST)
$router->post("/ass/listas", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Signatures\Signatures::getSignatures($request));
    }
]);

// ADICIONANDO A ROTA DE CADASTRO DE ASSINATURA
$router->get("/ass/listas/cadastrar", [
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
$router->post("/ass/listas/cadastrar", [
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

// ADICIONANDO A ROTA DE EXPORTAÇÃO DE ASSINATURAS
$router->get("/ass/listas/exportar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Signatures\Export::getContent($request));
    }
]);

// ADICIONANDO A ROTA DE EXPORTAÇÃO DE ASSINATURAS
$router->post("/ass/listas/exportar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Signatures\Export::setContent($request));
    }
]);

// ADICIONANDO A ROTA DE EDIÇÃO DE ASSINATURA
$router->get("/ass/listas/{list}/{id}/editar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $list, $id) {
        return new Response(200, Signatures\EditSignature::getEditSignature($list, $id));
    }
]);

// ADICIONANDO A ROTA DE EDIÇÃO DE ASSINATURA (POST)
$router->post("/ass/listas/{list}/{id}/editar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $list, $id)
    {
        return new Response(200, Signatures\EditSignature::setEditSignature($request, $list, $id));
    }
]);

// ADICIONANDO A ROTA DE CONSULTA DE ASSINATURA
$router->get("/ass/listas/{list}/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($list, $id)
    {
        return new Response(200, Signatures\Signature::getSignature($list, $id));
    }
]);

// ADICIONANDO A ROTA DE EXCLUSÃO DE ASSINATURA
$router->post("/ass/listas/{list}/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $list, $id)
    {
        return new Response(200, Signatures\DeleteSignature::deleteSignature($request, $list, $id));
    }
]);

?>