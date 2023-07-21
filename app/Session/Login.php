<?php

namespace App\Session;

use App\Model\Entity;

class Login 
{
    /**
     * Iniciliza a sessão
     */
    public static function init()
    {
        if (session_status() != PHP_SESSION_ACTIVE)
        {
            session_start();
        }
    }

    /**
     * Cria o login de usuário
     * @param mixed $ob
     * @return bool
     */
    public static function login($ob)
    {
        self::init();

        if ($ob instanceof Entity\Student)
        {
            $_SESSION['user'] = [
                'usuario' => [
                    "id" => $ob->id,
                    "nome" => $ob->nome,
                    "email" => $ob->email,
                    "type" => "student"
                ]
            ];
        }
        
        else
        {
            $_SESSION['user'] = [
                'usuario' => [
                    "id" => $ob->id,
                    "nome" => $ob->nome,
                    "email" => $ob->email,
                    "type" => "admin"
                ]
            ];
        }

        return true;
    }

    /**
     * Desconecta o usuário
     */
    public static function logout()
    {
        self::init();

        unset($_SESSION['user']['usuario']);
        
        setcookie('user', null, time() - 100);
        setcookie('id', null, time() - 100);
        setcookie('nome', null, time() - 100);
        setcookie('email', null, time() - 100);

        return true;
    }
}

?>