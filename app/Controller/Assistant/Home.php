<?php

namespace App\Controller\Assistant;

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
        parent::setActiveModule("home");

        // RENDERIZA A PÁGINA
        $content = parent::render("home/index");

        return parent::getPage("Painel", $content, true);
    }
}

?>