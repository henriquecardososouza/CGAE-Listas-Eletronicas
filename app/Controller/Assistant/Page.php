<?php

namespace App\Controller\Assistant;

use \App\Utils\View;

/**
 * Controlador da página base (assistente)
 */
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
     * @param string $title Título da página
     * @param string $content Conteúdo da página
     * @param string $renderNavbar Indica se a navbar deve ser renderizada na página
     * @return string View renderizada
     */
    public static function getPage($title, $content, $renderNavbar = true)
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
     * @return string View renderizada
     */
    private static function getHeader()
    {
        // INICIALIZA A SESSÃO
        \App\Session\Login::init();

        // CONFIGURA OS PARÂMETROS DE RENDERIZAÇÃO
        $itens = [
            "user" => $_SESSION['user']['usuario']['nome']
        ];

        foreach (self::$modules as $name => $value)
        {
            $itens['active-'.$name] = $value ? "active" : "";
        }
        
        // RETORNA A VIEW
        return View::render("admin/base/header", $itens);
    }

    /**
     * Renderiza o rodapé da página
     * @return string View renderizada
     */
    private static function getFooter()
    {
        return View::render("admin/base/footer");
    }

    /**
     * Configura a navbar para o módulo especificado
     * @param string $module Módulo a ser ativado
     */
    protected static function configNavbar($module)
    {
        $aux = self::$modules;

        // PROCURA O MÓDULO SOLICITADO E DESABILITA OS OUTROS MÓDULOS DISPONÍVEIS
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
     * @param Request $request Objeto de requisição
     * @return string View renderizada
     */
    protected static function getPagination($request, $obPagination)
    {
        // RECUPERA A PÁGINAS DISPONÍVEIS
        $pages = $obPagination->getPages();
        
        // VERIFICA SE EXISTE MAIS DE UMA PÁGINA
        if (count($pages) <= 1) return "";

        // OBTÉM A URL ATUAL
        $links = "";
        $url = $request->getRouter()->getCurrentUrl();
        $queryParams = $request->getQueryParams();

        // OBTÉM O ÍCONE DOS BOTÕES DE PÓXIMO E ANTERIOR
        $previous = "<span aria-hidden='true'>&laquo;</span>";
        $next = "<span aria-hidden='true'>&raquo;</span>";
        $current = -1;

        // BUSCA A PÁGINA ATUAL
        foreach ($pages as $page)
        {
            if ($page['current'])
            {
                $current = $page['page'];
            }
        }

        $aux = [];

        // RENDERIZA O BOTÃO DE ANTERIOR
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
        
        // RENDERIZA O BOTÃO DE PRÓXIMO
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

        // RENDERIZA OS BOTÕES DE PÁGINAS ESPECÍFICAS
        foreach ($pages as $page)
        {
            // CONSTRÓI O LINK DE REDIRECIONAMENTO
            $queryParams['page'] = $page['page'];
            $link = $url."?".http_build_query($queryParams);

            // RENDERIZA O BOTÃO
            $links .= View::render("admin/pagination/link", [
                "page" => $page['page'],
                "link" => $link,
                "active" => $page['current'] ? "active" : ""
            ]);
        }

        $links .= $nextLink;

        // RETORNA OS BOTÕES DE PAGINAÇÃO CONFIGURADOS NO GRID
        return View::render("admin/pagination/index", [
            "links" => $links
        ]);
    }
}