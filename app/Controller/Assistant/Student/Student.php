<?php

namespace App\Controller\Admin\Modules\Students;

use App\Controller\Admin\Page;
use App\Model\Entity\Student as EntityStudent;
use App\Utils\View;

class Student extends Page
{
    /**
     * Retorna a view da página de aluno
     * @param int $id
     * @return string
     */
    public static function getStudent($id)
    {
        parent::configNavbar("students");

        $content = self::getContent($id);

        return parent::getPage("Aluno", $content);
    }

    /**
     * Configura a view da página de estudante
     * @param int $id
     * @return string
     */
    public static function setStudent($request, $id)
    {
        $postVars = $request->getPostVars();
        $ob = EntityStudent::getStudentById($id);

        if (!$ob instanceof EntityStudent)
        {
            return self::getStudent($id);
        }

        if (isset($postVars['acao']))
        {
            if ($postVars['acao'] == "ativar")
            {
                $senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
                $ob->senha = $senha;
                $ob->ativo = true;
            }

            else if ($postVars['acao'] == "desativar")
            {
                $ob->ativo = false;
                $ob->senha = NULL;
            }

            $ob->atualizar();
        }

        return self::getStudent($id);
    }

    /**
     * Retorna os dados do aluno
     * @param int $id
     * @return string
     */
    private static function getContent($id)
    {
        $content = null;
        $ob = EntityStudent::getStudentById($id);

        if (is_null($ob))
        {
            $content = View::render("admin/modules/students/student/not_found");
        }

        else
        {
            $content = View::render("admin/modules/students/student/index", [
                "nome" => $ob->nome,
                "email" => $ob->email,
                "refeitorio" => $ob->idRefeitorio,
                "quarto" => str_split($ob->quarto, 1)[0]."-".str_split($ob->quarto, 1)[1],
                "serie" => $ob->serie."°",
                "sexo" => ucfirst($ob->sexo),
                "pernoite" => $ob->pernoite ? "Sim" : "Não",
                "cidade" => $ob->cidade,
                "nome_responsavel" => $ob->nomeResponsavel,
                "telefone" => $ob->telefoneResponsavel,
                "actions" => self::getActions($id)
            ]);
        }

        return $content;
    }

    /**
     * Retorna os botões de ação
     * @param int $id
     * @return string
     */
    private static function getActions($id)
    {
        $content = "";
        $ob = EntityStudent::getStudentById($id);

        if (!is_null($ob))
        {
            if ($ob->ativo)
            {
                $content .= View::render("admin/modules/students/student/actions/desativar");
            }

            else
            {
                $content .= View::render("admin/modules/students/student/actions/ativar");
            }
            
            $content .= View::render("admin/modules/students/student/actions/excluir", [
                "id" => $ob->id
            ]);

            $content .= View::render("admin/modules/students/student/actions/atualizar", [
                "id" => $ob->id
            ]);
        }

        return $content;
    }
}