<?php

use \App\Http\Response;
use \App\Controller\Admin\Modules\Signatures;

$router->get("/admin/signatures", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Signatures\Lists::getLists($request));
    }
]);

$router->post("/admin/signatures", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Signatures\Lists::setLists($request));
    }
]);

$router->get("/admin/signatures/new", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function () {
        return new Response(200, Signatures\NewSignature::getNewSignature());
    }
]);

$router->post("/admin/signatures/new", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Signatures\NewSignature::setNewSignature($request));
    }
]);

$router->get("/admin/signatures/{list}/{id}/edit", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request, $list, $id) {
        return new Response(200, Signatures\Signature::getEditSignature($request, $list, $id));
    }
]);

$router->post("/admin/signatures/{list}/{id}/edit", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request, $list, $id) {
        return new Response(200, Signatures\Signature::getEditSignature($request, $list, $id));
    }
]);

$router->get("/admin/signatures/{list}/{id}/delete", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function () {
        return new Response(200, Signatures\Signature::getDeleteSignature());
    }
]);

$router->post("/admin/signatures/{list}/{id}/delete", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request, $list, $id) {
        return new Response(200, Signatures\Signature::setDeleteSignature($request, $list, $id));
    }
]);

$router->get("/admin/signatures/{list}/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request, $list, $id) {
        return new Response(200, Signatures\Signature::getSignature($request, $list, $id));
    }
]);

?>