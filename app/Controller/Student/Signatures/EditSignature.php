<?php

namespace App\Controller\Student\Signatures;

use App\Controller\Student\Page;
use App\Model\Entity\Listas\Pernoite;
use App\Model\Entity\Listas\Saida;
use App\Model\Entity\Listas\VaiVolta;

class EditSignature extends Page
{
    public static function getSignature($list, $id)
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
            "endereco" => $ob->destino,
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
}