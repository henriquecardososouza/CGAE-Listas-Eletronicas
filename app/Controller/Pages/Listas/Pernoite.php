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
     * @param bool $success
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
            return self::getPernoite("O Aluno não possui permissão para assinar a lista de pernoite!");
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

        $hourInitial = getenv("HOUR_INITIAL");
        $hourFinal = getenv("HOUR_FINAL");

        if (!($hourInitial < $horaAtual && $horaAtual < $hourFinal))
        {
            return self::getPernoite("O horário para cadastro de assinaturas já se encerrou!<br>Contate um assistente para realizar sua assinatura");
        }

        if (!("07:00:00" < $horaSaida && $horaSaida < "23:00:00"))
        {
            return self::getPernoite("O horário de saída informado não é válido!");
        }

        if ($dataAtual == $dataSaida)
        {
            if ($horaAtual > $horaSaida)
            {
                return self::getPernoite("O horário de saída informado não é válido!");
            }
        }

        if (!("07:00:00" < $horaChegada && $horaChegada < "23:00:00"))
        {
            return self::getPernoite("O horário de chegada informado não é válido!");
        }

        if ($horaSaida >= $horaChegada && $dataSaida == $dataChegada)
        {
            return self::getPernoite("O horário de chegada informado não é válido!");
        }

        if ($dataAtual > $dataSaida)
        {
            return self::getPernoite("A data de saída informada não é válida!");
        }

        if ($dataSaida > $dataChegada)
        {
            return self::getPernoite("A data de chegada informada não é válida!");
        }

        $ob = EntityPernoite::getListByStudent($_SESSION['user']['usuario']['id']);

        if (!empty($ob))
        {
            foreach ($ob as $item)
            {
                if ($item->ativa)
                {
                    return self::getPernoite("O aluno já possui uma assinatura ativa nesta lista!");
                }
            }
        }

        $obList = new EntityPernoite(0, $_SESSION['user']['usuario']['id'], true, $endereco, $nomeResponsavel, $telefone, $dataSaida, $dataChegada, $horaSaida, $horaChegada);

        $obList->cadastrar();

        return self::getPernoite("Assinatura registrada!", true);
    }
}

?>