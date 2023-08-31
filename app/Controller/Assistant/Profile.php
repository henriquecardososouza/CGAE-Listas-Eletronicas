<?php

namespace App\Controller\Assistant;

use App\Utils\View;

/**
 * Controlador da página de perfil (assistente)
 */
class Profile extends Page
{
    /**
     * Retorna a view da página de perfil de assistente
     * @return string
     */
    public static function getProfile()
    {
        // CONFIGURA A NAVBAR
        parent::configNavbar("profile");
        
        // INICIALIZA A SESSÃO
        \App\Session\Login::init();

        // RENDERIZA A VIEW
        $content = View::render("assistant/profile", [
            "nome" => $_SESSION['user']['usuario']['nome'],
            "email" => $_SESSION['user']['usuario']['email']
        ]);

        return parent::getPage("Perfil", $content);
    }
}