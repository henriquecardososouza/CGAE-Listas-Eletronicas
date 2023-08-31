<?php

namespace App\Controller\Admin\Modules\Signatures;

use App\Utils\View;
use App\Utils\Database\Pagination;
use App\Controller\Admin\Page;
use App\Model\Entity\Listas;
use App\Model\Entity\Student;
use App\Utils\Filter\Admin\ListConfig;

class Lists extends Page
{
    /**
     * Retorna a view da página de consulta de listas
     * @param Request $request
     * @return string
     */
    public static function getLists($request)
    {
        parent::configNavbar("signatures");

        $content = View::render("admin/modules/lists/index", 
        [
            "filters" => self::getFilters($request),
            "table" => self::getTable($request, $obPagination),
            "pagination" => parent::getPagination($request, $obPagination)
        ]);

        return parent::getPage("Listas", $content);
    }

    /**
     * Configura a view da página de consultas de listas
     * @param Request $request
     * @return string
     */
    public static function setLists($request)
    {
        parent::configNavbar("signatures");
        
        $content = View::render("admin/modules/lists/index", [
            "filters" => self::getFilters($request),
            "table" => self::getTable($request, $obPagination),
            "pagination" => parent::getPagination($request, $obPagination)
        ]);

        return parent::getPage("Listas", $content);
    }

    /**
     * Retorna os objetos html de filtros
     * @param Request $request
     * @return string
     */
    private static function getFilters($request)
    {
        $content = [
            "search" => isset($request->getPostVars()['busca']) ? $request->getPostVars()['busca'] : "",
            "filter" => self::getFilter($request),
            "order" => self::getOrder($request)
        ];

        return View::render("admin/modules/lists/filters/index", $content);
    }

    /**
     * Retorna os objetos de filtros de busca
     * @param Request $request
     * @return string
     */
    private static function getFilter($request)
    {
        $filters = array_keys(ListConfig::$filters);
        $values = array_values(ListConfig::$filters);

        $postVars = $request->getPostVars();


        for ($i = 0; $i < count($filters); $i++)
        {
            if (!isset($postVars[$filters[$i]]) || empty($postVars[$filters[$i]]))
            {
                $postVars[$filters[$i]] = $values[$i]['value'];
            }
        }
        
        $filtersCount = 0;

        foreach ($postVars as $key => $value)
        {
            foreach ($filters as $filter)
            {
                if ($key == $filter)
                {
                    if ($value == "off" || $value == "null")
                    {
                        $filtersCount++;
                    }
                }
            }
        }

        $blockActives = false;

        if ($filtersCount >= count($filters))
        {
            $actives = "";
            $blockActives = true;
        }
        
        $checked = [];

        for ($i = 0; $i < count($filters); $i++)
        {
            if ($postVars[$filters[$i]] == "off")
            {
                $checked[] = "";
            }

            else if ($filters[$i] == "estado")
            {
                if ($postVars[$filters[$i]] == "null")
                {
                    $checked[] = "";
                    $checked[] = "";
                }

                else
                {
                    if ($postVars['estado'] == "ativo")
                    {
                        $checked[] = "checked";
                        $checked[] = "";
                    }
                    
                    else
                    {
                        $checked[] = "";
                        $checked[] = "checked";
                    }
                }
            }

            else if ($filters[$i] == "sexo")
            {
                if ($postVars[$filters[$i]] == "null")
                {
                    $checked[] = "";
                    $checked[] = "";
                }

                else
                {
                    if ($postVars['sexo'] == "masculino")
                    {
                        $checked[] = "checked";
                        $checked[] = "";
                    }
                    
                    else
                    {
                        $checked[] = "";
                        $checked[] = "checked";
                    }
                }
            }

            else if ($filters[$i] != "data_initial" && $filters[$i] != "data_final" && $filters[$i] != "hour_initial" && $filters[$i] != "hour_final")
            {
                $checked[] = "checked";
            }
        }

        $dateValues = [];

        if ($postVars['data_initial'] == "null")
        {
            $dateValues['date_initial'] = "";
        }

        else 
        {
            $dateValues['date_initial'] = $postVars['data_initial'];
        }

        if ($postVars['data_final'] == "null")
        {
            $dateValues['date_final'] = "";
        }

        else 
        {
            $dateValues['date_final'] = $postVars['data_final'];
        }
        
        $hourValues = [];

        if ($postVars['hour_initial'] == "null")
        {
            $hourValues['hour_initial'] = "";
        }

        else 
        {
            $hourValues['hour_initial'] = $postVars['hour_initial'];
        }

        if ($postVars['hour_final'] == "null")
        {
            $hourValues['hour_final'] = "";
        }

        else 
        {
            $hourValues['hour_final'] = $postVars['hour_final'];
        }

        $itens = "";
        $filterActives = 0;
        $dataAlreadyIn = false;
        $hourAlreadyIn = false;

        for ($i = 0; $i < count($filters); $i++)
        {
            if ($postVars[$filters[$i]] == "on" 
                || ($postVars[$filters[$i]] != "null" && $values[$i]['name'] == "estado") 
                || ($postVars[$filters[$i]] != "null" && $values[$i]['name'] == "sexo")
                || ($postVars[$filters[$i]] != "null" && $values[$i]['name'] == "data" && !$dataAlreadyIn)
                || ($postVars[$filters[$i]] != "null" && $values[$i]['name'] == "hour" && !$hourAlreadyIn))
            {
                if ($values[$i]['name'] == "data")
                {
                    $dataAlreadyIn = true;
                }

                else if ($values[$i]['name'] == "hour")
                {
                    $hourAlreadyIn = true;
                }

                $itens .= View::render("admin/modules/lists/filters/filter/active_card", [
                    "name_oficial" => $values[$i]['name_oficial'],
                    "name" => $values[$i]['name']
                ]);

                $filterActives++;
            }
        }

        if (!$blockActives)
        {
            $actives = View::render("admin/modules/lists/filters/filter/title", [
                "itens" => $itens
            ]);
        }

        $params = [
            "actives" => (!empty($actives) ? "<hr>" : "").$actives
        ];

        for ($i = 0; $i < count($checked); $i++)
        {
            $params["checked-".$i] = $checked[$i];
        }

        foreach ($dateValues as $date => $value)
        {
            $params[$date] = $value;
        }
        
        foreach ($hourValues as $hour => $value)
        {
            $params[$hour] = $value;
        }

        return View::render("admin/modules/lists/filters/filter/index", $params);
    }

    /**
     * Retorna os objetos de ordem ativos
     * @param Request $request
     * @return string
     */
    private static function getOrder($request)
    {
        $postVars = $request->getPostVars();
        $order = $postVars['order'] ?? null;
        $way = $postVars['way'] ?? null;

        $order = is_null($way) ? null : $order;
        $way = is_null($order) ? null : $way;

        $active = "";
        $idOrder = -1;
        $idWay = -1;

        for ($i = 0; $i < count(ListConfig::$orders['order']); $i++)
        {
            if (ListConfig::$orders['order'][$i] == $order)
            {
                $idOrder = $i;
                break;
            }
        }
        
        for ($i = 0; $i < count(ListConfig::$orders['way']); $i++)
        {
            if (ListConfig::$orders['way'][$i] == $way)
            {
                $idWay = $i;
                break;
            }
        }

        if (!is_null($order))
        {
            $active = "<hr>".View::render("admin/modules/lists/filters/order_by/title", [
                "item" => View::render("admin/modules/lists/filters/order_by/active_card", [
                    "name" => ucfirst($order)
                ])
            ]);
        }

        $item = [
            "active" => $active
        ];

        for ($i = 0; $i < count(ListConfig::$orders['order']); $i++)
        {
            if ($i == $idOrder)
            {
                $item["checked-".$i] = "checked";
            }

            else 
            {
                $item["checked-".$i] = "";
            }
        }

        for ($i = 0; $i < count(ListConfig::$orders['way']); $i++)
        {
            if ($i == $idWay)
            {
                $item["checked-0".$i] = "checked";
            }

            else 
            {
                $item["checked-0".$i] = "";
            }
        }

        $content = View::render("admin/modules/lists/filters/order_by/index", $item);

        return $content;
    }
    
    /**
     * Retorna os resultados da busca
     * @param Request $request
     * @return string
     */
    private static function getItens($request, &$obPagination)
    {
        $content = "";
        $postVars = $request->getPostVars();
        $search = isset($postVars['busca']) ? $postVars['busca'] : null;
        $filters = [];
        $values = [];
        $order = [];

        if (!is_null($search))
        {
            $search = strtolower($search);
        }

        foreach (ListConfig::$filters as $filter => $data)
        {
            if (!isset($postVars[$filter]) || empty($postVars[$filter]))
            {
                $postVars[$filter] = $data['value'];
            }

            $filters[] = $filter;
            $values[] = $data['value'];
        }

        if (isset($postVars['order']) && isset($postVars['way']))
        {
            $order = [
                $postVars['order'] => $postVars['way']
            ];
        }

        $renderLists = [
            "vai_volta" => false,
            "saida" => false,
            "pernoite" => false,
        ];

        if ($postVars['vai_volta'] == "off" && $postVars['saida'] == "off" && $postVars['pernoite'] == "off")
        {
            foreach ($renderLists as $key => $value)
            {
                $renderLists[$key] = true;
            }
        }

        else 
        {
            if ($postVars['vai_volta'] == "on")
            {
                $renderLists['vai_volta'] = true;
            }
            
            if ($postVars['saida'] == "on")
            {
                $renderLists['saida'] = true;
            }
            
            if ($postVars['pernoite'] == "on")
            {
                $renderLists['pernoite'] = true;
            }
        }
        
        $listas = [
            $renderLists['vai_volta'] ? Listas\VaiVolta::processData(Listas\VaiVolta::getLists(null, "id DESC")) : [],
            $renderLists['saida'] ? Listas\Saida::processData(Listas\Saida::getLists(null, "id DESC")) : [],
            $renderLists['pernoite'] ? Listas\Pernoite::processData(Listas\Pernoite::getLists(null, "id DESC")) : []
        ];

        $ob = array_merge($listas[0], $listas[1], $listas[2]);

        if ($postVars['data_initial'] != "null")
        {
            $aux = [];
            $dataMin = $postVars['data_initial'];
            $dataMax = $postVars['data_final'];

            foreach ($ob as $item)
            {
                $data = $item instanceof Listas\VaiVolta ? $item->data : $item->dataSaida;
                
                if ($data >= $dataMin && $data <= $dataMax)
                {
                    $aux[] = $item;
                }
            }

            $ob = $aux;
        }
        
        if ($postVars['hour_initial'] != "null")
        {
            $aux = [];
            $hourMin = $postVars['hour_initial'];
            $hourMax = $postVars['hour_final'];

            foreach ($ob as $item)
            {
                $hour = $item->horaSaida;
                
                if ($hour >= $hourMin && $hour <= $hourMax)
                {
                    $aux[] = $item;
                }
            }

            $ob = $aux;
        }

        if ($postVars['estado'] != "null")
        {
            $aux = [];
            $ativa = false;

            switch ($postVars['estado'])
            {
                case "ativo":
                    $ativa = true;
                    break;

                case "finalizado":
                    $ativa = false;
                    break;
            }
            
            foreach ($ob as $item)
            {
                if ($item->ativa == $ativa)
                {
                    $aux[] = $item;
                }
            }

            $ob = $aux;
        }

        if ($postVars['sexo'] != "null")
        {
            $aux = [];

            switch ($postVars['sexo'])
            {
                case "masculino":
                    foreach ($ob as $item)
                    {
                        $sexo = Student::getStudentById($item->aluno)->sexo;

                        if ($sexo == "masculino")
                        {
                            $aux[] = $item;
                        }
                    }

                    break;

                case "feminino":
                    foreach ($ob as $item)
                    {
                        $sexo = Student::getStudentById($item->aluno)->sexo;

                        if ($sexo == "feminino")
                        {
                            $aux[] = $item;
                        }
                    }

                    break;
            }
            
            $ob = $aux;
        }

        if (!is_null($search))
        {
            $aux = [];

            foreach ($ob as $item)
            {
                $nome = strtolower(Student::getStudentById($item->aluno)->nome);

                if (str_contains($nome, $search))
                {
                    $aux[] = $item;
                }
            }

            $ob = $aux;
        }

        if (!empty($order) && !empty($ob))
        {
            $obOrdered = [];

            switch (array_keys($order)[0])
            {
                case "data":
                    $aux = [];
                    $id = 0;

                    foreach ($ob as $item)
                    {
                        $aux[] = [
                            "data" => $item instanceof Listas\VaiVolta ? $item->data : $item->dataSaida,
                            "id" => $id
                        ];

                        $id++;
                    }

                    do
                    {
                        $element = array_shift($aux);
                        $add = true;

                        for ($j = 0; $j < count($aux); $j++)
                        {
                            if ($element['data'] > $aux[$j]['data'])
                            {
                                $aux[] = $element;
                                $add = false;
                                break;
                            }
                        }

                        if ($add)
                        {
                            $obOrdered[] = $ob[$element['id']];
                        }
                    } while (count($obOrdered) < count($ob));

                    break;

                default:
                    $obOrdered = $ob;
                    break;
            }

            if ($order[array_keys($order)[0]] == "decrescente")
            {
                $aux = $obOrdered;
                $obOrdered = [];

                for ($i = count($aux) - 1; $i > -1; $i--)
                {
                    $obOrdered[] = $aux[$i];
                }
            }

            $ob = $obOrdered;
        }

        if (empty($ob))
        {
            $content = null;
            $obPagination = new Pagination(count($ob), 1, 0);
        }

        else 
        {
            $currentPage = $request->getQueryParams()['page'] ?? 1;
            $obPagination = new Pagination(count($ob), $currentPage, 10);

            $aux = [];

            for ($i = ($currentPage - 1) * $obPagination->getLimit(); $i < ($currentPage - 1) * $obPagination->getLimit() + $obPagination->getLimit(); $i++)
            {
                if (isset($ob[$i]))
                {
                    $aux[] = $ob[$i];
                }
            }

            $ob = $aux;
        }

        if (empty($ob))
        {
            $content = null;
            $obPagination = new Pagination(count($ob), 1, 0);
        }

        foreach ($ob as $obList)
        {
            $lista = null;
            $name = null;
            $data = null;

            if ($obList instanceof Listas\VaiVolta)
            {
                $lista = "Vai e Volta";
                $name = "vai_volta";
                $data = $obList->data;
            }

            else if ($obList instanceof Listas\Saida)
            {
                $lista = "Saída";
                $name = "saida";
                $data = $obList->dataSaida;
            }

            else
            {
                $lista = "Pernoite";
                $name = "pernoite";
                $data = $obList->dataSaida;
            }

            $data = explode("-", $data, 4);
            $data = $data[2]."/".$data[1]."/".$data[0];

            $content .= View::render("admin/modules/lists/card", [
                "lista" => $lista,
                "id" => $obList->id,
                "name" => $name,
                "aluno" => Student::getStudentById($obList->aluno)->nome,
                "data" => $data,
                "ativa" => $obList->ativa ? "Sim" : "Não"
            ]);
        }

        return $content;
    }

    /**
     * Retorna os elementos encontrados ordenados em uma tabela
     * @param Request $request
     * @return string
     */
    private static function getTable($request, &$obPagination)
    {
        $content = View::render("admin/modules/lists/no_itens");

        $itens = self::getItens($request, $obPagination);

        if (!is_null($itens))
        {
            $content = View::render("admin/modules/lists/table", [
                "itens" => $itens
            ]);
        }
        
        return $content;
    }
}

?>