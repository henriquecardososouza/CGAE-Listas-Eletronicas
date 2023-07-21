<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Home extends Page
{
    /**
     * Retorna a view do painel de administração 
     * @return string
     */
    public static function getHome()
    {
        parent::configNavbar("home");

        $content = View::render("admin/home");

        return parent::getPage("Painel", $content, true, true);
    }
}

?>