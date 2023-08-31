<?php

namespace App\Controller\Student;

use \App\Utils\View;

/**
 * Controlador da página base (aluno)
 */
class Page
{
    /**
     * Define os módulos disponíveis e define se estão ativos ou não
     * @var array
     */
    private static $modules = [
        "home" => false,
        "listas" => false,
        "assinaturas" => false,
        "perfil" => false
    ];

    /**
     * Retorna o conteúdo da página base
     * @param string $title Título da página
     * @param string $content Conteúdo da página
     * @param bool $renderNavbar Define se a navbar deve ser renderizada
     * @return string View renderizada
     */
    public static function getPage($title, $content, $renderNavbar = true)
    {
        return View::render("student/base/page", [
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
        $variables = [
            "user" => explode(" ", $_SESSION['user']['usuario']['nome'])[0],
        ];

        foreach (self::$modules as $module => $value)
        {
            $variables["active-".$module] = $value ? "active" : "";
        }
        
        return View::render("student/base/header", $variables);;
    }

    /**
     * Renderiza o rodapé da página
     * @return string View renderizada
     */
    private static function getFooter()
    {
        return View::render("student/base/footer");
    }

    /**
     * Configura um novo módulo ativo
     * @param string $activeModule Novo do módulo a ser ativado
     */
    protected static function setActiveModule($activeModule)
    {
        foreach (self::$modules as $module => $value)
        {
            self::$modules[$module] = $module == $activeModule;
        }
    }
}