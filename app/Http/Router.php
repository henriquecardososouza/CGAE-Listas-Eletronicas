<?php

namespace App\Http;

use App\Controller\Error;

class Router
{
    /**
     * URL completa do sistema
     * @var string
     */
    private $url;

    /**
     * Prefixo de todas as rotas
     * @var string
     */
    private $prefix;

    /**
     * Índices de rotas
     * @var array
     */
    private $routes;

    /**
     * Instância de requisição
     * @var Request
     */
    private $request;

     /**
      * Content-Type padrão da resposta
      * @var string
      */
    private $contentType = "text/html";

    /**
     * Construtor da classe
     * @param string $url
     */
    public function __construct($url)
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * Define o prefixo das rotas
     */
    private function setPrefix()
    {
        $parseUrl = parse_url($this->url);
        $this->prefix = $parseUrl["path"] ?? "";
    }

    /**
     * Altera o valor do content-type
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Adiciona uma rota na classe
     * @param string $method
     * @param string $route
     * @param array $params
     */
    public function addRoute($method, $route, $params = [])
    {
        foreach ($params as $key => $value)
        {
            if ($value instanceof \Closure)
            {
                $params['controller'] = $value;
                unset($params[$key]);
            }
        }

        $params['middlewares'] = $params['middlewares'] ?? [];
        $params['variables'] = [];
        $patternVariable = "/{(.*?)}/";

        if (preg_match_all($patternVariable, $route, $matches))
        {
            $route = preg_replace($patternVariable, "(.*?)", $route);
            $params["variables"] = $matches[1];
        }

        $patternRoute = "/^" . str_replace("/", "\/", $route) . "$/";

        $this->routes[$patternRoute][$method] = $params;
    }

    /**
     * Define uma rota de GET
     * @param string $route
     * @param array $params
     */
    public function get($route, $params = [])
    {
        return $this->addRoute("GET", $route, $params);
    }

    /**
     * Define uma rota de POST
     * @param string $route
     * @param array $params
     */
    public function post($route, $params = [])
    {
        return $this->addRoute("POST", $route, $params);
    }

    /**
     * Define uma rota de PUT
     * @param string $route
     * @param array $params
     */
    public function put($route, $params = [])
    {
        return $this->addRoute("PUT", $route, $params);
    }

    /**
     * Define uma rota de DELETE
     * @param string $route
     * @param array $params
     */
    public function delete($route, $params = [])
    {
        return $this->addRoute("DELETE", $route, $params);
    }

    /**
     * Retorna a URI sem o prefixo
     * @return string
     */
    private function getUri()
    {
        $uri = $this->request->getUri();
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];
        return rtrim(end($xUri), "/");
    }

    /**
     * Retorna os dados da rota atual
     * @return array
     */
    private function getRoute()
    {
        $uri = $this->getUri();
        $httpMethod = $this->request->getHttpMethod();
    
        foreach ($this->routes as $patternRoute => $methods)
        {
            if (preg_match($patternRoute, $uri, $matches))
            {
                if (isset($methods[$httpMethod]))
                {
                    unset($matches[0]);

                    $keys = $methods[$httpMethod]["variables"];
                    $methods[$httpMethod]["variables"] = array_combine($keys, $matches);
                    $methods[$httpMethod]["variables"]["request"] = $this->request;

                    return $methods[$httpMethod];
                }

                else 
                {
                    throw new \Exception("method not allowed", 405);
                }
            }
        }

        throw new \Exception("page not found", 404);
    }

    /**
     * Executa a rota atual
     * @return Response
     */
    public function run()
    {
        try 
        {
            $route = $this->getRoute();

            if (!isset($route['controller']))
            {
                throw new \Exception("url could not be proccessed", 500);
            }

            $args = [];
            $reflection = new \ReflectionFunction($route['controller']);

            foreach ($reflection->getParameters() as $parameter)
            {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? "";
            }

            return (new Middlewares\Queue($route['middlewares'], $route['controller'], $args))->next($this->request);
        }

        catch (\Exception $e)
        {
            return self::getErrorPage($e);
        }
    }

    /**
     * Retorna a URL atual
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->url.$this->getUri();
    }

    /**
     * Redireciona o usuário
     * @var string $route
     */
    public function redirect($route)
    {
        $url = $this->url.$route;
        header("location: ".$url);
        exit();
    }

    /**
     * Controla a exibição das telas de erro
     * @param \Exception $e
     * @return Response
     */
    private function getErrorPage($e)
    {
        $content = null;

        switch ($e->getCode())
        {
            case 404:
                $content = Error\Error404::getError404();
                break;

            case 405:
                $content = Error\Error405::getError405();
                break;

            case 503:
                $content = Error\Error503::getError503();
                break;

            default:
                $content = Error\Error500::getError500();
                break;
        }

        return new Response($e->getCode(), $content);
    }
}

?>