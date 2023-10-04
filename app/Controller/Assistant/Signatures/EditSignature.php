<?php

namespace App\Controller\Assistant\Signatures;

use App\Controller\Assistant\Page;
use App\Controller\Common\Alert;
use App\Model\Entity\Listas\Saida;
use App\Model\Entity\Listas\Pernoite;
use App\Model\Entity\Listas\VaiVolta;

class EditSignature extends Page
{
    public static function getEditSignature($type, $id, $message = null, $success = false)
    {
        parent::setActiveModule("signatures");

        $data = self::getData($type, $id);
        $form = "";
        $list = "";

        switch ($type)
        {
            case "vai_volta":
                $list = "Vai e volta";
                $form = self::getVaiVoltaForm($data);
                break;

            case "saida":
                $list = "Saída";
                $form = self::getSaidaForm($data);
                break;

            case "pernoite":
                $list = "Pernoite";
                $form = self::getPernoiteForm($data);
                break;
        }

        $content = parent::render("signature/edit/index", [
            "list" => $list,
            "status" => is_null($message) ? "" : ($success ? Alert::getSuccess($message) : Alert::getError($message)),
            "form" => $form
        ]);

        return parent::getPage("Editar assinatura", $content);
    }

    public static function setEditSignature($request, $list, $id)
    {
        $postVars = $request->getPostVars();

        try
        {
            if ($postVars['acao'] != "editar") throw new \Exception();

            $var = [];

            foreach ($postVars as $key => $value)
            {
                if ($key == "acao") continue;

                $var[$key] = $value;
            }

            switch ($list)
            {
                case "vai_volta":
                    $ob = new VaiVolta(-1, $_SESSION['user']['usuario']['id'], null, true, $postVars['destino'], $postVars['data'], $postVars['hora_saida'], $postVars['hora_chegada']);
                    $ob->cadastrar();
                    VaiVolta::atualizarAssinaturas("id = ".$id, ["ativa" => false, "pai" => $ob->id]);
                    break;

                case "saida":
                    $ob = new Saida(-1, $_SESSION['user']['usuario']['id'], null, true, $postVars['destino'], $postVars['data_saida'], $postVars['data_chegada'], $postVars['hora_saida'], $postVars['hora_chegada']);
                    $ob->cadastrar();
                    Saida::atualizarAssinaturas("id = ".$id, ["ativa" => false, "pai" => $ob->id]);
                    break;

                case "pernoite":
                    $ob = new Pernoite(-1, $_SESSION['user']['usuario']['id'], null, true, $postVars['endereco'], $postVars['nome_responsavel'], $postVars['telefone'], $postVars['data_saida'], $postVars['data_chegada'], $postVars['hora_saida'], $postVars['hora_chegada']);
                    $ob->cadastrar();
                    Pernoite::atualizarAssinaturas("id = ".$id, ["ativa" => false, "pai" => $ob->id]);
                    break;

                default:
                    throw new \Exception();
                    break;
            }

            $request->getRouter()->redirect("/ass/listas/".$list."/".$ob->id."?status=success");
        }
        
        catch (\Exception $e)
        {
            return self::getEditSignature($list, $id);
        }
    }

    private static function getVaiVoltaForm($data)
    {
        $content = parent::render("signature/edit/vai_volta", [
            "id" => $data->id,
            "destino" => $data->destino,
            "data" => $data->data,
            "hora-saida" => $data->horaSaida,
            "hora-chegada" => $data->horaChegada
        ]);

        return $content;
    }

    private static function getSaidaForm($data)
    {
        $content = parent::render("signature/edit/saida", [
            "id" => $data->id,
            "destino" => $data->destino,
            "data_saida" => $data->dataSaida,
            "data_chegada" => $data->dataChegada,
            "hora_saida" => $data->horaSaida,
            "hora_chegada" => $data->horaChegada,
        ]);
        
        return $content;
    }

    private static function getPernoiteForm($data)
    {
        $content = parent::render("signature/edit/pernoite", [
            "id" => $data->id,
            "endereco" => $data->endereco,
            "nome" => $data->nomeResponsavel,
            "telefone" => $data->telefone,
            "data-saida" => $data->dataSaida,
            "data-chegada" => $data->dataChegada,
            "hora-saida" => $data->horaSaida,
            "hora-chegada" => $data->horaChegada
        ]);
        
        return $content;
    }

    private static function getData($type, $id)
    {
        $ob = null;

        switch ($type)
        {
            case "vai_volta":
                $ob = VaiVolta::getSignatureById($id);
                break;
                
            case "saida":
                $ob = Saida::getSignatureById($id);
                break;
                
            case "pernoite":
                $ob = Pernoite::getSignatureById($id);
                break;
        }

        return $ob;
    }
}