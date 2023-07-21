<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity;
use \App\Session;

class Login extends Page
{
    /**
     * Retorna a View da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getLogin($request, $errorMessage = null)
    {
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : "";
        
        $content = View::render("pages/login", [
            "status" => $status
        ]);

        return parent::getPage("Entrar", $content, false);
    }

    /**
     * Realiza o login
     * @param Request $request
     */
    public static function setLogin($request)
    {
        $postVars = $request->getPostVars();

        if (!isset($postVars['check']))
        {
            $postVars['check'] = "off";
        }

        $type = "";
        $ob = null;

        $obStudent = Entity\Student::getStudentByEmail($postVars['email']);

        if (!$obStudent instanceof Entity\Student || !password_verify($postVars['senha'], $obStudent->senha))
        {
            $obAdmin = Entity\Admin::getAdminByEmail($postVars['email']);

            if (!$obAdmin instanceof Entity\Admin || !password_verify($postVars['senha'], $obAdmin->senha))
            {
                return self::getLogin($request, "Usuário ou senha inválidos!");
            }

            else 
            {
                $ob = $obAdmin;
                $type = "admin";
            }
        }

        else
        {
            $ob = $obStudent;
            $type = "student";
        }

        $postVars['id'] = $ob->id;

        Session\Login::login($ob, $type);

        if ($postVars['check'] == "on")
        {
            self::setCookies($postVars, $type);
        }

        if ($type == "student")
        {
            $request->getRouter()->redirect("/");
        }
        
        else 
        {
            $request->getRouter()->redirect("/admin");
        }
    }

    /**
     * Configura os cookies de login
     * @param array $postVars
     * @param string $type
     */
    private static function setCookies($postVars, $type)
    {
        date_default_timezone_set("America/Sao_Paulo");
        
        setcookie("user", null, time() - 100);
        setcookie("id", null, time() - 100);
        setcookie("nome", null, time() - 100);
        setcookie("email", null, time() - 100);

        setcookie("user", $type, time() + getenv('LOGIN_TIME'));
        setcookie("id", $postVars['id'], time() + getenv('LOGIN_TIME'));
        setcookie("nome", $postVars['nome'], time() + getenv('LOGIN_TIME'));
        setcookie("email", $postVars['email'], time() + getenv('LOGIN_TIME'));
    }

    /**
     * Desconecta o usuário
     */
    public static function setLogout($request)
    {
        Session\Login::Logout();
        $request->getRouter()->redirect("/login");
    }
}