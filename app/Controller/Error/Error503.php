<?php

namespace App\Controller\Error;

use App\Utils\View;

class Error503 extends Base
{
    /**
     * Retorna a view da página de erro 503
     * @return string
     */
    public static function getError503()
    {
        $content = View::render("error/error_503");

        return parent::getPage($content);
    }
}

?>