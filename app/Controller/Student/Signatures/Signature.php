<?php

namespace App\Controller\Student\Signatures;

use App\Controller\Student\Page;
use App\Controller\Page\Alert;
use App\Utils\View;
use App\Model\Entity\Aluno as EntityAluno;
use App\Model\Entity\Lists\VaiVolta as EntityVaiVolta;
use App\Model\Entity\Lists\Pernoite as EntityPernoite;
use App\Model\Entity\Lists\Saida as EntitySaida;
use App\Model\Entity\Edit_Lists\VaiVolta as EntityEditVaiVolta;
use App\Model\Entity\Edit_Lists\Pernoite as EntityEditPernoite;
use App\Model\Entity\Edit_Lists\Saida as EntityEditSaida;

/**
 * Controlador da página de assinatura (aluno)
 */
class Signature extends Page
{
    /**
     * Retorna a view da página de assinatura
     * @param Request $request Objeto de requisição
     * @param string $list Nome da lista correspondente a assinatura
     * @param int $id ID da assinatura no banco
     * @param string $message Texto da mensagem de alerta
     * @param bool $success Indica se a mensagem corresponde a um processo bem ou mal sucedido
     * @return string View renderizada
     */
    public static function getSignature($list, $id, $message = null, $success = false)
    {
        // CONFIGURA A NAVBAR
        parent::setActiveModule("assinaturas");

        $item = "";
        $lista = "";
        $edit = "";

        // OBTÉM OS DADOS DA ASSINATURA
        switch ($list)
        {
            case "vai_volta":
                $item = self::getVaiVolta($id, $ativa);
                $edit = self::getVaiVoltaEditModal($id);
                $lista = "Vai e Volta";
                break;

            case "pernoite":
                $item = self::getPernoite($id, $ativa);
                $edit = self::getPernoiteEditModal($id);
                $lista = "Pernoite";
                break;

            default:
                $item = self::getSaida($id, $ativa);
                $edit = self::getSaidaEditModal($id);
                $lista = "Saida";
                break;
        }

        $actions = self::getActions($ativa);

        // RENDERIZA A VIEW
        $content = View::render("student/signatures/signature/index", [
            "status" => is_null($message) ? "" : ($success ? Alert::getSuccess($message) : Alert::getError($message)),
            "lista" => $lista,
            "name_list" => $list,
            "id" => $id,
            "content" => $item,
            "actions" => $actions,
            "edit" => $edit
        ]);

        return parent::getPage("Minhas Assinaturas", $content);
    }

    /**
     * Processa as ações da página
     * @param Request $request Objeto de requisição
     * @param string $list Nome da lista correspondete a assinatura
     * @param int $id ID da assinatura no banco
     * @return string View renderizada
     */
    public static function setSignature($request, $list, $id)
    {
        // OBTÉM AS VARIÁVIES DE POST
        $postVars = $request->getPostVars();

        // VERIFICA SE ALGUMA AÇÃO FOI SOLICITADA
        switch ($postVars['acao'])
        {
            case "encerrar":
                return self::endSignature($list, $id);

            case "editar":
                return self::editSignature($request, $list, $id);

            default:
                return self::getSignature($request, $list, $id);
        }
    }

    /**
     * Encerra uma assinatura
     * @param Request $request Objeto de requisição
     * @param string $list Nome da lista a qual pertence a assinatura
     * @param int $id ID da assinatura no banco
     * @return string View renderizada
     */
    private static function endSignature($list, $id)
    {
        // INICIALIZA A SESSÃO
        \App\Session\Login::init();
        
        // OBTÉM A DATA E HORA ATUAL
        date_default_timezone_set("America/Sao_Paulo");
        $dataAtual = date("Y-m-d", time());
        $horaAtual = date("H:i:s", time() + 60);

        // ATUALIZA A ASSINATURA
        switch ($list)
        {
            case "vai_volta":
                EntityVaiVolta::atualizarAssinaturas("id = ".$id, [
                    "hora_chegada" => $horaAtual,
                    "ativa" => false
                ]);
                break;

            case "saida":
                EntitySaida::atualizarAssinaturas("id = ".$id, [
                    "data_chegada" => $dataAtual,
                    "hora_chegada" => $horaAtual,
                    "ativa" => false
                ]);
                break;

            case "pernoite":
                EntityPernoite::atualizarAssinaturas("id = ".$id, [
                    "data_chegada" =>$dataAtual,
                    "hora_chegada" => $horaAtual,
                    "ativa" => false
                ]);
                break;
        }

        return self::getSignature($list, $id, "Assinatura encerrada", true);
    }

    /**
     * Edita uma assinatura
     * @param Request $request Objeto de requisição
     * @param string $list Nome da lista a qual pertence a assinatura
     * @param int $id ID da assinatura no banco
     * @return string View renderizada
     */
    private static function editSignature($request, $list, $id)
    {
        // INICIALIZA A SESSÃO
        \App\Session\Login::init();

        // RECUPERA AS VARIÁVEIS DE POST
        $postVars = $request->getPostVars();

        // OBTÉM A DATA E HORA ATUAL
        date_default_timezone_set("America/Sao_Paulo");
        $dataAtual = date("Y-m-d H:i:s", time());
        $horaAtual = date("", time() + 60);
        
        // SALVA A ASSINATURA ATUAL
        switch ($list)
        {
            case "vai_volta":
                // RECUPERA A ASSINATURA ATUAL E SALVA SUA CÓPIA
                $obSignature = EntityVaiVolta::getSignatureById($id);
                $obEdit = new EntityEditVaiVolta(0, $id, $dataAtual, $horaAtual, $obSignature->destino, $obSignature->data, $obSignature->horaSaida, $obSignature->horaChegada);
                $obEdit->cadastrar();

                // ATUALIZA A ASSINATURA
                $obSignature->atualizar([
                    "destino" => $postVars['destino'] ?? $obSignature->destino,
                    "data" => $postVars['data'] ?? $obSignature->data,
                    "hora_saida" => $postVars['hora_saida'] ?? $obSignature->horaSaida,
                    "hora_chegada" => $postVars['hora_chegada'] ?? $obSignature->horaChegada    
                ]);

                break;
                
            case "pernoite":
                // RECUPERA A ASSINATURA ATUAL E SALVA SUA CÓPIA
                $obSignature = EntityPernoite::getSignatureById($id);
                $obEdit = new EntityEditPernoite(0, $id, $dataAtual, $horaAtual, $obSignature->nomeResponsavel, $obSignature->endereco, $obSignature->telefone, $obSignature->dataSaida, $obSignature->dataChegada, $obSignature->horaSaida, $obSignature->horaChegada);
                $obEdit->cadastrar();

                // ATUALIZA A ASSINATURA
                $obSignature->atualizar([
                    "nome_responsavel" => $postVars['nome'] ?? $obSignature->nomeResponsavel,
                    "telefone" => $postVars['telefone'] ?? $obSignature->telefone,
                    "endereco" => $postVars['endereco'] ?? $obSignature->endereco,
                    "data_saida" => $postVars['data_saida'] ?? $obSignature->dataSaida,
                    "data_chegada" => $postVars['data_chegada'] ?? $obSignature->dataChegada,
                    "hora_saida" => $postVars['hora_saida'] ?? $obSignature->horaSaida,
                    "hora_chegada" => $postVars['hora_chegada'] ?? $obSignature->horaChegada
                ]);

                break;
                
            case "saida":
                // RECUPERA A ASSINATURA ATUAL E SALVA SUA CÓPIA
                $obSignature = EntitySaida::getSignatureById($id);
                $obEdit = new EntityEditSaida(0, $id, $dataAtual, $horaAtual, $obSignature->destino, $obSignature->dataSaida, $obSignature->dataChegada, $obSignature->horaSaida, $obSignature->horaChegada);
                $obEdit->cadastrar();

                // ATUALIZA A ASSINATURA
                $obSignature->atualizar([
                    "destino" => $postVars['destino'] ?? $obSignature->destino,
                    "data_saida" => $postVars['data_saida'] ?? $obSignature->dataSaida,
                    "data_chegada" => $postVars['data_chegada'] ?? $obSignature->dataChegada,
                    "hora_saida" => $postVars['hora_saida'] ?? $obSignature->horaSaida,
                    "hora_chegada" => $postVars['hora_chegada'] ?? $obSignature->horaChegada
                ]);

                break;
        }

        return self::getSignature($list, $id, "Solicitação Enviada", true);
    }

    /**
     * Retorna a view segundo a lista de vai e volta
     * @param int $id
     * @return string
     */
    private static function getVaiVolta($id, &$ativa)
    {
        $list = EntityVaiVolta::getSignatureById($id);

        if (is_null($list))
        {
            throw new \Exception("page not found", 404);
        }

        $ativa = $list->ativa;
        $aluno = EntityAluno::getAlunoById($list->aluno)->nome;

        $data = explode("-", $list->data, 4);
        $data = $data[2]."/".$data[1]."/".$data[0];

        $content = View::render("student/signatures/signature/vai_volta/index", [
            "aluno" => $aluno,
            "destino" => $list->destino,
            "data" => $data,
            "hora_saida" => $list->horaSaida,
            "hora_chegada" => $list->horaChegada
        ]);

        return $content;
    }

    /**
     * Retorna a view segundo a lista de pernoite
     * @param int $id
     * @return string
     */
    private static function getPernoite($id, &$ativa)
    {
        $list = EntityPernoite::getSignatureById($id);

        if (is_null($list))
        {
            throw new \Exception("page not found", 404);
        }

        $ativa = $list->ativa;
        $aluno = EntityAluno::getAlunoById($list->aluno)->nome;

        $dataSaida = explode("-", $list->dataSaida, 4);
        $dataSaida = $dataSaida[2]."/".$dataSaida[1]."/".$dataSaida[0];
        
        $dataChegada = explode("-", $list->dataSaida, 4);
        $dataChegada = $dataChegada[2]."/".$dataChegada[1]."/".$dataChegada[0];

        $content = View::render("student/signatures/signature/pernoite/index", [
            "aluno" => $aluno,
            "endereco" => $list->endereco,
            "nome" => $list->nomeResponsavel,
            "telefone" => $list->telefone,
            "data_saida" => $dataSaida,
            "data_chegada" => $dataChegada,
            "hora_saida" => $list->horaSaida,
            "hora_chegada" => $list->horaChegada
        ]);

        return $content;
    }

    /**
     * Retorna a view segundo a lista de saída
     * @param int $id
     * @return string
     */
    private static function getSaida($id, &$ativa)
    {
        $list = EntitySaida::getSignatureById($id);

        if (is_null($list))
        {
            throw new \Exception("page not found", 404);
        }

        $ativa = $list->ativa;
        $aluno = EntityAluno::getAlunoById($list->aluno)->nome;

        $dataSaida = explode("-", $list->dataSaida, 4);
        $dataSaida = $dataSaida[2]."/".$dataSaida[1]."/".$dataSaida[0];
        
        $dataChegada = explode("-", $list->dataSaida, 4);
        $dataChegada = $dataChegada[2]."/".$dataChegada[1]."/".$dataChegada[0];

        $content = View::render("student/signatures/signature/saida/index", [
            "aluno" => $aluno,
            "destino" => $list->destino,
            "data_saida" => $dataSaida,
            "data_chegada" => $dataChegada,
            "hora_saida" => $list->horaSaida,
            "hora_chegada" => $list->horaChegada
        ]);

        return $content;
    }

    /**
     * Retorna os botões de ação
     * @param string $uri
     * @return string
     */
    private static function getActions($ativa)
    {
        if (!$ativa)
        {
            return "";
        }

        return View::render("student/signatures/signature/actions");
    }

    /**
     * Retorna a div modal do formulário de edição da lista vai e volta
     * @param int $id
     */
    private static function getVaiVoltaEditModal($id)
    {
        $ob = EntityVaiVolta::getSignatureById($id);

        return View::render("student/signatures/signature/vai_volta/edit_modal", [
            "destino" => $ob->destino,
            "data" => $ob->data,
            "hora_saida" => $ob->horaSaida,
            "hora_chegada" => $ob->horaChegada
        ]);
    }

    /**
     * Retorna a div modal do formulário de edição da lista pernoite
     * @param int $id
     */
    private static function getPernoiteEditModal($id)
    {
        $ob = EntityPernoite::getSignatureById($id);

        return View::render("student/signatures/signature/pernoite/edit_modal", [
            "endereco" => $ob->endereco,
            "nome" => $ob->nomeResponsavel,
            "telefone" => $ob->telefone,
            "data_saida" => $ob->dataSaida,
            "data_chegada" => $ob->dataChegada,
            "hora_saida" => $ob->horaSaida,
            "hora_chegada" => $ob->horaChegada
        ]);
    }

    /**
     * Retorna a div modal do formulário de edição da lista saída
     * @param int $id
     */
    private static function getSaidaEditModal($id)
    {
        $ob = EntitySaida::getSignatureById($id);

        return View::render("student/signatures/signature/saida/edit_modal", [
            "destino" => $ob->destino,
            "data_saida" => $ob->dataSaida,
            "data_chegada" => $ob->dataChegada,
            "hora_saida" => $ob->horaSaida,
            "hora_chegada" => $ob->horaChegada
        ]);
    }
}

?>