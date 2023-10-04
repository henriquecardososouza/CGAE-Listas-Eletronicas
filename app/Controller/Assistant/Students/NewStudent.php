<?php

namespace App\Controller\Assistant\Students;

use App\Controller\Common\Alert;
use App\Controller\Assistant\Page;
use App\Model\Entity\Aluno;

class NewStudent extends Page
{
    /**
     * 
     * @return string
     */
    public static function getView($message = null, $success = false)
    {
        parent::setActiveModule("students");

        $content = parent::render("students/new/index", [
            "status" => self::getStatus($message, $success)
        ]);

        return parent::getPage("Alunos", $content);
    }

    /**
     * 
     * @param Request $request
     * @return string
     */
    public static function setView($request)
    {
        $postVars = $request->getPostVars();
        
        $ob = new Aluno(-1, $postVars['nome'], $postVars['sexo'], $postVars['email'], $postVars['quarto'], $postVars['serie'], $postVars["refeitorio"], null, (isset($postVars['pernoite']) ? true : false), $postVars['nome_responsavel'], $postVars['cidade'], $postVars['telefone']);
        $ob->cadastrar();
    
        return self::getView("Aluno cadastrado com sucesso!", true);
    }

    /**
     * Retorna as mensagens de status
     * @param string $message
     * @param bool $success
     * @return string|null
     */
    private static function getStatus($message, $success)
    {
        $content = null;

        if (!is_null($message))
        {
            if ($success)
            {
                $content = Alert::getSuccess($message);
            }

            else
            {
                $content = Alert::getError($message);
            }
        }

        return $content;
    }
}