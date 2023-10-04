<?php

namespace App\Controller\Assistant\Students;

use App\Controller\Common\Alert;
use App\Controller\Assistant\Page;
use App\Model\Entity\Aluno;

class Disable extends Page
{
    /**
     * Retorna a view de desabilitar alunos
     * @param string $message
     * @param bool $success
     * @return string
     */
    public static function getView($message = null, $success = false)
    {
        parent::setActiveModule("students");

        $content = parent::render("students/disable/index", [
            "status" => is_null($message) ? null : "<br>".($success ? Alert::getSuccess($message) : Alert::getError($message))
        ]);

        return parent::getPage("Alunos", $content);
    }

    /**
     * Realiza a desabilitação de alunos
     * @param request $request
     * @return string
     */
    public static function setView($request)
    {
        $postVars = $request->getPostVars();

        $where = "ativo = true";


        foreach ($postVars as $key => $value)
        {
            if ($value != "null" && $key != "confirm")
            {
                $where .= " AND ".$key." = '".$value."'";
            }
        }

        if (isset($postVars['confirm']))
        {
            self::disableStudents($where);
            return self::getView("Alunos desativados com sucesso!", true);
        }

        $obStudents = Aluno::processData(Aluno::getAlunos($where));

        if (!empty($obStudents))
        {
            return self::getConfirm($obStudents, $request);
        }

        return self::getView("Nenhum aluno foi encontrado!");
    }

    /**
     * Retorna a view de confirmação
     * @param array $obStudents
     * @param Request $request
     * @return string
     */
    private static function getConfirm($obStudents, $request)
    {
        parent::setActiveModule("students");
        
        $content = parent::render("students/disable/confirm", self::getContent($obStudents, $request));

        return parent::getPage("Alunos", $content);
    }

    /**
     * Retorna o conteúdo da view de confirmação
     * @param array $obStudents
     * @param Request $request
     * @return array
     */
    private static function getContent($obStudents, $request)
    {
        $postVars = $request->getPostVars();

        $content = [
            "num-alunos" => count($obStudents),
            "quarto" => $postVars['quarto'],
            "serie" => $postVars['serie'],
            "sexo" => $postVars['sexo']
        ];

        $lines = "";

        foreach ($obStudents as $item)
        {
            $lines .= parent::render("students/disable/item", [
                "id_refeitorio" => $item->idRefeitorio,
                "nome" => $item->nome,
                "sexo" => ucfirst($item->sexo),
                "quarto" => str_split($item->quarto)[0].".".str_split($item->quarto)[1],
                "serie" => $item->serie."°"
            ]);
        }

        $content['lines'] = $lines;

        return $content;
    }

    /**
     * Desablita os alunos selecionados
     * @param string $where
     */
    private static function disableStudents($where)
    {
        $list = Aluno::processData(Aluno::getAlunos($where));

        foreach ($list as $item)
        {
            $item->senha = null;
            $item->ativo = false;
            $item->atualizar();
        }
    }
}