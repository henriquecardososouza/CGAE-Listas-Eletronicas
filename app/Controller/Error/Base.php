<?php

namespace App\Controller\Error;

use App\Utils\View;

class Base
{
    /**
     * Retorna a página base de erros
     * @param string $content
     * @return string
     */
    protected static function getPage($content)
    {
        return View::render("error/base/page", [
            "title" => "Error",
            "header" => self::getHeader(),
            "content" => $content,
            "footer" => self::getFooter()
        ]);
    }

    /**
     * Retorna o header padrão das página de erro
     * @return string
     */
    private static function getHeader()
    {
        return View::render("error/base/header");
    }

    /**
     * Retorna o footer padrão das páginas de erro
     * @return string
     */
    private static function getFooter()
    {
        return View::render("error/base/footer");
    }
}