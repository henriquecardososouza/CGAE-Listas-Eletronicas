<?php

namespace App\Utils;

class Environment
{
    /**
     * Carrega as variáveis de ambiente do projeto
     * @param string $dir
     */
    public static function load($dir)
    {
        if (!file_exists($dir."/.env"))
        {
            return;
        }

        $lines = file($dir."/.env");

        foreach ($lines as $line)
        {
            putenv(trim($line));
        }
    }
}