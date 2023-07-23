<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Profile extends Page
{
    /**
     * Retorna a view da página de perfil de admin
     * @return string
     */
    public static function getProfile()
    {
        parent::configNavbar("profile");
        
        \App\Session\Login::init();

        $content = View::render("admin/profile", [
            "nome" => $_SESSION['user']['usuario']['nome'],
            "email" => $_SESSION['user']['usuario']['email']
        ]);

        return parent::getPage("Perfil", $content);
    }
}