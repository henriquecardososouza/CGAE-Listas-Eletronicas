<?php

namespace App\Controller\Pages\Solicitation;

use App\Controller\Pages\Page;
use App\Model\Entity\Solicitation;
use App\Utils\View;

class MySolicitation extends Page
{
    /**
     * Retorna a view da página de solicitação
     * @param int $id
     * @return $string
     */
    public static function getSolicitation($id)
    {
        $content = View::render("pages/solicitation/index", [
            "content" => self::getContent($id)
        ]);

        return parent::getPage("Solicitação", $content);
    }

    /**
     * Retorna o conteúdo principal da página
     * @param int $id
     * @return string
     */
    private static function getContent($id)
    {
        $ob = Solicitation::getSolicitationById($id);

        $lista = "";

        switch ($ob->lista)
        {
            case "vai_volta":
                $lista = "Vai e Volta";
                break;

            case "saida":
                $lista = "Saída";
                break;

            case "pernoite":
                $lista = "Pernoite";
                break;
        }

        $data = explode("-", $ob->dataAbertura, 4);
        $data = explode(" ", $data[2], 4)[0]."/".$data[1]."/".$data[0]." - ".explode(" ", $data[2], 4)[1];

        $dataConclusao = "--/--/----";

        if ($ob->dataEncerramento != null)
        {
            $dataConclusao = explode("-", $ob->dataEncerramento, 4);
            $dataConclusao = explode(" ", $dataConclusao[2], 4)[0]."/".$dataConclusao[1]."/".$dataConclusao[0]." - ".explode(" ", $dataConclusao[2], 4)[1];
        }

        return View::render("pages/solicitation/content", [
            "aluno" => $_SESSION['user']['usuario']['nome'],
            "motivo" => $ob->motivo,
            "acao" => ucfirst($ob->acao),
            "lista" => $lista,
            "status" => $ob->ativa ? "Em aberto" : "Finalizada",
            "resultado" => $ob->aprovada ? "Aprovada" : ($ob->ativa ? "---" : "Rejeitada"),
            "data_abertura" => $data,
            "data_conclusao" => $dataConclusao
        ]);
    }
}