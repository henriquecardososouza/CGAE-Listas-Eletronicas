<?php

namespace App\Utils\Filter\Admin;

class SolicitationConfig
{
    /**
     * Define os filtros disponíveis e seus respectivos valores default
     * @var array
     */
    public static $filters = [
        "list" => [
            "value" => "null",
            "name" => "list",
            "name_oficial" => "Lista"
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

        "acao" => [
            "value" => "null",
            "name" => "acao",
            "name_oficial" => "Ação"
        ],

        "sexo" => [
            "value" => "null",
            "name" => "sexo",
            "name_oficial" => "Sexo"
        ]
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