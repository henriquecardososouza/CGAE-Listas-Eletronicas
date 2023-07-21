<?php

use \App\Http\Response;
use \App\Controller\Admin;

$router->get("/admin/solicitations/open", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Admin\Modules\Solicitation\Open::getOpenSolicitations($request));
    }
]);

$router->post("/admin/solicitations/open", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Admin\Modules\Solicitation\Open::getOpenSolicitations($request));
    }
]);

$router->get("/admin/solicitations/closed", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Admin\Modules\Solicitation\Closed::getClosedSolicitations($request));
    }
]);

$router->post("/admin/solicitations/closed", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request) {
        return new Response(200, Admin\Modules\Solicitation\Closed::getClosedSolicitations($request));
    }
]);

$router->get("/admin/solicitations/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request, $id) {
        return new Response(200, Admin\Modules\Solicitation\Solicitation::getSolicitation($request, $id));
    }
]);

$router->post("/admin/solicitations/{id}", [
    "middlewares" => [
        "recover-cookies",
        "require-admin-login"
    ],

    function ($request, $id) {
        return new Response(200, Admin\Modules\Solicitation\Solicitation::getSolicitation($request, $id));
    }
]);

?>