<?php

use \App\Http\Response;
use \App\Controller\Admin\Modules\Students;

$router->get("/admin/students", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Students\Students::getStudents($request));
    }
]);

$router->post("/admin/students", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Students\Students::getStudents($request));
    }
]);

$router->get("/admin/students/new", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function () {
        return new Response(200, Students\NewStudent::getNew());
    }
]);

$router->post("/admin/students/new", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Students\NewStudent::setNew($request));
    }
]);

$router->get("/admin/students/update/all", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function () {
        return new Response(200, Students\UpdateAll::getUpdate());
    }
]);

$router->post("/admin/students/update/all", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Students\UpdateAll::setUpdate($request));
    }
]);

$router->get("/admin/student/update/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($id) {
        return new Response(200, Students\Update::getUpdate($id));
    }
]);

$router->post("/admin/student/update/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request, $id) {
        return new Response(200, Students\Update::setUpdate($request, $id));
    }
]);

$router->get("/admin/student/delete/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($id) {
        return new Response(200, Students\Delete::getDelete($id));
    }
]);

$router->post("/admin/student/delete/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request, $id) {
        return new Response(200, Students\Delete::setDelete($request, $id));
    }
]);

$router->get("/admin/student/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($id) {
        return new Response(200, Students\Student::getStudent($id));
    }
]);

$router->post("/admin/student/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request, $id) {
        return new Response(200, Students\Student::setStudent($request, $id));
    }
]);

$router->get("/admin/students/disable/all", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function () {
        return new Response(200, Students\DisableAll::getDisable());
    }
]);

$router->post("/admin/students/disable/all", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Students\DisableAll::setDisable($request));
    }
]);

?>