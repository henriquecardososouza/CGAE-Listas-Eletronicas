<?php

use \App\Http\Response;
use \App\Controller\Assistant\Students;
use \App\Controller\Assistant\Student;

// ADICIONANDO A ROTA DE CONSULTA DE ALUNOS
$router->get("/ass/alunos", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Students\Students::getStudents($request));
    }
]);

// ADICIONANDO A ROTA DE CONSULTA DE ALUNOS (POST)
$router->post("/ass/alunos", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Students\Students::getStudents($request));
    }
]);

// ADICIONANDO A ROTA DE CADASTRO DE ALUNOS
$router->get("/ass/alunos/cadastrar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Students\NewStudent::getView());
    }
]);

// ADICIONANDO A ROTA DE CADASTRO DE ALUNOS (POST)
$router->post("/ass/alunos/cadastrar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Students\NewStudent::setView($request));
    }
]);

// ADICIONANDO A ROTA DE ATUALIZAÇÃO GERAL DE ALUNOS
$router->get("/ass/alunos/atualizar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Students\Update::getView());
    }
]);

// ADICIONANDO A ROTA DE ATUALIZAÇÃO GERAL DE ALUNOS (POST)
$router->post("/ass/alunos/atualizar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Students\Update::setView($request));
    }
]);

// ADICIONANDO A ROTA DE DESABILITAÇÃO GERAL DE ALUNOS
$router->get("/ass/alunos/desativar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Students\Disable::getView());
    }
]);

// ADICIONANDO A ROTA DE DESABILITAÇÃO GERAL DE ALUNOS (POST)
$router->post("/ass/alunos/desativar", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Students\Disable::setView($request));
    }
]);

// ADICIONANDO A ROTA DE ATUALIZAÇÃO DE ALUNO
$router->get("/ass/alunos/atualizar/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($id)
    {
        return new Response(200, Student\Update::getUpdate($id));
    }
]);

// ADICIONANDO A ROTA DE ATUALIZAÇÃO DE ALUNO (POST)
$router->post("/ass/alunos/atualizar/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $id)
    {
        return new Response(200, Student\Update::setUpdate($request, $id));
    }
]);

// ADICIONANDO A ROTA DE EXCLUSÃO DE ALUNO
$router->get("/ass/alunos/excluir/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($id)
    {
        return new Response(200, Student\Delete::getDelete($id));
    }
]);

// ADICIONANDO A ROTA DE EXCLUSÃO DE ALUNO (POST)
$router->post("/ass/alunos/excluir/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $id)
    {
        return new Response(200, Student\Delete::setDelete($request, $id));
    }
]);

// ADICIONANDO A ROTA DE CONSULTA DE ALUNO
$router->get("/ass/alunos/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($id)
    {
        return new Response(200, Student\Student::getStudent($id));
    }
]);

// ADICIONANDO A ROTA DE CONSULTA DE ALUNO (POST)
$router->post("/ass/alunos/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request, $id)
    {
        return new Response(200, Student\Student::setStudent($request, $id));
    }
]);

?>