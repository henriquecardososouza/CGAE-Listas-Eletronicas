<?php

namespace App\Utils;

class View
{
    /**
     * Variáveis padrão da View
     * @var array
     */
    private static $vars = [];
    
    /**
     * Define os dados iniciais da classe
     * @param array $vars
     */
    public static function init($vars = [])
    {
        self::$vars = $vars;
    }

    /**
     * Retorna o conteúdo de uma View
     * @param string $view
     * @return string
     */
    private static function getContentView($view)
    {
        $file = __DIR__."/../../resources/view/"
.$view.".html";
        return file_exists($file) ? file_get_contents($file) : "";
    }

    /**
     * Retorna o conteúdo renderizado de uma view
     * @param string $view
     * @param array $vars
     * @return string
     */
    public static function render($view, $vars = []) 
    {
        $content = self::getContentView($view);
        $vars = array_merge(self::$vars, $vars);
        $keys = array_keys($vars);

        $keys = array_map(function ($item) 
        {
            return "{{".$item."}}";
        }, $keys);

        return str_replace($keys, array_values($vars), $content);
    }
}

?>