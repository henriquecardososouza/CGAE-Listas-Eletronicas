<?php

namespace App\Controller\Error;

use App\Utils\View;

class Error404 extends Base
{
    /**
     * Retorna a view da página de erro 404
     * @return string
     */
    public static function getError404()
    {
        $content = View::render("error/error_404");

        return parent::getPage($content);
    }
}

?>