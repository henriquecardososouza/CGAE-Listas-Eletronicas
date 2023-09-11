<?php

use \App\Http\Response;
use \App\Controller\Student;

// ADICIONANDO A ROTA DE MINHAS ASSINATURAS ENCERRADAS
$router->get("/assinaturas/finalizadas", [
    "middlewares" => [
        "recover-cookies",
        "require-student-login",
        "update-signatures"
    ],

    function () {
        return new Response(200, Student\Signatures\Signatures::getContent("finalizadas"));
    }
]);

// ADICIONANDO A ROTA DE MINHAS ASSINATURAS ATIVAS
$router->get("/assinaturas/ativas", [
    "middlewares" => [
        "recover-cookies",
        "require-student-login",
        "update-signatures"
    ],

    function () {
        return new Response(200, Student\Signatures\Signatures::getContent("ativas"));
    }
]);

// ADICIONANDO A ROTA DE CONSULTA DE ASSINATURA
$router->get("/assinaturas/{list}/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-student-login",
        "update-signatures"
    ],

    function ($list, $id) {
        return new Response(200, Student\Signatures\Signature::getSignature($list, $id));
    }
]);

// ADICIONANDO A ROTA DE CONSULTA DE ASSINATURA (POST)
$router->post("/assinaturas/{list}/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-student-login",
        "update-signatures"
    ],

    function ($request, $list, $id) {
        return new Response(200, Student\Signatures\Signature::getSignature($request, $list, $id));
    }
]);

?>