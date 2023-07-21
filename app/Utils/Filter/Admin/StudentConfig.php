<?php

namespace App\Utils\Filter\Admin;

class StudentConfig
{
    /**
     * Define os filtros disponíveis e seus respectivos valores default
     * @var array
     */
    public static $filters = [
        "serie" => [
            "value" => "null",
            "name" => "serie",
            "name_oficial" => "Série"
        ],

        "sexo" => [
            "value" => "null",
            "name" => "sexo",
            "name_oficial" => "Sexo"
        ],

        "quarto" => [
            "value" => "null",
            "name" => "quarto",
            "name_oficial" => "Quarto"
        ],

        "estado" => [
            "value" => "null",
            "name" => "estado",
            "name_oficial" => "Estado"
        ]
    ];
    
    /**
     * Define os filtros de ordenação disponíveis e seus respectivos valores default
     * @var array
     */
    public static $orders = [
        "order" => [
            "nome",
            "refeitorio"
        ],

        "way" => [
            "crescente",
            "decrescente"
        ]
    ];
}

?>