<?php

use \App\Http\Response;
use \App\Controller\Pages;

$router->get("", [
    "middlewares" => [
        "recover-cookies",
        "require-user-login"
    ],

    function () {
        return new Response(200, Pages\Home::getHome());
    }
]);

$router->get("/login", [
    "middlewares" => [
        "without-login"
    ],

    function ($request) {
        return new Response(200, Pages\Login::getLogin($request));
    }
]);

$router->post("/login", [
    "middlewares" => [
        "without-login"
    ],

    function ($request) {
        return new Response(200, Pages\Login::setLogin($request));
    }
]);

$router->get("/logout", [
    "middlewares" => [
        "require-login"
    ],

    function ($request) {
        return new Response(200, Pages\Login::setLogout($request));
    }
]);

$router->get("/profile", [
    "middlewares" => [
        "require-user-login"
    ],

    function () {
        return new Response(200, Pages\Profile::getProfile());
    }
]);

$router->get("/mylists/encerred", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Minhas_Listas\Encerred::getEncerred($request));
    }
]);

$router->post("/mylists/encerred", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Minhas_Listas\Encerred::setEncerred($request));
    }
]);

$router->get("/mylists/encerred/{list}/{id}", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request, $list, $id) {
        return new Response(200, Pages\Minhas_Listas\MyList::getList($request, $list, $id));
    }
]);

$router->get("/mylists/active", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Minhas_Listas\Actives::getActives($request));
    }
]);

$router->post("/mylists/active", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Minhas_Listas\Actives::setActives($request));
    }
]);

$router->get("/mylists/active/{list}/{id}", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request, $list, $id) {
        return new Response(200, Pages\Minhas_Listas\MyList::getList($request, $list, $id));
    }
]);

$router->post("/mylists/active/{list}/{id}", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request, $list, $id) {
        return new Response(200, Pages\Minhas_Listas\MyList::getList($request, $list, $id));
    }
]);

$router->post("/mylists/active/{list}/{id}", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request, $list, $id) {
        return new Response(200, Pages\Minhas_Listas\MyList::getList($request, $list, $id));
    }
]);

$router->get("/solicitations/open", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Solicitation\Open::getSolicitation($request));
    }
]);

$router->post("/solicitations/open", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Solicitation\Open::setSolicitation($request));
    }
]);

$router->get("/solicitations/item/{id}", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($id) {
        return new Response(200, Pages\Solicitation\MySolicitation::getSolicitation($id));
    }
]);

$router->get("/solicitations/finished", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Solicitation\Finished::getSolicitation($request));
    }
]);

$router->post("/solicitations/finished", [
    "middlewares" => [
        "require-user-login"
    ],

    function ($request) {
        return new Response(200, Pages\Solicitation\Finished::setSolicitation($request));
    }
]);


?>