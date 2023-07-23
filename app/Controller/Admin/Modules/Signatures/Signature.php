<?php

namespace App\Controller\Admin\Modules\Signatures;

use App\Controller\Admin\Alert;
use App\Controller\Admin\Page;
use App\Model\Entity\Listas;
use App\Model\Entity\Solicitation;
use App\Model\Entity\Student;
use App\Utils\View;

class Signature extends Page
{
    /**
     * Retorna a view da página de assinatura
     * @param Request $request
     * @return string
     */
    public static function getSignature($request, $list, $id)
    {
        parent::configNavbar("signatures");

        $content = View::render("admin/modules/lists/signature/index", [
            "lists" => self::getList($list, $id, $found),
            "solicitations" => self::getSolicitations($list, $id),
            "actions" => $found ? View::render("admin/modules/lists/signature/actions") : "",
            "list" => $list,
            "id" => $id
        ]);

        return parent::getPage("Assinatura", $content);
    }

    /**
     * Retorna a view de exclusão de assinatura
     * @param string $list
     * @param int $id
     * @return string
     */
    public static function getDeleteSignature()
    {
        parent::configNavbar("signatures");

        $content = View::render("admin/modules/lists/signature/delete");

        return parent::getPage("Assinatura | Excluir", $content);
    }
    
    /**
     * Configura a view de exclusão de assinatura
     * @param Request $request
     * @param string $list
     * @param int $id
     * @return string
     */
    public static function setDeleteSignature($request, $list, $id)
    {
        $postVars = $request->getPostVars();

        if ($postVars['acao'] == "voltar")
        {
            $request->getRouter()->redirect("/admin/signatures/".$list."/".$id);
            return;
        }

        $obList = null;

        switch ($list)
        {
            case "vai_volta":
                $obList = Listas\VaiVolta::getListById($id);
                break;
                
            case "saida":
                $obList = Listas\Saida::getListById($id);
                break;
                
            case "pernoite":
                $obList = Listas\Pernoite::getListById($id);
                break;
        }
        
        if (is_null($obList))
        {
            $request->getRouter()->redirect("/admin/signatures/".$list."/".$id);
            return;
        }

        $obList->excluir("id = ".$id);
        $request->getRouter()->redirect("/admin/signatures");
    }

    /**
     * Retorna a view de edição de assinatura
     * @param Request $request
     * @param string $list
     * @param int $id
     * @return string
     */
    public static function getEditSignature($request, $list, $id)
    {
        parent::configNavbar("signatures");
        
        $content = View::render("admin/modules/lists/signature/edit/index", [
            "list" => $list == "vai_volta" ? "Vai e Volta" : ucfirst($list),
            "list-form" => self::getListEditForm($request, $list, $id, $message, $success),
            "status" => !is_null($message) ? ($success ? Alert::getSuccess($message) : Alert::getError($message)) : ""
        ]);

        return parent::getPage("Assinatura | Editar", $content);
    }

    /**
     * Retorna os formulários de acordo com a lista escolhida
     * @param Request $request
     * @param string $list
     * @param int $id
     * @param string $message
     * @param bool $success
     * @return string
     */
    private static function getListEditForm($request, $list, $id, &$message, &$success)
    {
        $postVars = $request->getPostVars();

        if ($request->getHttpMethod() == "POST")
        {
            $success = false;

            switch ($list)
            {
                case "vai_volta":
                    $destino = $postVars['destino'];
                    $data = $postVars['data'];
                    $horaSaida = $postVars['hora_saida'].":00";
                    $horaChegada = $postVars['hora_chegada'].":00";

                    date_default_timezone_set("America/Sao_Paulo");
                    $dataAtual = date("Y-m-d", time());
                    $horaAtual = date("H:i:s", time() + 60);

                    if ($horaSaida >= $horaChegada)
                    {
                        $message = "O horário de chegada não é válido!";
                    }

                    if ($dataAtual > $data)
                    {
                        $message = "A data informada não é válida!";
                    }

                    else if ($dataAtual == $data && $horaAtual > $horaSaida)
                    {
                        $message = "O horário de saída não é válido!";
                    }

                    else
                    {
                        $message = "Atualizado com Sucesso!";
                        $success = true;
                        $obList = Listas\VaiVolta::getListById($id);

                        $obList->atualizar([
                            "destino" => $destino,
                            "data" => $data,
                            "hora_saida" => $horaSaida,
                            "hora_chegada" => $horaChegada,
                            "ativa" => true
                        ]);
                    }

                    break;

                case "pernoite":
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
                        $message = "A Data de Chegada não é Válida!";
                    }
            
                    else if ($horaSaida >= $horaChegada && $dataSaida == $dataChegada)
                    {
                        $message = "O Horário de Chegada não é Válido!";
                    }
            
                    else if ($dataAtual > $dataSaida)
                    {
                        $message = "A Data de Saída Informada não é Válida!";
                    }
            
                    else if ($dataAtual == $dataSaida && $horaAtual > $horaSaida)
                    {
                        $message = "O Horário de Saída não é Válido!";
                    }

                    else
                    {
                        $message = "Atualizado com Sucesso!";
                        $success = true;
                        $obList = Listas\Pernoite::getListById($id);

                        $obList->atualizar([
                            "nome_responsavel" => $nomeResponsavel,
                            "endereco" => $endereco,
                            "telefone" => $telefone,
                            "data_saida" => $dataSaida,
                            "data_chegada" => $dataChegada,
                            "hora_saida" => $horaSaida,
                            "hora_chegada" => $horaChegada,
                            "ativa" => true
                        ]);
                    }

                    break;

                case "saida":
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
                        $message = "A Data de Chegada não é Válida!";
                    }

                    else if ($horaSaida >= $horaChegada && $dataSaida == $dataChegada)
                    {
                        $message = "O Horário de Chegada não é Válido!";
                    }

                    else if ($dataAtual > $dataSaida)
                    {
                        $message = "A Data de Saída Informada não é Válida!";
                    }

                    else if ($dataAtual == $dataSaida && $horaAtual > $horaSaida)
                    {
                        $message = "O Horário de Saída não é Válido!";
                    }

                    else
                    {
                        $message = "Atualizado com Sucesso!";
                        $success = true;
                        $obList = Listas\Saida::getListById($id);

                        $obList->atualizar([
                            "destino" => $destino,
                            "data_saida" => $dataSaida,
                            "data_chegada" => $dataChegada,
                            "hora_saida" => $horaSaida,
                            "hora_chegada" => $horaChegada,
                            "ativa" => true
                        ]);
                    }

                    break;
            }
        }

        else
        {
            $message = null;
            $success = false;
        }

        $content = null;
        
        switch ($list)
        {
            case "vai_volta":
                $ob = Listas\VaiVolta::getListById($id);

                $content = View::render("admin/modules/lists/signature/edit/vai_volta", [
                    "id" => $ob->id,
                    "destino" => $ob->destino,
                    "data" => $ob->data,
                    "hora-saida" => $ob->horaSaida,
                    "hora-chegada" => $ob->horaChegada
                ]);

                break;

            case "pernoite":
                $ob = Listas\Pernoite::getListById($id);

                $content = View::render("admin/modules/lists/signature/edit/pernoite", [
                    "id" => $ob->id,
                    "endereco" => $ob->endereco,
                    "nome" => $ob->nomeResponsavel,
                    "telefone" => $ob->telefone,
                    "data-saida" => $ob->dataSaida,
                    "data-chegada" => $ob->dataChegada,
                    "hora-saida" => $ob->horaSaida,
                    "hora-chegada" => $ob->horaChegada
                ]);

                break;

            case "saida":
                $ob = Listas\Saida::getListById($id);

                $content = View::render("admin/modules/lists/signature/edit/saida", [
                    "id" => $ob->id,
                    "destino" => $ob->destino,
                    "data_saida" => $ob->dataSaida,
                    "data_chegada" => $ob->dataChegada,
                    "hora_saida" => $ob->horaSaida,
                    "hora_chegada" => $ob->horaChegada,
                ]);

                break;
        }

        return $content;
    }

    /**
     * Retorna os dados da assinatura
     * @param string $list
     * @param int $id
     * @param bool $found
     * @return string
     */
    private static function getList($list, $id, &$found)
    {
        $content = null;
        $obSignature = null;
        $dataSaida = null;
        $dataChegada = null;

        switch ($list)
        {
            case "vai_volta":
                $obSignature = Listas\VaiVolta::getListById($id);

                if (is_null($obSignature))
                {
                    break;
                }

                $data = explode("-", $obSignature->data, 4);
                $dataSaida = $data[2]."/".$data[1]."/".$data[0];
                $dataChegada = $data[2]."/".$data[1]."/".$data[0];
                $aluno = Student::getStudentById($obSignature->aluno)->nome;
                $destino = $obSignature->destino;
                $horaSaida = $obSignature->horaSaida;
                $horaChegada = $obSignature->horaChegada;

                $content = View::render("admin/modules/lists/signature/vai_volta", [
                    "aluno" => $aluno,
                    "destino" => $destino,
                    "data_saida" => $dataSaida,
                    "data_chegada" => $dataChegada,
                    "hora_saida" => $horaSaida,
                    "hora_chegada" => $horaChegada
                ]);

                break;

            case "saida":
                $obSignature = Listas\Saida::getListById($id);

                if (is_null($obSignature))
                {
                    break;
                }
                
                $data = explode("-", $obSignature->dataSaida, 4);
                $dataSaida = $data[2]."/".$data[1]."/".$data[0];
                $data = explode("-", $obSignature->dataChegada, 4);
                $dataChegada = $data[2]."/".$data[1]."/".$data[0];
                $aluno = Student::getStudentById($obSignature->aluno)->nome;
                $destino = $obSignature->destino;
                $horaSaida = $obSignature->horaSaida;
                $horaChegada = $obSignature->horaChegada;

                $content = View::render("admin/modules/lists/signature/saida", [
                    "aluno" => $aluno,
                    "destino" => $destino,
                    "data_saida" => $dataSaida,
                    "data_chegada" => $dataChegada,
                    "hora_saida" => $horaSaida,
                    "hora_chegada" => $horaChegada
                ]);

                break;

            case "pernoite":
                $obSignature = Listas\Pernoite::getListById($id);

                if (is_null($obSignature))
                {
                    break;
                }
                
                $data = explode("-", $obSignature->dataSaida, 4);
                $dataSaida = $data[2]."/".$data[1]."/".$data[0];
                $data = explode("-", $obSignature->dataChegada, 4);
                $dataChegada = $data[2]."/".$data[1]."/".$data[0];
                $aluno = Student::getStudentById($obSignature->aluno)->nome;
                $endereco = $obSignature->endereco;
                $responsavel = $obSignature->nomeResponsavel;
                $telefone = $obSignature->telefone;
                $horaSaida = $obSignature->horaSaida;
                $horaChegada = $obSignature->horaChegada;

                $content = View::render("admin/modules/lists/signature/pernoite", [
                    "aluno" => $aluno,
                    "endereco" => $endereco,
                    "responsavel" => $responsavel,
                    "telefone" => $telefone,
                    "data_saida" => $dataSaida,
                    "data_chegada" => $dataChegada,
                    "hora_saida" => $horaSaida,
                    "hora_chegada" => $horaChegada
                ]);

                break;
        }

        if (is_null($obSignature))
        {
            $content = View::render("admin/modules/lists/signature/not_found");
            $found = false;
        }

        else 
        {
            $found = true;
        }
        
        return $content;
    }

    /**
     * Retorna as solicitações associadas a esta assinatura
     * @param string $list
     * @param int $id
     * @return string
     */
    private static function getSolicitations($list, $id)
    {
        $content = "";
        $obList = null;

        switch ($list)
        {
            case "vai_volta":
                $obList = Listas\VaiVolta::getListById($id);
                break;
                
            case "pernoite":
                $obList = Listas\Pernoite::getListById($id);
                break;
                
            case "saida":
                $obList = Listas\Saida::getListById($id);
                break;
        }

        if (is_null($obList))
        {
            return null;
        }
        
        $aluno = $obList->aluno;
        $solicitations = Solicitation::getSolicitationByStudent($aluno);
        
        $aux = [];

        foreach ($solicitations as $item)
        {
            if ($item->lista == $list)
            {
                if ($item->idLista == $id)
                {
                    $aux[] = $item;
                }
            }
        }

        $solicitations = $aux;

        if (!empty($solicitations))
        {
            $itens = "";

            foreach ($solicitations as $item)
            {
                $data = explode("-", $item->dataAbertura, 10);
                $data = explode(" ", $data[2], 10)[0]."/".$data[1]."/".$data[0]." ".explode(" ", $data[2], 10)[1];

                $itens .= View::render("admin/modules/lists/signature/solicitation_item", [
                    "id" => $item->id,
                    "acao" => ucfirst($item->acao),
                    "data" => $data,
                    "ativa" => $item->ativa ? "Sim" : "Não"
                ]);
            }

            $content = View::render("admin/modules/lists/signature/solicitations", [
                "itens" => $itens,
            ]);
        }

        return $content;
    }
}