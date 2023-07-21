<?php

namespace App\Utils\Filter;

class ListConfig
{
    /**
     * Define os filtros disponíveis e seus respectivos valores default
     * @var array
     */
    public static $filters = [
        "vai_volta" => [
            "value" => "off",
            "name" => "vai_volta",
            "name_oficial" => "Vai e Volta"
        ],

        "pernoite" => [
            "value" => "off",
            "name" => "pernoite",
            "name_oficial" => "Pernoite"
        ],

        "saida" => [
            "value" => "off",
            "name" => "saida",
            "name_oficial" => "Saída"
        ],
        
        "data_initial" => [
            "value" => "null",
            "name" => "data",
            "name_oficial" => "Data"
        ],
        
        "data_final" => [
            "value" => "null",
            "name" => "data",
            "name_oficial" => "Data"
        ],
    ];
    
    /**
     * Define os filtros de ordenação disponíveis e seus respectivos valores default
     * @var array
     */
    public static $orders = [
        "order" => [
            "data",
            "nome"
        ],

        "way" => [
            "crescente",
            "decrescente"
        ]
    ];
}

?>