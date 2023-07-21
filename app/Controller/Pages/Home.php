<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class Home extends Page
{
    /**
     * Retorna o conteúdo (view) da Home
     * @return string
     */
    public static function getHome()
    {
        $content = View::render("pages/home", [

        ]);
        
        return parent::getPage("Home", $content, true, true);
    }
}

?>