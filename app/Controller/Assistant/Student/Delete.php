<?php

namespace App\Controller\Assistant\Student;

use App\Controller\Assistant\Page;
use App\Model\Entity\Aluno;

class Delete extends Page
{
    /**
     * Realiza a exclusão de um aluno
     * @param Request $request
     * @param int $id
     * @return string
     */
    public static function setDelete($request, $id)
    {
        if ($request->getPostVars()['acao'] != "excluir") throw new \Exception("credentials missing", 500);

        $ob = Aluno::getAlunoById($id);
        $ob->excluir();

        $request->getRouter()->redirect("/ass/alunos");
    }
}

?>