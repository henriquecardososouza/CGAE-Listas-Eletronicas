<?php

namespace App\Controller\Student;

use \App\Utils\View;

/**
 * Controlador da página principal (aluno)
 */
class Home extends Page
{
    /**
     * Retorna o conteúdo (view) da Home
     * @return string View renderizada
     */
    public static function getHome()
    {
        // CONFIGURA A NAVBAR
        parent::setActiveModule("home");

        // RENDERIZA A VIEW DA PÁGINA
        $content = View::render("student/home");
        
        return parent::getPage("Home", $content);
    }
}

?>