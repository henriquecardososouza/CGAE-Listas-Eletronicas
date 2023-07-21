<?php

namespace App\Controller\Admin\Modules\Students;

use App\Controller\Admin\Alert;
use App\Controller\Admin\Page;
use App\Model\Entity\Student;
use App\Utils\View;

class DisableAll extends Page
{
    /**
     * Retorna a view de desabilitar alunos
     * @param string $message
     * @param bool $success
     * @return string
     */
    public static function getDisable($message = null, $success = false)
    {
        parent::configNavbar("students");

        $content = View::render("admin/modules/students/all/disable/index", [
            "status" => is_null($message) ? null : ($success ? "<br>".Alert::getSuccess($message) : "<br>".Alert::getError($message))
        ]);

        return parent::getPage("Alunos", $content);
    }

    /**
     * Realiza a desabilitação de alunos
     * @param request $request
     * @return string
     */
    public static function setDisable($request)
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
            return self::getDisable("Alunos desativados com sucesso!", true);
        }

        $obStudents = Student::processData(Student::getStudents($where));

        if (!empty($obStudents))
        {
            return self::getConfirm($obStudents, $request);
        }

        return self::getDisable("Nenhum aluno foi encontrado!");
    }

    /**
     * Retorna a view de confirmação
     * @param array $obStudents
     * @param Request $request
     * @return string
     */
    private static function getConfirm($obStudents, $request)
    {
        parent::configNavbar("students");
        
        $content = View::render("admin/modules/students/all/disable/confirm", self::getContent($obStudents, $request));

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
            $lines .= View::render("admin/modules/students/all/disable/item", [
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
        $list = Student::processData(Student::getStudents($where));

        foreach ($list as $item)
        {
            $item->senha = null;
            $item->ativo = false;
            $item->atualizar();
        }
    }
}