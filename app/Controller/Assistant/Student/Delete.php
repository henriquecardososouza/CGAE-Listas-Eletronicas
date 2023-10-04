<?php

namespace App\Controller\Assistant\Student;

use App\Controller\Assistant\Page;
use App\Model\Entity\Aluno;

class Delete extends Page
{
    /**
     * Retorna a view de exclusão de aluno
     * @param int $id
     * @return string
     */
    public static function getDelete($id)
    {
        parent::setActiveModule("students");

        $ob = Aluno::getAlunoById($id);

        $content = parent::render("student/delete/index", [
            "id" => $ob->id,
            "nome" => $ob->nome
        ]);

        return parent::getPage("Alunos", $content);
    }

    /**
     * Realiza a exclusão de um aluno
     * @param Request $request
     * @param int $id
     * @return string
     */
    public static function setDelete($request, $id)
    {
        $ob = Aluno::getAlunoById($id);
        $ob->excluir();

        $request->getRouter()->redirect("/ass/alunos");
    }
}

?>