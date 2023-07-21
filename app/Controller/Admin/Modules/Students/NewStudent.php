<?php

namespace App\Controller\Admin\Modules\Students;

use App\Controller\Admin\Alert;
use App\Controller\Admin\Page;
use App\Model\Entity\Student;
use App\Utils\View;

class NewStudent extends Page
{
    /**
     * 
     * @return string
     */
    public static function getNew($message = null, $success = false)
    {
        parent::configNavbar("students");

        $content = View::render("admin/modules/students/new/index", [
            "status" => self::getStatus($message, $success)
        ]);

        return parent::getPage("Alunos", $content);
    }

    /**
     * 
     * @param Request $request
     * @return string
     */
    public static function setNew($request)
    {
        $postVars = $request->getPostVars();
        
        $ob = new Student(-1, $postVars['nome'], $postVars['sexo'], $postVars['email'], $postVars['quarto'], $postVars['serie'], $postVars["refeitorio"], null, (isset($postVars['pernoite']) ? true : false), $postVars['nome_responsavel'], $postVars['cidade'], $postVars['telefone']);
        $ob->cadastrar();
    
        return self::getNew("Aluno Cadastrado com Sucesso!", true);
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