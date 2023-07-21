<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\Admin;

class Edit extends Page
{
    /**
     * Retorna a view da página de editar perfil
     * @return string
     */
    public static function getEdit($request, $message = null, $success = false)
    {
        $content = View::render("admin/edit_profile", self::getAttributes($request, $message, $success));
        return parent::getPage("Editar", $content);
    }
    
    /**
     * Configura a view da página de editar perfil
     * @return string
     */
    public static function setEdit($request)
    {
        \App\Session\Login::init();

        $message = "";
        $success = false;
        $postVars = $request->getPostVars();

        $ob = Admin::getAdminById($_SESSION['user']['usuario']['id']);

        $ob->nome = $postVars['nome'];
        $ob->email = $postVars['email'];
        $ob->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);

        $ob->atualizar();

        \App\Session\Login::login($ob);

        $message = "Atualizado com sucesso!";
        $success = true;

        $content = View::render("admin/edit_profile", self::getAttributes($request, $message, $success));
        return parent::getPage("Editar", $content);
    }

    /**
     * Retorna os atributos da view de editar perfil
     * @return array
     */
    private static function getAttributes($request, $message = null, $success = false)
    {
        $attr = [];
        $ob = Admin::getAdminById($_SESSION['user']['usuario']['id']);

        $attr['status'] = is_null($message) ? "" : ($success ? Alert::getSuccess($message) : Alert::getError($message));
        $attr['nome'] = $ob->nome;
        $attr['email'] = $ob->email;

        return $attr;
    }
}

?>