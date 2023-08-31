<?php

namespace App\Controller\Assistant;

use \App\Utils\View;

/**
 * Controlador da página principal (assistente)
 */
class Home extends Page
{
    /**
     * Retorna a view do painel de assistente 
     * @return string
     */
    public static function getHome()
    {
        // CONFIGURA A NAVBAR
        parent::configNavbar("home");

        // RENDERIZA A PÁGINA
        $content = View::render("assistant/home");

        return parent::getPage("Painel", $content, true);
    }
}

?>