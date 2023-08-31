<?php

namespace App\Controller\Student\Signatures;

use App\Controller\Student\Page;
use App\Model\Entity\Lists;
use App\Utils\View;

class Actives extends Page
{
    /**
     * Retorna a view da página de minhas listas ativas
     * @return string
     */
    public static function getActives()
    {
        parent::setActiveModule("assinaturas");

        $content = View::render("student/signatures/actives/index", [
            "itens" => self::getItens(),
            "no_itens" => View::render("student/signatures/actives/no_itens"),
            "not_found" => View::render("student/signatures/actives/not_found")
        ]);

        return parent::getPage("Minhas Assinaturas", $content);
    }
    
    /**
     * Retorna os cards das listas ativas
     * @return string
     */
    private static function getItens()
    {
        \App\Session\Login::init();

        $lists['vai_volta'] = Lists\VaiVolta::processData(Lists\VaiVolta::getSignatures("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']), "id DESC");
        $lists['saida'] = Lists\Saida::processData(Lists\Saida::getSignatures("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']), "id DESC");
        $lists['pernoite'] = Lists\Pernoite::processData(Lists\Pernoite::getSignatures("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']), "id DESC");    
        
        $result = "";

        foreach (array_merge($lists['vai_volta'], $lists['saida'], $lists['pernoite']) as $item)
        {
            $arr = (array) $item;
            $keys = array_keys($arr);
            $values = array_values($arr);

            $aux = "{";
            
            for ($i = 0; $i < count($keys); $i++)
            {
                $valueData = $values[$i];

                if (str_contains($keys[$i], "data"))
                {
                    $valueData = explode("-", $valueData, 4);
                    $valueData = $valueData[2]."/".$valueData[1]."/".$valueData[0];
                }

                else if (str_contains($keys[$i], "hora"))
                {
                    $valueData = substr($valueData, 0, -3);
                }

                $aux .= $keys[$i].": '".$valueData."', ";
            }

            if (isset($arr["data"]))
            {
                $aux .= "type: 'vai_volta'";
            }

            else if (isset($arr['endereco']))
            {
                $aux .= "type: 'pernoite'";
            }

            else
            {
                $aux .= "type: 'saida'";
            }

            $aux .= "}";

            $result .= $aux.", ";
        }
        
        return substr($result, 0, -2);
    }
}