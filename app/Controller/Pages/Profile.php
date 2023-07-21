<?php

namespace App\Controller\Pages;

use App\Utils\View;

class Profile extends Page
{
    /**
     * Retorna a veiw da página de perfil
     * @return string
     */
    public static function getProfile()
    {
        $ob = \App\Model\Entity\Student::getStudentById($_SESSION['user']['usuario']['id']);

        $quarto = str_split($ob->quarto, 1);
        $quarto = $quarto[0].".".$quarto[1];

        $content = View::render("pages/profile", [
            "nome" => $ob->nome,
            "email" => $ob->email,
            "quarto" => $quarto,
            "serie" => $ob->serie."° ano",
            "numero" => $ob->idRefeitorio,
            "pernoite" => $ob->pernoite ? "Sim" : "Não"
        ]);

        return parent::getPage("Perfil", $content);
    }
}

?>