<?php

namespace App\Controller\Student\Signatures;

use App\Controller\Common\Alert;
use App\Controller\Student\Page;
use App\Model\Entity\Listas\Pernoite;
use App\Model\Entity\Listas\Saida;
use App\Model\Entity\Listas\VaiVolta;

class EditSignature extends Page
{
    public static function getSignature($list, $id, $message = null)
    {
        parent::setActiveModule("assinaturas");

        switch ($list)
        {
            case "vai_volta":
                $form = self::getVaiVoltaForm($id);
                break;

            case "saida":
                $form = self::getSaidaForm($id);
                break;

            case "pernoite":
                $form = self::getPernoiteForm($id);
                break;

            default:
                throw new \Exception("not found", 404);
        }

        $content = parent::render("signature/edit/index", [
            "alert" => is_null($message) ? "" : "<hr>".Alert::getError($message)."<hr>",
            "form" => $form
        ]);

        return parent::getPage("Editar assinatura", $content);
    }

    public static function setSignature($request, $list, $id)
    {
        $postVars = $request->getPostVars();

        try
        {
            if ($postVars['acao'] != "editar") throw new \Exception();

            switch ($list)
            {
                case "vai_volta":
                    $res = self::verifyVaiVoltaData($postVars);

                    if (!is_null($res))
                    {
                        return self::getSignature($list, $id, $res);
                    }

                    $ob = new VaiVolta(-1, $_SESSION['user']['usuario']['id'], null, true, $postVars['destino'], $postVars['data'], $postVars['hora_saida'], $postVars['hora_chegada']);
                    $ob->cadastrar();
                    VaiVolta::atualizarAssinaturas("id = ".$id, ["ativa" => false, "pai" => $ob->id]);
                    break;

                case "saida":
                    $res = self::verifySaidaData($postVars);

                    if (!is_null($res))
                    {
                        return self::getSignature($list, $id, $res);
                    }

                    $ob = new Saida(-1, $_SESSION['user']['usuario']['id'], null, true, $postVars['destino'], $postVars['data_saida'], $postVars['data_chegada'], $postVars['hora_saida'], $postVars['hora_chegada']);
                    $ob->cadastrar();
                    Saida::atualizarAssinaturas("id = ".$id, ["ativa" => false, "pai" => $ob->id]);
                    break;

                case "pernoite":
                    $res = self::verifyPernoiteData($postVars);

                    if (!is_null($res))
                    {
                        return self::getSignature($list, $id, $res);
                    }

                    $ob = new Pernoite(-1, $_SESSION['user']['usuario']['id'], null, true, $postVars['endereco'], $postVars['nome_responsavel'], $postVars['telefone'], $postVars['data_saida'], $postVars['data_chegada'], $postVars['hora_saida'], $postVars['hora_chegada']);
                    $ob->cadastrar();
                    Pernoite::atualizarAssinaturas("id = ".$id, ["ativa" => false, "pai" => $ob->id]);
                    break;

                default:
                    throw new \Exception("not found", 404);
                    break;
            }

            $request->getRouter()->redirect("/assinaturas/".$list."/".$ob->id."?status=success");
        }
        
        catch (\Exception $e)
        {
            return self::getSignature($list, $id);
        }
    }

    private static function getVaiVoltaForm($id)
    {
        $ob = VaiVolta::getSignatureById($id) ?? throw new \Exception("not found", 404);

        $content = parent::render("signature/edit/vai_volta", [
            "destino" => $ob->destino,
            "data" => $ob->data,
            "hora_saida" => $ob->horaSaida,
            "hora_chegada" => $ob->horaChegada
        ]);

        return $content;
    }

    private static function getSaidaForm($id)
    {
        $ob = Saida::getSignatureById($id) ?? throw new \Exception("not found", 404);

        $content = parent::render("signature/edit/saida", [
            "destino" => $ob->destino,
            "data_saida" => $ob->dataSaida,
            "data_chegada" => $ob->dataChegada,
            "hora_saida" => $ob->horaSaida,
            "hora_chegada" => $ob->horaChegada
        ]);

        return $content;
    }

    private static function getPernoiteForm($id)
    {
        $ob = Pernoite::getSignatureById($id) ?? throw new \Exception("not found", 404);

        $content = parent::render("signature/edit/pernoite", [
            "endereco" => $ob->endereco,
            "nome_responsavel" => $ob->nomeResponsavel,
            "telefone" => $ob->telefone,
            "data_saida" => $ob->dataSaida,
            "data_chegada" => $ob->dataChegada,
            "hora_saida" => $ob->horaSaida,
            "hora_chegada" => $ob->horaChegada
        ]);

        return $content;
    }

    /**
     * 
     * @param array $vars
     * @return string|null
     */
    private static function verifyVaiVoltaData($vars)
    {
        $destino = $vars['destino'];
        $data = $vars['data'];
        $horaSaida = $vars['hora_saida'].":00";
        $horaChegada = $vars['hora_chegada'].":00";

        // OBTÉM A DATA E HORA ATUAIS
        date_default_timezone_set("America/Sao_Paulo");
        $dataAtual = date("Y-m-d", time());
        $horaAtual = date("H:i:s", time() + 60);

        // VERIFICA SE OS DADOS DA ASSINATURA SÃO VÁLIDOS

        if ($dataAtual > $data)
        {
            return "A data de saída não pode ser anterior a data atual!";
        }

        if (!("05:00:00" <= $horaSaida && $horaSaida <= "23:00:00"))
        {
            return "O horário de saída deve estar compreendido entre as 05:00 e as 23:00 horas!";
        }

        if (!("05:00:00" <= $horaChegada && $horaChegada <= "23:00:00"))
        {
            return "O horário de chegada deve estar compreendido entre as 05:00 e as 23:00 horas!";
        }

        if ($dataAtual == $data)
        {
            if ($horaAtual > $horaSaida)
            {
                return "O horário de saída deve ser posteior ao horário atual!";
            }
        }

        if ($horaSaida >= $horaChegada)
        {
            return "O horário de chegada deve ser posteior ao horário de saída!";
        }

        return null;
    }

    /**
     * 
     * @param array $vars
     * @return string|null
     */
    private static function verifySaidaData($vars)
    {
        
    }

    /**
     * 
     * @param array $vars
     * @return string|null
     */
    private static function verifyPernoiteData($vars)
    {
        
    }
}