<?php

namespace App\Controller\Pages\Minhas_Listas;

use App\Utils\View;
use App\Controller\Pages\Page;
use App\Model\Entity\Student as EntityStudent;
use App\Model\Entity\Listas\VaiVolta as EntityVaiVolta;
use App\Model\Entity\Listas\Pernoite as EntityPernoite;
use App\Model\Entity\Listas\Saida as EntitySaida;
use App\Model\Entity\Solicitation as EntitySolicitation;
use App\Model\Entity\Edit_lists\VaiVolta as EntityEditVaiVolta;
use App\Model\Entity\Edit_lists\Pernoite as EntityEditPernoite;
use App\Model\Entity\Edit_lists\Saida as EntityEditSaida;

class MyList extends Page
{
    /**
     * Retorna a view da página de assinatura
     * @param Request $request
     * @param string $list
     * @param int $id
     * @return string
     */
    public static function getList($request, $list, $id, $message = null, $success = false)
    {
        if ($request->getHttpMethod() == "POST" && is_null($message))
        {
            switch ($request->getPostVars()['acao'])
            {
                case "excluir":
                    return self::sendDeleteSolicitation($request, $list, $id);

                case "editar":
                    return self::sendEditSolicitation($request, $list, $id);
            }
        }

        $item = "";
        $lista = "";
        $nameList = "";
        $actions = "";
        $edit = "";

        switch ($list)
        {
            case "vai_volta":
                $item = self::getVaiVolta($id);
                $lista = "Vai e Volta";
                $nameList = "vai_volta";
                $actions = self::getActions($request->getUri());
                $edit = self::getVaiVoltaEditModal($id);

                break;

            case "pernoite":
                $item = self::getPernoite($id);
                $lista = "Pernoite";
                $nameList = "pernoite";
                $actions = self::getActions($request->getUri());
                $edit = self::getPernoiteEditModal($id);
                break;

            case "saida":
                $item = self::getSaida($id);
                $lista = "Saida";
                $nameList = "saida";
                $actions = self::getActions($request->getUri());
                $edit = self::getSaidaEditModal($id);
                break;
        }

        $status = "";

        if (!is_null($message))
        {
            if ($success)
            {
                $status = \App\Controller\Pages\Alert::getSuccess($message);
            }

            else 
            {
                $status = \App\Controller\Pages\Alert::getError($message);
            }
        }

        $content = View::render("pages/my_lists/index", [
            "status" => $status,
            "lista" => $lista,
            "name_list" => $nameList,
            "id" => $id,
            "content" => $item,
            "actions" => $actions,
            "edit" => $edit
        ]);

        return parent::getPage("Minhas Assinaturas", $content);
    }

    /**
     * Cria uma solicitação de exclusão de assinatura
     * @param Request $request
     * @param string $list
     * @param int $id
     */
    public static function sendDeleteSolicitation($request, $list, $id)
    {
        \App\Session\Login::init();

        $postVars = $request->getPostVars();

        $ob = EntitySolicitation::processData(EntitySolicitation::getSolicitation("id_lista = ".$id." AND lista = '".$list."' AND aluno = ".$_SESSION['user']['usuario']['id']." AND acao = 'excluir' AND ativa = true"));
        
        if (!empty($ob))
        {
            return self::getList($request, $list, $id, "Já existe uma solicitação de exclusão ativa para essa assinatura!<br>Espere a conclusão de uma solicitação para abrir uma nova");
        }
        
        date_default_timezone_set("America/Sao_Paulo");
        $dataAtual = date("Y-m-d", time());
        $horaAtual = date("H:i:s", time() + 60);

        $ob = new EntitySolicitation();
        $ob->aluno = $_SESSION['user']['usuario']['id'];
        $ob->idLista = (int)$id;
        $ob->idEdit = -1;
        $ob->lista = $list;
        $ob->acao = $postVars['acao'];
        $ob->motivo = $postVars['motivo'];
        $ob->ativa = true;
        $ob->aprovada = false;
        $ob->dataAbertura = $dataAtual." ".$horaAtual;

        $ob->cadastrar();

        return self::getList($request, $list, $id, "Solicitação Enviada", true);
    }

    /**
     * Cria uma solicitação de edição de um assinatura
     * @param Request $request
     * @param string $list
     * @param int $id
     */
    public static function sendEditSolicitation($request, $list, $id)
    {
        \App\Session\Login::init();

        $postVars = $request->getPostVars();

        $ob = EntitySolicitation::processData(EntitySolicitation::getSolicitation("id_lista = ".$id." AND lista = '".$list."' AND aluno = ".$_SESSION['user']['usuario']['id']." AND acao = 'editar' AND ativa = true"));
        
        if (!empty($ob))
        {
            return self::getList($request, $list, $id, "Já existe uma solicitação de edição ativa para essa assinatura!<br>Espere a conclusão de uma solicitação para abrir uma nova");
        }
        
        date_default_timezone_set("America/Sao_Paulo");
        $dataAtual = date("Y-m-d", time());
        $horaAtual = date("H:i:s", time() + 60);
        
        $obEdit = null;
        
        switch ($list)
        {
            case "vai_volta":
                $obEdit = new EntityEditVaiVolta();
                $obEdit->vaiVolta = $id;
                $obEdit->destino = $postVars['destino'];
                $obEdit->data = $postVars['data'];
                $obEdit->horaSaida = $postVars['hora_saida'];
                $obEdit->horaChegada = $postVars['hora_chegada'];
                $obEdit->cadastrar();
                break;

                
            case "pernoite":
                $obEdit = new EntityEditPernoite();
                $obEdit->pernoite = $id;
                $obEdit->endereco = $postVars['endereco'];
                $obEdit->nomeResponsavel = $postVars['nome'];
                $obEdit->telefone = $postVars['telefone'];
                $obEdit->dataSaida = $postVars['data_saida'];
                $obEdit->dataChegada = $postVars['data_chegada'];
                $obEdit->horaSaida = $postVars['hora_saida'];
                $obEdit->horaChegada = $postVars['hora_chegada'];
                $obEdit->cadastrar();
                break;

                
            case "saida":
                $obEdit = new EntityEditSaida();
                $obEdit->saida = $id;
                $obEdit->destino = $postVars['destino'];
                $obEdit->dataSaida = $postVars['data_saida'];
                $obEdit->dataChegada = $postVars['data_chegada'];
                $obEdit->horaSaida = $postVars['hora_saida'];
                $obEdit->horaChegada = $postVars['hora_chegada'];
                $obEdit->cadastrar();
                break;
        }

        $ob = new EntitySolicitation();
        $ob->aluno = $_SESSION['user']['usuario']['id'];
        $ob->idLista = (int)$id;
        $ob->idEdit = is_null($obEdit) ? -1 : $obEdit->id;
        $ob->lista = $list;
        $ob->acao = $postVars['acao'];
        $ob->motivo = $postVars['motivo'];
        $ob->ativa = true;
        $ob->aprovada = false;
        $ob->dataAbertura = $dataAtual." ".$horaAtual;

        $ob->cadastrar();

        return self::getList($request, $list, $id, "Solicitação Enviada", true);
    }

    /**
     * Retorna a view segundo a lista de vai e volta
     * @param int $id
     * @return string
     */
    private static function getVaiVolta($id)
    {
        $list = EntityVaiVolta::getListById($id);

        if (is_null($list))
        {
            throw new \Exception("page not found", 404);
        }

        $aluno = EntityStudent::getStudentById($list->aluno)->nome;

        $data = explode("-", $list->data, 4);
        $data = $data[2]."/".$data[1]."/".$data[0];

        $content = View::render("pages/my_lists/vai_volta/index", [
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
    private static function getPernoite($id)
    {
        $list = EntityPernoite::getListById($id);

        if (is_null($list))
        {
            throw new \Exception("page not found", 404);
        }

        $aluno = EntityStudent::getStudentById($list->aluno)->nome;

        $dataSaida = explode("-", $list->dataSaida, 4);
        $dataSaida = $dataSaida[2]."/".$dataSaida[1]."/".$dataSaida[0];
        
        $dataChegada = explode("-", $list->dataSaida, 4);
        $dataChegada = $dataChegada[2]."/".$dataChegada[1]."/".$dataChegada[0];

        $content = View::render("pages/my_lists/pernoite/index", [
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
    private static function getSaida($id)
    {
        $list = EntitySaida::getListById($id);

        if (is_null($list))
        {
            throw new \Exception("page not found", 404);
        }

        $aluno = EntityStudent::getStudentById($list->aluno)->nome;

        $dataSaida = explode("-", $list->dataSaida, 4);
        $dataSaida = $dataSaida[2]."/".$dataSaida[1]."/".$dataSaida[0];
        
        $dataChegada = explode("-", $list->dataSaida, 4);
        $dataChegada = $dataChegada[2]."/".$dataChegada[1]."/".$dataChegada[0];

        $content = View::render("pages/my_lists/saida/index", [
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
    private static function getActions($uri)
    {
        if (str_contains($uri, "encerred"))
        {
            return "";
        }

        return View::render("pages/my_lists/actions");
    }

    /**
     * Retorna a div modal do formulário de edição da lista vai e volta
     * @param int $id
     */
    private static function getVaiVoltaEditModal($id)
    {
        $ob = EntityVaiVolta::getListById($id);

        return View::render("pages/my_lists/vai_volta/edit_modal", [
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
        $ob = EntityPernoite::getListById($id);

        return View::render("pages/my_lists/pernoite/edit_modal", [
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
        $ob = EntitySaida::getListById($id);

        return View::render("pages/my_lists/saida/edit_modal", [
            "destino" => $ob->destino,
            "data_saida" => $ob->dataSaida,
            "data_chegada" => $ob->dataChegada,
            "hora_saida" => $ob->horaSaida,
            "hora_chegada" => $ob->horaChegada
        ]);
    }
}

?>