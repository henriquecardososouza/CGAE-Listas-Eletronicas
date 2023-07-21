<?php

namespace App\Controller\Error;

use App\Utils\View;

class Error405 extends Base
{
    /**
     * Retorna a view da página de erro 405
     * @return string
     */
    public static function getError405()
    {
        $content = View::render("error/error_405");

        return parent::getPage($content);
    }
}

?>