<?php

namespace App\Controller\Pages\Listas;

use App\Controller\Pages\Alert;
use App\Utils\View;
use App\Controller\Pages\Page;
use App\Model\Entity\Listas\Pernoite as EntityPernoite;

class Pernoite extends Page
{
    /**
     * Retorna a view da lista de pernoite
     * @param string $message
     * @return string
     */
    public static function getPernoite($message = null, $success = false)
    {
        $content = View::render("pages/listas/pernoite", [
            "status" => !is_null($message) ? (!$success ? Alert::getError($message) : Alert::getSuccess($message)) : ""
        ]);
        return parent::getPage("Listas | Pernoite", $content);
    }

    /**
     * Cadastra a assinatura
     * @param Request $request
     * @return string
     */
    public static function setPernoite($request)
    {
        \App\Session\Login::init();

        $ob = \App\Model\Entity\Student::getStudentById($_SESSION['user']['usuario']['id']);

        if (!$ob->pernoite)
        {
            return self::getPernoite("O Aluno não Possui Permissão para Assinar a Lista de Pernoite!");
        }

        $postVars = $request->getPostVars();

        $endereco = $postVars['endereco'];
        $nomeResponsavel = $postVars['nome_responsavel'];
        $telefone = trim($postVars['telefone']);
        $dataSaida = $postVars['data_saida'];
        $dataChegada = $postVars['data_chegada'];
        $horaSaida = $postVars['hora_saida'].":00";
        $horaChegada = $postVars['hora_chegada'].":00";

        date_default_timezone_set("America/Sao_Paulo");
        $dataAtual = date("Y-m-d", time());
        $horaAtual = date("H:i:s", time() + 60);

        if ($dataSaida > $dataChegada)
        {
            return self::getPernoite("A Data de Chegada não é Válida!");
        }

        if ($horaSaida >= $horaChegada && $dataSaida == $dataChegada)
        {
            return self::getPernoite("O Horário de Chegada não é Válido!");
        }

        if ($dataAtual > $dataSaida)
        {
            return self::getPernoite("A Data de Saída Informada não é Válida!");
        }

        else if ($dataAtual == $dataSaida)
        {
            if ($horaAtual > $horaSaida)
            {
                return self::getPernoite("O Horário de Saída não é Válido!");
            }
        }

        $ob = EntityPernoite::getListByStudent($_SESSION['user']['usuario']['id']);

        if (!empty($ob))
        {
            foreach ($ob as $item)
            {
                if ($item->ativa)
                {
                    return self::getPernoite("O Aluno já possui uma Assinatura Ativa nessa Lista!");
                }
            }
        }

        $obList = new EntityPernoite(0, $_SESSION['user']['usuario']['id'], true, $endereco, $nomeResponsavel, $telefone, $dataSaida, $dataChegada, $horaSaida, $horaChegada);

        $obList->cadastrar();

        return self::getPernoite("Assinatura Registrada!", true);
    }
}

?>