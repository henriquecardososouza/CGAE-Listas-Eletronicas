<?php

namespace App\Controller\Assistant;

use App\Model\Entity\Assistente;
use App\Controller\Common\Alert;

/**
 * Controlador da página de perfil (assistente)
 */
class Profile extends Page
{
    /**
     * Retorna a view da página de perfil de assistente
     * @return string
     */
    public static function getProfile()
    {
        // CONFIGURA A NAVBAR
        parent::setActiveModule("profile");
        
        // INICIALIZA A SESSÃO
        \App\Session\Login::init();

        // RENDERIZA A VIEW
        $content = parent::render("profile", [
            "nome" => $_SESSION['user']['usuario']['nome'],
            "email" => $_SESSION['user']['usuario']['email']
        ]);

        return parent::getPage("Perfil", $content);
    }

    /**
     * Retorna a view da página de editar perfil
     * @return string
     */
    public static function getEditProfile($request, $message = null, $success = false)
    {
        parent::setActiveModule("profile");
        $content = parent::render("edit_profile", self::getAttributes($request, $message, $success));
        return parent::getPage("Editar", $content);
    }
    
    /**
     * Configura a view da página de editar perfil
     * @return string
     */
    public static function setEditProfile($request)
    {
        \App\Session\Login::init();

        $message = "";
        $success = false;
        $postVars = $request->getPostVars();

        $ob = Assistente::getAssistenteById($_SESSION['user']['usuario']['id']);

        $ob->nome = $postVars['nome'];
        $ob->email = $postVars['email'];
        $ob->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);

        $ob->atualizar();

        \App\Session\Login::login($ob);

        $message = "Atualizado com sucesso!";
        $success = true;

        $content = parent::render("edit_profile", self::getAttributes($request, $message, $success));
        return parent::getPage("Editar", $content);
    }

    /**
     * Retorna os atributos da view de editar perfil
     * @return array
     */
    private static function getAttributes($request, $message = null, $success = false)
    {
        $attr = [];
        $ob = Assistente::getAssistenteById($_SESSION['user']['usuario']['id']);

        $attr['status'] = is_null($message) ? "" : ($success ? Alert::getSuccess($message) : Alert::getError($message));
        $attr['nome'] = $ob->nome;
        $attr['email'] = $ob->email;

        return $attr;
    }
}