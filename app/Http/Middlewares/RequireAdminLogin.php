<?php

namespace App\Http\Middlewares;

class RequireAdminLogin
{
    /**
     * Executa o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        $this->verifyLogin($request);

        return $next($request);
    }

    /**
     * Verifica se existe um login de admin
     * @param Request $request
     */
    private function verifyLogin($request)
    {
        \App\Session\Login::init();

        if (isset($_SESSION['user']['usuario']))
        {
            if ($_SESSION['user']['usuario']['type'] == "admin")
            {
                return;
            }
        }

        $request->getRouter()->redirect("/login");
    }
}

?>