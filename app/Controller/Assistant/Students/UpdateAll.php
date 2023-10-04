<?php

namespace App\Controller\Admin\Modules\Students;

use App\Controller\Admin\Page;
use App\Model\Entity\Student;
use App\Utils\View;

class UpdateAll extends Page
{
    /**
     * Retorna a view da página de confirmação de atualização de todos os alunos
     * @return string
     */
    public static function getUpdate()
    {
        parent::configNavbar("students");
        
        $content = View::render("admin/modules/students/all/update/index");

        return parent::getPage("Alunos", $content);
    }

    /**
     * Atualiza os alunos e realiza o redirecionamento adequado
     * @param Request $request
     */
    public static function setUpdate($request)
    {
        $ob = Student::processData(Student::getStudents("ativo = true"));

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

        $request->getRouter()->redirect("/admin/students?status=success");
    }
}