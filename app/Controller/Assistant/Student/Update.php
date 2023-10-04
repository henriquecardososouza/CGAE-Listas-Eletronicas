<?php

namespace App\Controller\Assistant\Student;

use App\Controller\Common\Alert;
use App\Controller\Assistant\Page;
use App\Model\Entity\Aluno;

class Update extends Page
{
    /**
     * Retorna a view do formulário de atualização de aluno
     * @param int $id
     * @return string
     */
    public static function getUpdate($id, $message = null, $success = false)
    {
        parent::setActiveModule("students");

        $content = parent::render("student/update/index", self::getForm($id, $message, $success));

        return parent::getPage("Alunos", $content);
    }

    /**
     * Configura a view de atualização de aluno
     * @param Request $request
     * @param int $id
     * @return string
     */
    public static function setUpdate($request, $id)
    {
        $postVars = $request->getPostVars();

        $ob = Aluno::getAlunoById($id);

        $ob1 = Aluno::processData(Aluno::getAlunos("ativo = true AND id_refeitorio = ".$postVars['refeitorio']." AND id != ".$ob->id));
        $ob2 = Aluno::processData(Aluno::getAlunos("ativo = true AND email = '".$postVars['email']."' AND id != ".$ob->id));

        if (!is_null($ob1))
        {
            return self::getUpdate($id, "O número do Refeitório informado já<br>está sendo utilizado por outro aluno!", false);
        }
        
        if (!is_null($ob2))
        {
            return self::getUpdate($id, "O email informado já está<br>sendo utilizado por outro aluno!", false);
        }

        $senha = empty($postVars['senha']) ? $ob->senha : password_hash($postVars['senha'], PASSWORD_DEFAULT);

        $ob->nome = $postVars['nome'];
        $ob->email = $postVars['email'];
        $ob->senha = $senha;
        $ob->nomeResponsavel = $postVars['nome_responsavel'];
        $ob->telefoneResponsavel = $postVars['telefone'];
        $ob->sexo = $postVars['sexo'];
        $ob->pernoite = isset($postVars['pernoite']);
        $ob->cidade = $postVars['cidade'];
        $ob->quarto = $postVars['quarto'];
        $ob->serie = $postVars['serie'];
        $ob->idRefeitorio = $postVars['refeitorio'];

        $ob->atualizar();

        return self::getUpdate($id, "Atualizado com sucesso!", true);
    }

    /**
     * Retorna as variáveis da view de update
     * @param int $id
     * @return array
     */
    private static function getForm($id, $message, $success)
    {
        $ob = Aluno::getAlunoById($id);

        $content = [
            "id" => $ob->id,
            "nome" => $ob->nome,
            "email" => $ob->email,
            "responsavel" => $ob->nomeResponsavel,
            "telefone" => $ob->telefoneResponsavel,
            "cidade" => $ob->cidade,
            "id_refeitorio" => $ob->idRefeitorio,
            "checked-00" => $ob->pernoite ? "checked" : ""
        ];

        $index = [
            "quarto" => 0,
            "serie" => 0
        ];

        $index['quarto'] = round(((floor($ob->quarto / 10) - 1) * 4) + ((1 - (ceil($ob->quarto / 10) - $ob->quarto / 10)) * 10 - 1) + 1);
        $index['serie'] = round((int)$ob->serie) - 1;

        for ($i = 0; $i < 12; $i++)
        {
            if ($i == $index['quarto'])
            {
                $content['selected-'.$i] = "selected";
                continue;
            }

            $content['selected-'.$i] = "";
        }
        
        for ($i = 0; $i < 3; $i++)
        {
            if ($i == $index['serie'])
            {
                $content['selected-0'.$i] = "selected";
                continue;
            }

            $content['selected-0'.$i] = "";
        }

        if ($ob->sexo == "masculino")
        {
            $content['checked-0'] = "checked";
            $content['checked-1'] = "";
        }

        else
        {
            $content['checked-0'] = "";
            $content['checked-1'] = "checked";
        }

        $content['status'] = self::getStatus($message, $success);

        return $content;
    }

    private static function getStatus($message, $success)
    {
        if (is_null($message))
        {
            return null;
        }

        if ($success)
        {
            return Alert::getSuccess($message);
        }

        else
        {
            return Alert::getError($message);
        }
    }
}

?>