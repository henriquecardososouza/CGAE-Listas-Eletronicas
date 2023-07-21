<?php

namespace App\Http\Middlewares;

class Queue
{
    /**
     * Mapeamento de middlewars
     * @var array
     */
    private static $map = [];
    
    /**
     * Mapeamento de middlewars que serão carregados em todas as rotas
     * @var array
     */
    private static $default = [];

    /**
     * Fila de middlewares a serem executados
     * @var array
     */
    private $middlewares = [];

    /**
     * Função de execução do controlador
     * @var \Closure
     */
    private $controller;

    /**
     * Argumentos da função do controlador
     * @var array
     */
    private $controllerArgs = [];

    /**
     * Constrói a classe de fila de middlewares
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct($middlewares, $controller, $controllerArgs)
    {
        $this->middlewares = array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    /**
     * Define o mapeamento de middlewares
     * @param array $maps
     */
    public static function setMap($map)
    {
        self::$map = $map;
    }

    /**
     * Define o mapeamento de middlewares padrões
     * @param array $default
     */
    public static function setDefault($default)
    {
        self::$default = $default;
    }

    /**
     * Executa o próximo nível da gila de middlewares
     * @param Request $request
     * @return Response
     */
    public function next($request)
    {
        if (empty($this->middlewares))
        {
            return call_user_func_array($this->controller, $this->controllerArgs);
        }

        $middleware = array_shift($this->middlewares);

        if (!isset(self::$map[$middleware]))
        {
            throw new \Exception("failed to proccess the middleware ".$middleware, 500);
        }

        $queue = $this;

        $next = function ($request) use ($queue)
        {
            return $queue->next($request);
        };

        return (new self::$map[$middleware])->handle($request, $next);
    }
}