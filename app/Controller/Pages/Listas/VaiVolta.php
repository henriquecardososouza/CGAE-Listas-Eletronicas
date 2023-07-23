<?php

namespace App\Controller\Pages\Listas;

use App\Controller\Pages\Alert;
use App\Utils\View;
use App\Controller\Pages\Page;
use App\Model\Entity\Listas\VaiVolta as EntityVaiVolta;

class VaiVolta extends Page
{
    /**
     * Retorna a view da lista vai e volta
     * @param string $message
     * @param bool $success
     * @return string
     */
    public static function getVaiVolta($message = null, $success = false)
    {
        $content = View::render("pages/listas/vai_volta", [
            "status" => !is_null($message) ? (!$success ? Alert::getError($message) : Alert::getSuccess($message)) : ""
        ]);
        return parent::getPage("Listas | Vai e Volta", $content);
    }

    /**
     * Cadastra a assinatura
     * @param Request $request
     * @return string
     */
    public static function setVaiVolta($request)
    {
        $postVars = $request->getPostVars();

        $destino = $postVars['destino'];
        $data = $postVars['data'];
        $horaSaida = $postVars['hora_saida'].":00";
        $horaChegada = $postVars['hora_chegada'].":00";

        date_default_timezone_set("America/Sao_Paulo");
        $dataAtual = date("Y-m-d", time());
        $horaAtual = date("H:i:s", time() + 60);
        
        $hourInitial = getenv("HOUR_INITIAL");
        $hourFinal = getenv("HOUR_FINAL");

        if (!($hourInitial < $horaAtual && $horaAtual < $hourFinal))
        {
            return self::getVaiVolta("O horário para cadastro de assinaturas já se encerrou!<br>Contate um assistente para realizar sua assinatura");
        }

        if (!("07:00:00" < $horaSaida && $horaSaida < "23:00:00"))
        {
            return self::getVaiVolta("O horário de saída informado não é válido!");
        }

        if ($dataAtual == $data)
        {
            if ($horaAtual > $horaSaida)
            {
                return self::getVaiVolta("O horário de saída informado não é válido!");
            }
        }

        if (!("07:00:00" < $horaChegada && $horaChegada < "23:00:00"))
        {
            return self::getVaiVolta("O horário de chegada informado não é válido!");
        }

        if ($horaSaida >= $horaChegada)
        {
            return self::getVaiVolta("O horário de chegada informado não é válido!");
        }

        if ($dataAtual > $data)
        {
            return self::getVaiVolta("A data informada não é válida!");
        }

        \App\Session\Login::init();
        
        $ob = EntityVaiVolta::getListByStudent($_SESSION['user']['usuario']['id']);

        if (!empty($ob))
        {
            foreach ($ob as $item)
            {
                if ($item->ativa)
                {
                    return self::getVaiVolta("O aluno já possui uma assinatura cadastrada nesta lista!");
                }
            }
        }

        $obList = new EntityVaiVolta(0, $_SESSION['user']['usuario']['id'], true, $destino, $data, $horaSaida, $horaChegada);

        $obList->cadastrar();

        return self::getVaiVolta("Assinatura registrada!", true);
    }
}

?>