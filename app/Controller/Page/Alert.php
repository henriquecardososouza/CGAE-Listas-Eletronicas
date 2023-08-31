<?php

namespace App\Controller\Page;

use \App\Utils\View;

class Alert
{
    /**
     * Retorna uma mensagem de sucesso
     * @param string $message Texto da mensagem
     * @return string View renderizada
     */
    public static function getSuccess($message)
    {
        return View::render("pages/alert/status", [
            'tipo' => "success",
            "mensagem" => $message
        ]);
    }

    /**
     * Retorna uma mensagem de erro
     * @param string $message Texto da mensagem
     * @return string View renderizada
     */
    public static function getError($message)
    {
        return View::render("pages/alert/status", [
            'tipo' => "danger",
            "mensagem" => $message
        ]);
    }
}

?>