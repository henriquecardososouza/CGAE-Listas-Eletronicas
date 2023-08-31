<?php

use \App\Http\Response;
use \App\Controller\Assistant\Modules\Student;

// ADICIONANDO A ROTA DE CONSULTA DE ALUNOS
$router->get("/assistant/students", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Student\Students::getStudents($request));
    }
]);

// ADICIONANDO A ROTA DE CONSULTA DE ALUNOS (POST)
$router->post("/assistant/students", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Student\Students::getStudents($request));
    }
]);

// ADICIONANDO A ROTA DE CADASTRO DE ALUNOS
$router->get("/assistant/students/new", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Student\NewStudent::getNew());
    }
]);

// ADICIONANDO A ROTA DE CADASTRO DE ALUNOS (POST)
$router->post("/assistant/students/new", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Student\NewStudent::setNew($request));
    }
]);

// ADICIONANDO A ROTA DE ATUALIZAÇÃO GERAL DE ALUNOS
$router->get("/assistant/students/update/all", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Student\UpdateAll::getUpdate());
    }
]);

// ADICIONANDO A ROTA DE ATUALIZAÇÃO GERAL DE ALUNOS (POST)
$router->post("/assistant/students/update/all", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Student\UpdateAll::setUpdate($request));
    }
]);

// ADICIONANDO A ROTA DE ATUALIZAÇÃO DE ALUNO
$router->get("/assistant/student/update/{id}", [
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
$router->post("/assistant/student/update/{id}", [
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
$router->get("/assistant/student/delete/{id}", [
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
$router->post("/assistant/student/delete/{id}", [
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
$router->get("/assistant/student/{id}", [
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
$router->post("/assistant/student/{id}", [
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

// ADICIONANDO A ROTA DE DESABILITAÇÃO GERAL DE ALUNOS
$router->get("/assistant/students/disable/all", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ()
    {
        return new Response(200, Student\DisableAll::getDisable());
    }
]);

// ADICIONANDO A ROTA DE DESABILITAÇÃO GERAL DE ALUNOS (POST)
$router->post("/assistant/students/disable/all", [
    "middlewares" => [
        "recover-cookies",
        "require-assistant-login",
        "update-lists"
    ],

    function ($request)
    {
        return new Response(200, Student\DisableAll::setDisable($request));
    }
]);

?>