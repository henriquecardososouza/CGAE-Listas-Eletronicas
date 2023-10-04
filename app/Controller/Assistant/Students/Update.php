<?php

namespace App\Controller\Assistant\Students;

use App\Controller\Assistant\Page;
use App\Model\Entity\Aluno;

class Update extends Page
{
    /**
     * Retorna a view da página de confirmação de atualização de todos os alunos
     * @return string
     */
    public static function getView()
    {
        parent::setActiveModule("students");
        
        $content = parent::render("students/update/index");

        return parent::getPage("Alunos", $content);
    }

    /**
     * Atualiza os alunos e realiza o redirecionamento adequado
     * @param Request $request
     */
    public static function setView($request)
    {
        $ob = Aluno::processData(Aluno::getAlunos("ativo = true"));

        foreach ($ob as $item)
        {
            if ($item->serie == 3)
            {
                $item->ativo = false;
                $item->senha = null;
            }

            else
            {
                $item->serie ++;
            }

            $item->atualizar();
        }

        $request->getRouter()->redirect("/ass/alunos?status=success");
    }
}