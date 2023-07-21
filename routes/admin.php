<?php

use \App\Http\Response;
use \App\Controller\Admin;

include __DIR__."/modules/signature.php";
include __DIR__."/modules/solicitation.php";
include __DIR__."/modules/student.php";

$router->get("/admin", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function () {
        return new Response(200, Admin\Home::getHome());
    }
]);

$router->get("/admin/profile", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function () {
        return new Response(200, Admin\Profile::getProfile());
    }
]);

$router->get("/admin/edit", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Admin\Edit::getEdit($request));
    }
]);

$router->post("/admin/edit", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Admin\Edit::setEdit($request));
    }
]);

?>