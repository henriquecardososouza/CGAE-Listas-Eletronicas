<?php

namespace App\Controller\Admin\Modules\Students;

use App\Controller\Admin\Page;
use App\Model\Entity\Student as EntityStudent;
use App\Utils\View;

class Delete extends Page
{
    /**
     * Retorna a view de exclusão de aluno
     * @param int $id
     * @return string
     */
    public static function getDelete($id)
    {
        parent::configNavbar("students");

        $ob = EntityStudent::getStudentById($id);

        $content = View::render("admin/modules/students/delete/index", [
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
        $ob = EntityStudent::getStudentById($id);
        $ob->excluir();

        $request->getRouter()->redirect("/admin/students");
    }
}

?>