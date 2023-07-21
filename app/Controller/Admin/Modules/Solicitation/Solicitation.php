<?php

namespace App\Controller\Admin\Modules\Solicitation;

use App\Controller\Admin\Page;
use App\Model\Entity\Solicitation as EntitySolicitation;
use App\Model\Entity\Student;
use App\Model\Entity\Listas;
use App\Model\Entity\Edit_Lists;
use App\Utils\View;

class Solicitation extends Page
{
    /**
     * Retorna a view de solicitação
     * @param Request $request
     * @param int $id
     * @return bool
     */
    public static function getSolicitation($request, $id)
    {
        if ($request->getHttpMethod() == "POST")
        {
            self::setAction($request, $id);
        }

        parent::configNavbar("solicitations");

        $content = View::render("admin/modules/solicitations/item/index", [
            "solicitation" => self::getItem($id),
            "actions" => self::getActions($id),
            "modal" => EntitySolicitation::getSolicitationById($id)->ativa ? self::getModal($id) : ""
        ]);

        return parent::getPage("Solicitações", $content);
    }

    /**
     * Retorna as informações da solicitação
     * @param int $id
     * @return string
     */
    private static function getItem($id)
    {
        $ob = EntitySolicitation::getSolicitationById($id);
        $data = explode("-", $ob->dataAbertura, 10);
        $data = explode(" ", $data[2], 10)[0]."/".$data[1]."/".$data[0]." - ".explode(" ", $data[2])[1];

        $content = "";
        $dataEncerramento = "--/--/--";
        $resultado = "---";

        if (!$ob->ativa)
        {
            $dataEncerramento = explode("-", $ob->dataEncerramento, 10);
            $dataEncerramento = explode(" ", $dataEncerramento[2], 10)[0]."/".$dataEncerramento[1]."/".$dataEncerramento[0]." - ".explode(" ", $dataEncerramento[2])[1];
            $resultado = $ob->aprovada ? "Aprovada" : "Rejeitada";
        }

        $content = View::render("admin/modules/solicitations/item/item", [
            "aluno" => Student::getStudentById($ob->aluno)->nome,
            "lista" => $ob->lista == "vai_volta" ? "Vai e Volta" : ucfirst($ob->lista),
            "data_abertura" => $data,
            "data_encerramento" => $dataEncerramento,
            "result" => $resultado,
            "acao" => ucfirst($ob->acao),
            "motivo" => $ob->motivo
        ]);

        return $content;
    }
    
    /**
     * Retorna os itens de ações da solicitação
     * @param int $id
     * @return string
     */
    private static function getActions($id)
    {
        $ob = EntitySolicitation::getSolicitationById($id);
        $content = "";

        if ($ob->ativa)
        {
            $content = View::render("admin/modules/solicitations/item/actions", [
                "compare_data" => $ob->acao == "editar" ? View::render("admin/modules/solicitations/item/btn_compare_data", [
                    "list" => $ob->lista
                ]) : ""
            ]);
        }

        return $content;
    }

    /**
     * Retorna a tela modal de comparação de dados
     * @param int $id
     * @return string
     */
    private static function getModal($id)
    {
        $content = "";
        $ob = EntitySolicitation::getSolicitationById($id);

        if (!is_null($ob))
        {
            if ($ob->acao == "editar")
            {
                $obList = null;
                $obEdit = null;

                switch ($ob->lista)
                {
                    case "vai_volta":
                        $obList = Listas\VaiVolta::getListById($ob->idLista);
                        $obEdit = Edit_Lists\VaiVolta::getListById($ob->idEdit);
                        
                        $content = View::render("admin/modules/solicitations/item/modal/vai_volta", [
                            "destino" => $obList->destino,
                            "destino_new" => $obEdit->destino,
                            "data" => $obList->data,
                            "data_new" => $obEdit->data,
                            "hora_saida" => $obList->horaSaida,
                            "hora_saida_new" => $obEdit->horaChegada,
                            "hora_chegada" => $obList->horaSaida,
                            "hora_chegada_new" => $obEdit->horaChegada
                        ]);

                        break;
                        
                    case "saida":
                        $obList = Listas\Saida::getListById($ob->idLista);
                        $obEdit = Edit_Lists\Saida::getListById($ob->idEdit);
                        
                        $content = View::render("admin/modules/solicitations/item/modal/saida", [
                            "destino" => $obList->destino,
                            "destino_new" => $obEdit->destino,
                            "data_saida" => $obList->dataSaida,
                            "data_saida_new" => $obEdit->dataSaida,
                            "data_chegada" => $obList->dataChegada,
                            "data_chegada_new" => $obEdit->dataChegada,
                            "hora_saida" => $obList->horaSaida,
                            "hora_saida_new" => $obEdit->horaSaida,
                            "hora_chegada" => $obList->horaChegada,
                            "hora_chegada_new" => $obEdit->horaChegada
                        ]);

                        break;
                        
                    case "pernoite":
                        $obList = Listas\Pernoite::getListById($ob->idLista);
                        $obEdit = Edit_Lists\Pernoite::getListById($ob->idEdit);

                        $content = View::render("admin/modules/solicitations/item/modal/pernoite", [
                            "endereco" => $obList->endereco,
                            "endereco_new" => $obEdit->endereco,
                            "nome" => $obList->nomeResponsavel,
                            "nome_new" => $obEdit->nomeResponsavel,
                            "telefone" => $obList->telefone,
                            "telefone_new" => $obEdit->telefone,
                            "data_saida" => $obList->dataSaida,
                            "data_saida_new" => $obEdit->dataSaida,
                            "data_chegada" => $obList->dataChegada,
                            "data_chegada_new" => $obEdit->dataChegada,
                            "hora_saida" => $obList->horaSaida,
                            "hora_saida_new" => $obEdit->horaChegada,
                            "hora_chegada" => $obList->horaSaida,
                            "hora_chegada_new" => $obEdit->horaChegada
                        ]);

                        break;
                }
            }
        }

        return $content;
    }

    /**
     * Realiza as ações dispostas ao assistente
     * @param Request $request
     * @param int $id
     */
    private static function setAction($request, $id)
    {
        $acao = $request->getPostVars()['acao'] ?? "";

        date_default_timezone_set("America/Sao_Paulo");
        $data = date("Y-m-d H:i:s", time());
        
        if ($acao == "aceitar")
        {
            $ob = EntitySolicitation::getSolicitationById($id);

            $ob->atualizar([
                "ativa" => false,
                "aprovada" => true,
                "data_encerramento" => $data
            ]);

            switch ($ob->lista)
            {
                case "vai_volta":
                    $obList = Listas\VaiVolta::getListById($ob->idLista);

                    if ($ob->acao == "excluir")
                    {
                        $obList->excluir();
                        $data = EntitySolicitation::processData(EntitySolicitation::getSolicitation("lista = '".$ob->lista."' AND id_lista = ".$ob->idLista));

                        foreach ($data as $item)
                        {
                            if ($item->id != $ob->id)
                            {
                                $item->atualizar([
                                    "ativa" => false,
                                    "aprovada" => false,
                                    "data_encerramento" => $data
                                ]);
                            }
                        }
                    }

                    else
                    {
                        $obEdit = Edit_Lists\VaiVolta::getListById($ob->idEdit);

                        $obList->atualizar([
                            "ativa" => true,
                            "destino" => $obEdit->destino,
                            "data" => $obEdit->data,
                            "hora_saida" => $obEdit->horaSaida,
                            "hora_chegada" => $obEdit->horaChegada
                        ]);
                    }

                    break;

                case "pernoite":
                    $obList = Listas\Pernoite::getListById($ob->idLista);

                    if ($ob->acao == "excluir")
                    {
                        $obList->excluir();
                        $data = EntitySolicitation::processData(EntitySolicitation::getSolicitation("lista = '".$ob->lista."' AND id_lista = ".$ob->idLista));

                        foreach ($data as $item)
                        {
                            if ($item->id != $ob->id)
                            {
                                $item->atualizar([
                                    "ativa" => false,
                                    "aprovada" => false,
                                    "data_encerramento" => $data
                                ]);
                            }
                        }
                    }

                    else
                    {
                        $obEdit = Edit_Lists\Pernoite::getListById($ob->idEdit);

                        $obList->atualizar([
                            "ativa" => true,
                            "endereco" => $obEdit->endereco,
                            "nome_responsavel" => $obEdit->nomeResponsavel,
                            "telefone" => $obEdit->telefone,
                            "data_saida" => $obEdit->dataSaida,
                            "data_chegada" => $obEdit->dataChegada,
                            "hora_saida" => $obEdit->horaSaida,
                            "hora_chegada" => $obEdit->horaChegada
                        ]);
                    }

                    break;

                case "saida":
                    $obList = Listas\Saida::getListById($ob->idLista);
                    
                    if ($ob->acao == "excluir")
                    {
                        $obList->excluir();
                        $data = EntitySolicitation::processData(EntitySolicitation::getSolicitation("lista = '".$ob->lista."' AND id_lista = ".$ob->idLista));

                        foreach ($data as $item)
                        {
                            if ($item->id != $ob->id)
                            {
                                $item->atualizar([
                                    "ativa" => false,
                                    "aprovada" => false,
                                    "data_encerramento" => $data
                                ]);
                            }
                        }
                    }

                    else
                    {
                        $obEdit = Edit_Lists\Saida::getListById($ob->idEdit);

                        $obList->atualizar([
                            "ativa" => true,
                            "destino" => $obEdit->destino,
                            "data_saida" => $obEdit->dataSaida,
                            "data_chegada" => $obEdit->dataChegada,
                            "hora_saida" => $obEdit->horaSaida,
                            "hora_chegada" => $obEdit->horaChegada
                        ]);
                    }

                    break;
            }

            $request->getRouter()->redirect("/admin/solicitations/open");
            return;
        }

        else if ($acao == "rejeitar")
        {
            $ob = EntitySolicitation::getSolicitationById($id);
           
            $ob->atualizar([
                "ativa" => false,
                "aprovada" => false,
                "data_encerramento" => $data
            ]);

            $request->getRouter()->redirect("/admin/solicitations/open");
            return;
        }
    }
}

?>