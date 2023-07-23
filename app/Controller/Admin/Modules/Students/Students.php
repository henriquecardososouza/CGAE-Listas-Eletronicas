<?php

namespace App\Controller\Admin\Modules\Students;

use App\Controller\Admin\Page;
use App\Utils\Database\Pagination;
use App\Utils\View;
use App\Model\Entity\Student as EntityStudent;
use App\Utils\Filter\Admin\StudentConfig;

class Students extends Page
{
    /**
     * Retorna a view da página de estudantes cadastrados
     * @param Request $request
     * @return string
     */
    public static function getStudents($request)
    {
        parent::configNavbar("students");

        $content = View::render("admin/modules/students/students/index", [
            "filters" => self::getFilters($request),
            "table" => self::getTable($request, $obPagination),
            "pagination" => parent::getPagination($request, $obPagination)
        ]);

        return parent::getPage("Alunos", $content);
    }

    /**
     * Retorna os objetos de filtro
     * @param Request $request
     * @return string
     */
    private static function getFilters($request)
    {
        $content = View::render("admin/modules/students/students/filters/index", [
            "filter" => self::getFilter($request, $search),
            "order" => self::getOrder($request),
            "search" => $search
        ]);

        return $content;
    }

    /**
     * Retorna os filtros ativos
     * @param Request $request
     * @param string $search
     * @return string
     */
    private static function getFilter($request, &$search)
    {
        $actives = "";
        $arr = [];
        $search = null;

        $index = [
            "quarto" => 0,
            "serie" => 0,
            "sexo" => -1,
            "estado" => -1,
        ];

        if ($request->getHttpMethod() == "POST")
        {
            $postVars = $request->getPostVars();

            if (!empty($postVars['busca']))
            {
                $search = $postVars['busca'];
            }

            foreach (StudentConfig::$filters as $item)
            {
                if (isset($postVars[$item['name']]) && !empty($postVars[$item['name']]) && $postVars[$item['name']] != "null")
                {
                    $actives .= View::render("admin/modules/students/students/filters/filter/active_card", [
                        "name" => $item['name'],
                        "name_oficial" => $item['name_oficial']
                    ]);

                    switch ($item['name'])
                    {
                        case "quarto":
                            if (!is_numeric($postVars['quarto']))
                            {
                                break;
                            }

                            $quarto = $postVars[$item['name']];

                            $bloco = floor($quarto / 10);
                            $numQuarto = (1 - (ceil($quarto / 10) - $quarto / 10)) * 10;

                            $index['quarto'] = round((($bloco - 1) * 4) + ($numQuarto - 1) + 1);
                            
                            break;

                        case "serie":
                            if (!is_numeric($postVars['serie']))
                            {
                                break;
                            }

                            $index['serie'] = round((int)$postVars['serie']);
                            
                            break;
                    
                        case "sexo":
                            if ($postVars['sexo'] == "masculino")
                            {
                                $index['sexo'] = 0;
                            }

                            else
                            {
                                $index['sexo'] = 1;
                            }

                            break;

                        case "estado":
                            if ($postVars['estado'] == "ativo")
                            {
                                $index['estado'] = 2;
                            }

                            else
                            {
                                $index['estado'] = 3;
                            }

                            break;
                    }
                }
            }
        }
        
        for ($i = 0; $i < 13; $i++)
        {
            if ($i == $index['quarto'])
            {
                $arr["selected-".$i] = "selected";
            }

            else
            {
                $arr["selected-".$i] = "";
            }
        }

        for ($i = 0; $i < 4; $i++)
        {
            if ($i == $index['serie'])
            {
                $arr["selected-0".$i] = "selected";
            }

            else
            {
                $arr["selected-0".$i] = "";
            }
        }

        for ($i = 0; $i < 2; $i++)
        {
            if ($i == $index['sexo'])
            {
                $arr["checked-".$i] = "checked";
            }

            else
            {
                $arr["checked-".$i] = "";
            }
        }

        for ($i = 2; $i < 4; $i++)
        {
            if ($i == $index['estado'])
            {
                $arr["checked-".$i] = "checked";
            }

            else
            {
                $arr["checked-".$i] = "";
            }
        }

        $itens = [
            "actives" => empty($actives) ? "" : View::render("admin/modules/students/students/filters/filter/title", [
                "itens" => $actives
            ])
        ];

        foreach ($arr as $key => $value)
        {
            $itens[$key] = $value;
        }
        
        $content = View::render("admin/modules/students/students/filters/filter/index", $itens);

        return $content;
    }

    /**
     * Retorna a ordem ativa
     * @param Request $request
     * @return string
     */
    private static function getOrder($request)
    {
        $itens = [
            "active" => null
        ];

        if ($request->getHttpMethod() == "POST")
        {
            $postVars = $request->getPostVars();

            if (isset($postVars['order']) && isset($postVars['way']))
            {
                $itens['active'] = View::render("admin/modules/students/students/filters/order_by/title", [
                    "item" => View::render("admin/modules/students/students/filters/order_by/active_card", [
                        "name" => ($postVars['order'] == "nome") ? "Nome" : "N° Refeitório"
                    ])
                ]);

                if ($postVars['order'] != "nome")
                {
                    $itens['checked-0'] = "checked";
                    $itens['checked-1'] = "";
                }

                else
                {
                    $itens['checked-0'] = "";
                    $itens['checked-1'] = "checked";
                }

                if ($postVars['way'] == "crescente")
                {
                    $itens['checked-00'] = "checked";
                    $itens['checked-01'] = "";
                }

                else
                {
                    $itens['checked-00'] = "";
                    $itens['checked-01'] = "checked";
                }
            }
        }

        $content = View::render("admin/modules/students/students/filters/order_by/index", $itens);

        return $content;
    }

    /**
     * Retorna a tabela de resultados
     * @param Request $request
     * @param Pagination $obPagination
     * @return string|null
     */
    private static function getTable($request, &$obPagination)
    {
        $lines = self::getTableLines($request, $obPagination);
        
        if (is_null($lines))
        {
            $content = View::render("admin/modules/students/students/no_itens");
        }
        
        else
        {
            $content = View::render("admin/modules/students/students/table", [
                "lines" => $lines
            ]);
        }

        return $content;
    }

    /**
     * Retorna as linhas da tabela de resultados
     * @param Request $request
     * @param Pagination $obPagination
     * @return string|null
     */
    private static function getTableLines($request, &$obPagination)
    {
        $content = null;
        $where = "";
        $order = "nome";
        $busca = "";

        if ($request->getHttpMethod() == "POST")
        {
            $postVars = $request->getPostVars();
            $busca = $postVars['busca'] ?? "";
            $ANDNecessary = false;

            foreach ($postVars as $key => $value)
            {
                if ($key == "busca")
                {
                    continue;
                }

                if ($key == "order")
                {
                    if (isset($postVars['way']))
                    {
                        $order = $value;

                        if ($postVars['way'] == "decrescente")
                        {
                            $order .= " DESC";
                        }
                    }
                }

                else if ($value != "null" && $key != "way")
                {
                    if ($key == "estado")
                    {
                        $key = "ativo";
                        $value = ($value == "ativo" ? "TRUE" : "FALSE"); 
                    }

                    if ($ANDNecessary)
                    {
                        $where .= " AND ".$key." = ".($key == "sexo" ? "'" : "").$value.($key == "sexo" ? "'" : "");
                    }

                    else
                    {
                        $where .= $key." = ".($key == "sexo" ? "'" : "").$value.($key == "sexo" ? "'" : "");
                        $ANDNecessary = true;
                    }
                }
            }
        }

        $students = EntityStudent::processData(EntityStudent::getStudents($where, $order)) ?? [];

        if (!empty($busca))
        {
            $busca = strtolower($busca);
            $aux = [];

            for ($i = 0; $i < count($students); $i++)
            {
                if (str_contains(strtolower($students[$i]->nome), $busca))
                {
                    $aux[] = $students[$i];
                }
            }

            $students = $aux;
        }

        if (!empty($students))
        {
            $currentPage = $request->getQueryParams()['page'] ?? 1;
            $obPagination = new Pagination(count($students), $currentPage, 10);

            $aux = [];

            for ($i = ($currentPage - 1) * $obPagination->getLimit(); $i < ($currentPage - 1) * $obPagination->getLimit() + $obPagination->getLimit(); $i++)
            {
                if (isset($students[$i]))
                {
                    $aux[] = $students[$i];
                }
            }

            $students = $aux;
            $content = "";

            foreach ($students as $item)
            {
                $content .= View::render("admin/modules/students/students/table_line", [
                    "id" => $item->id,
                    "nome" => $item->nome,
                    "sexo" => ucfirst($item->sexo),
                    "id_refeitorio" => $item->idRefeitorio,
                    "quarto" => str_split($item->quarto)[0].".".str_split($item->quarto)[1],
                    "serie" => $item->serie."°"
                ]);
            }
            
        }

        else
        {
            $obPagination = new Pagination(0);
        }

        return $content;
    }
}