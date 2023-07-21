<?php

namespace App\Controller\Pages\Listas;

use App\Controller\Pages\Alert;
use App\Utils\View;
use App\Controller\Pages\Page;
use App\Model\Entity\Listas\Saida as EntitySaida;

class Saida extends Page
{
    /**
     * Retorna a view da lista de saída
     * @param string $message
     * @return string
     */
    public static function getSaida($message = null, $success = false)
    {
        $content = View::render("pages/listas/saida", [
            "status" => !is_null($message) ? (!$success ? Alert::getError($message) : Alert::getSuccess($message)) : ""
        ]);
        return parent::getPage("Listas | Saída", $content);
    }

    /**
     * Cadastra a assinatura
     * @param Request $request
     * @return string
     */
    public static function setSaida($request)
    {
        $postVars = $request->getPostVars();

        $destino = $postVars['destino'];
        $dataSaida = $postVars['data_saida'];
        $dataChegada = $postVars['data_chegada'];
        $horaSaida = $postVars['hora_saida'].":00";
        $horaChegada = $postVars['hora_chegada'].":00";

        date_default_timezone_set("America/Sao_Paulo");
        $dataAtual = date("Y-m-d", time());
        $horaAtual = date("H:i:s", time() + 60);

        if ($dataSaida > $dataChegada)
        {
            return self::getSaida("A Data de Chegada não é Válida!");
        }

        if ($horaSaida >= $horaChegada && $dataSaida == $dataChegada)
        {
            return self::getSaida("O Horário de Chegada não é Válido!");
        }

        if ($dataAtual > $dataSaida)
        {
            return self::getSaida("A Data de Saída Informada não é Válida!");
        }

        else if ($dataAtual == $dataSaida)
        {
            if ($horaAtual > $horaSaida)
            {
                return self::getSaida("O Horário de Saída não é Válido!");
            }
        }

        \App\Session\Login::init();
        
        $ob = Entitysaida::getListByStudent($_SESSION['user']['usuario']['id']);

        if (!empty($ob))
        {
            foreach ($ob as $item)
            {
                if ($item->ativa)
                {
                    return self::getSaida("O Aluno já possui uma Assinatura Ativa nessa Lista!");
                }
            }
        }

        $obList = new EntitySaida(0, $_SESSION['user']['usuario']['id'], true, $destino, $dataSaida, $dataChegada, $horaSaida, $horaChegada);

        $obList->cadastrar();

        return self::getSaida("Assinatura Registrada!", true);
    }
}

?>