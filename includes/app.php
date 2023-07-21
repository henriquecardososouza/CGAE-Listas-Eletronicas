<?php

require __DIR__."/../vendor/autoload.php";

use \App\Utils\View;
use \App\Utils\Environment;
use \App\Utils\Database\Database;
use \App\Http\Middlewares\Queue;

Environment::load(__DIR__."/../");
define("URL", getenv("URL"));

Database::config(
    getenv("DB_HOST"),
    getenv("DB_NAME"),
    getenv("DB_USER"),
    getenv("DB_PASS"),
    getenv("DB_PORT")
);

View::init([
    "URL" => URL,
    "SERVER_URI" => getenv("SERVER_URI")
]);

Queue::setMap([
    "maintenance" => \App\Http\Middlewares\Maintenance::class,
    "update-lists" => \App\Http\Middlewares\UpdateLists::class,
    "recover-cookies" => \App\Http\Middlewares\RecoverCookies::class,
    "require-login" => \App\Http\Middlewares\RequireLogin::class,
    "without-login" => \App\Http\Middlewares\WithoutLogin::class,
    "require-user-login" => \App\Http\Middlewares\RequireUserLogin::class,
    "require-admin-login" => \App\Http\Middlewares\RequireAdminLogin::class
]); 

Queue::setDefault([
    "maintenance",
    "update-lists"
]); 