<?php

namespace App\Controller\Assistant\Students;

use App\Controller\Assistant\Page;

class Students extends Page
{
    /**
     * Retorna a view da página de estudantes cadastrados
     * @return string
     */
    public static function getStudents()
    {
        parent::setActiveModule("students");

        $content = parent::render("students/index", [
            
        ]);

        return parent::getPage("Alunos", $content);
    }
}