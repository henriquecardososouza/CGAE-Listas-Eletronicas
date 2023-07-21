<?php

namespace App\Controller\Error;

use App\Utils\View;

class Error500 extends Base
{
    /**
     * Retorna a view da página de erro 500
     * @return string
     */
    public static function getError500()
    {
        $content = View::render("error/error_500");

        return parent::getPage($content);
    }
}

?>