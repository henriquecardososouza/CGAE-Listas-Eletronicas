<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Page
{
    /**
     * Todos os módulos do painel
     * @var array
     */
    protected static $modules = [
        "home" => true,
        "signatures" => false,
        "solicitations" => false,
        "students" => false,
        "profile" => false
    ];

    /**
     * Retorna o conteúdo da página base
     * @param string $title
     * @param string $content
     * @param string $renderNavbar
     * @return string
     */
    public static function getPage($title, $content, $renderNavbar = true, $active = false)
    {
        return View::render("admin/base/page", [
            "title"   => $title,
            "header"  => $renderNavbar ? self::getHeader() : "",
            "content" => $content,
            "footer"  => self::getFooter()
        ]);
    }

    /**
     * Renderiza o topo da página
     * @return string
     */
    private static function getHeader()
    {
        \App\Session\Login::init();

        $content = [
            "user" => $_SESSION['user']['usuario']['nome']
        ];

        foreach (self::$modules as $name => $value)
        {
            $content['active-'.$name] = $value ? "active" : "";
        }
        
        return View::render("admin/base/header", $content);
    }

    /**
     * Renderiza o rodapé da página
     * @return string
     */
    private static function getFooter()
    {
        return View::render("admin/base/footer");
    }

    /**
     * Configura a navbar para o módulo especificado
     * @param string $module
     */
    protected static function configNavbar($module)
    {
        $aux = self::$modules;

        foreach ($aux as $key => $value)
        {
            if ($key == $module)
            {
                self::$modules[$key] = true;
                continue;
            }

            self::$modules[$key] = false;
        }
    }    

    /**
     * Retorna os objetos de paginação
     * @param Request $request
     * @return string
     */
    protected static function getPagination($request, $obPagination)
    {
        $pages = $obPagination->getPages();
        
        if (count($pages) <= 1) return "";

        $links = "";

        $url = $request->getRouter()->getCurrentUrl();

        $queryParams = $request->getQueryParams();

        $previous = "<span aria-hidden='true'>&laquo;</span>";
        $next = "<span aria-hidden='true'>&raquo;</span>";
        $current = -1;

        foreach ($pages as $page)
        {
            if ($page['current'])
            {
                $current = $page['page'];
            }
        }

        $aux = [];

        if (isset($pages[$current - 1]) && $current - 1 > 0)
        {
            $aux[] = $pages[$current - 1];
            
            $queryParams['page'] = $pages[$current - 1]['page'];

            $links .= View::render("admin/pagination/link", [
                "page" => $previous,
                "link" => $url."?".http_build_query($queryParams),
                "active" => $pages[$current - 1]['current'] ? "active" : ""
            ]);
        }

        $aux[] = $pages[$current];
        $nextLink = "";
        
        if (isset($pages[$current + 1]))
        {
            $aux[] = $pages[$current + 1];

            $queryParams['page'] = $pages[$current + 1]['page'];

            $nextLink = View::render("admin/pagination/link", [
                "page" => $next,
                "link" => $url."?".http_build_query($queryParams),
                "active" => $pages[$current + 1]['current'] ? "active" : ""
            ]);
        }
        
        $pages = $aux;

        foreach ($pages as $page)
        {
            $queryParams['page'] = $page['page'];
            $link = $url."?".http_build_query($queryParams);

            $links .= View::render("admin/pagination/link", [
                "page" => $page['page'],
                "link" => $link,
                "active" => $page['current'] ? "active" : ""
            ]);
        }

        $links .= $nextLink;

        return View::render("admin/pagination/index", [
            "links" => $links
        ]);
    }
}