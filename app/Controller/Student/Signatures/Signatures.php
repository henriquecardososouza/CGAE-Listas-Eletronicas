<?php

namespace App\Controller\Student\Signatures;

use App\Controller\Student\Page;
use App\Model\Entity\Lists;

class Signatures extends Page
{
    public static function getContent($type)
    {
        parent::setActiveModule("assinaturas");

        $content = parent::render("modules/signatures/index", [
            "type" => $type,
            "no_itens" => self::getNoItens(),
            "not_found" => self::getNotFound(),
            "itens" => self::getItens($type)
        ]);

        return parent::getPage("Assinaturas", $content);
    }

    private static function getNoItens()
    {
        return parent::render("modules/signatures/no_itens");
    }

    private static function getNotFound()
    {
        return parent::render("modules/signatures/not_found");
    }

    private static function getItens($type)
    {
        // INICIALIZA A SESSÃO
        \App\Session\Login::init();

        // VERIFICA SE A BUSCA CORRESPONDE A LISTAS ATIVAS OU INATIVAS
        $ativa = $type == "ativas" ? "true" : "false";

        // RECUPERA OS DADOS DO BANCO
        $lists['vai_volta'] = Lists\VaiVolta::processData(Lists\VaiVolta::getSignatures("ativa = ".$ativa." AND aluno = ".$_SESSION['user']['usuario']['id']), "id DESC");
        $lists['saida'] = Lists\Saida::processData(Lists\Saida::getSignatures("ativa = ".$ativa." AND aluno = ".$_SESSION['user']['usuario']['id']), "id DESC");
        $lists['pernoite'] = Lists\Pernoite::processData(Lists\Pernoite::getSignatures("ativa = ".$ativa." AND aluno = ".$_SESSION['user']['usuario']['id']), "id DESC");    
        
        $result = "";

        foreach (array_merge($lists['vai_volta'], $lists['saida'], $lists['pernoite']) as $item)
        {
            // CONVERTE OS OBJETOS EM ARRAYS
            $arr = (array) $item;
            $keys = array_keys($arr);
            $values = array_values($arr);

            // INICIALIZA A ESCRITA DE UM NOVO OBJETO JS 
            $aux = "{";
            
            for ($i = 0; $i < count($keys); $i++)
            {
                $valueData = $values[$i];

                // FORMATA A DATA PARA O PADRÃO dd/mm/yyyy
                if (str_contains($keys[$i], "data"))
                {
                    $valueData = explode("-", $valueData, 4);
                    $valueData = $valueData[2]."/".$valueData[1]."/".$valueData[0];
                }

                // FORMATA A HORA PARA O PADRÃO hh:mm
                else if (str_contains($keys[$i], "hora"))
                {
                    $valueData = substr($valueData, 0, -3);
                }

                // ADICIONA O NOVO ATRIBUTO AO OBJETO

                if ($keys[$i] == "data")
                {
                    $aux .= "dataChegada".": '".$valueData."', ";
                    $aux .= "dataSaida".": '".$valueData."', ";
                }

                else
                {
                    $aux .= $keys[$i].": '".$valueData."', ";
                }
            }

            // ADICIONA O ATRIBUTO IDENTIFICADOR DE TIPO
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

            // FINALIZA A DECLARAÇÃO DO OBJETO E O ADICIONA AO ARRAY DE OBJETOS JS
            $aux .= "}";
            $result .= $aux.", ";
        }
        
        // RETORNA O OBJETO JS DE DADOS
        return substr($result, 0, -2);
    }
}