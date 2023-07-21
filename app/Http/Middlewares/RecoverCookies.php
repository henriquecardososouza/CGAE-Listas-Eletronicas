<?php

namespace App\Http\Middlewares;

class RecoverCookies
{
    /**
     * Executa o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        if ($this->verifyCookies()) 
        {
            $this->recoverCookies();
        }

        return $next($request);
    }

    /**
     * Verifica se existem cookies de login
     */
    private function verifyCookies()
    {
        return isset($_COOKIE['user']);
    }

    /**
     * 
     */
    private function recoverCookies()
    {
        \App\Session\Login::init();

        if ($_COOKIE['user'] == "student")
        {
            $obStudent = \App\Model\Entity\Student::getStudentById($_COOKIE['id']);

            $_SESSION['user'] = [
                'usuario' => [
                    'id' => $obStudent->id,
                    'nome' => $obStudent->nome,
                    'email' => $obStudent->email,
                    "type" => "student"
                ]
            ];
        }

        else 
        {
            $obAdmin = \App\Model\Entity\Admin::getAdminById($_COOKIE['id']);
            
            $_SESSION['user'] = [
                'usuario' => [
                    'id' => $obAdmin->id,
                    'nome' => $obAdmin->nome,
                    'email' => $obAdmin->email,
                    "type" => "admin"
                ]
            ];
        }
    }
}

?>