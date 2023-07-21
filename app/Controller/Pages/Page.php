<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class Page
{
    /**
     * Retorna o conteúdo da página base
     * @param string $title
     * @param string $content
     * @param string $renderNavbar
     * @return string
     */
    public static function getPage($title, $content, $renderNavbar = true, $active = false)
    {
        return View::render("pages/base/page", [
            "title"   => $title,
            "header"  => $renderNavbar ? self::getHeader($active) : "",
            "content" => $content,
            "footer"  => self::getFooter()
        ]);
    }

    /**
     * Renderiza o topo da página
     * @param bool $active
     * @return string
     */
    private static function getHeader($active)
    {
        return View::render("pages/base/header", [
            "user" => explode(" ", $_SESSION['user']['usuario']['nome'])[0],
            "active" => $active ? "active" : ""
        ]);
    }

    /**
     * Renderiza o rodapé da página
     * @return string
     */
    private static function getFooter()
    {
        return View::render("pages/base/footer");
    }
}