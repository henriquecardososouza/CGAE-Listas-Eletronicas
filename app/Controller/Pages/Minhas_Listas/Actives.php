<?php

namespace App\Controller\Pages\Minhas_Listas;

use App\Controller\Pages\Page;
use App\Model\Entity\Listas;
use App\Utils\View;
use App\Utils\Filter\ListConfig;

class Actives extends Page
{
    /**
     * Retorna a view da página de minhas listas ativas
     * @return string
     */
    public static function getActives($request)
    {
        $content = View::render("pages/my_lists/actives/index", [
            "filters" => self::getFilters($request),
            "itens" => self::getItens($request)
        ]);

        return parent::getPage("Minhas Assinaturas", $content);
    }

    /**
     * Configura a view de acordo com os filtros
     * @return string
     */
    public static function setActives($request)
    {
        $content = View::render("pages/my_lists/actives/index", [
            "filters" => self::getFilters($request),
            "itens" => self::getItens($request)
        ]);

        return parent::getPage("Minhas Assinaturas", $content);
    }

    /**
     * Retorna os cards das listas ativas
     * @return string
     */
    private static function getItens($request)
    {
        \App\Session\Login::init();

        $postVars = [];
        $filters = [];
        $values = [];
        $order = $request->getPostVars()['order'] ?? null;
        $way = $request->getPostVars()['way'] ?? null;
        $lists = [];

        if ($request->getHttpMethod() == "POST")
        {
            $postVars = $request->getPostVars();

            foreach (ListConfig::$filters as $filter => $data)
            {
                if (!isset($postVars[$filter]) || empty($postVars[$filter]))
                {
                    $postVars[$filter] = $data['value'];
                }

                $filters[] = $filter;
                $values[] = $data['value'];
            }

            if ($postVars['vai_volta'] == "on")
            {
                $lists['vai_volta'] = Listas\VaiVolta::processData(Listas\VaiVolta::getLists("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));
            }

            if ($postVars['saida'] == "on")
            {
                $lists['saida'] = Listas\Saida::processData(Listas\Saida::getLists("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));
            }

            if ($postVars['pernoite'] == "on")
            {
                $lists['pernoite'] = Listas\Pernoite::processData(Listas\Pernoite::getLists("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));
            }

            if ($postVars['vai_volta'] == "off" && $postVars['saida'] == "off" && $postVars['pernoite'] == "off")
            {
                $lists['vai_volta'] = Listas\VaiVolta::processData(Listas\VaiVolta::getLists("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));
                $lists['saida'] = Listas\Saida::processData(Listas\Saida::getLists("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));
                $lists['pernoite'] = Listas\Pernoite::processData(Listas\Pernoite::getLists("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));    
            }

            else 
            {
                if (!isset($lists['vai_volta']))
                {
                    $lists['vai_volta'] = [];
                }

                if (!isset($lists['saida']))
                {
                    $lists['saida'] = [];
                }

                if (!isset($lists['pernoite']))
                {
                    $lists['pernoite'] = [];
                }
            }

            if ($postVars['data_initial'] != "null")
            {
                $aux = $lists;

                $lists = [
                    "vai_volta" => [],
                    "saida" => [],
                    "pernoite" => []
                ];
                
                foreach ($aux as $list => $value)
                {
                    $dataAbertura = "";

                    foreach ($value as $obList)
                    {
                        switch ($list)
                        {
                            case "vai_volta":
                                $dataAbertura = $obList->data;
                                break;

                            case "pernoite":
                                $dataAbertura = $obList->dataSaida;
                                break;
                                
                            case "saida":
                                $dataAbertura = $obList->dataSaida;
                                break;
                        }

                        $data = explode(" ", $dataAbertura, 10)[0];

                        if ($postVars['data_initial'] <= $data && $data <= $postVars['data_final'])
                        {
                            $lists[$list][] = $obList;
                        }
                    }
                }
            }
        }

        else
        {
            $lists['vai_volta'] = Listas\VaiVolta::processData(Listas\VaiVolta::getLists("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));
            $lists['saida'] = Listas\Saida::processData(Listas\Saida::getLists("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));
            $lists['pernoite'] = Listas\Pernoite::processData(Listas\Pernoite::getLists("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));
        }

        if (empty($lists['vai_volta']) && empty($lists['pernoite']) && empty($lists['saida']))
        {
            return View::render("pages/my_lists/actives/no_lists");
        }

        $listas = array_merge($lists['vai_volta'], $lists['pernoite'], $lists['saida']);
        $listsOrdered = [];

        switch ($order)
        {
            case "data":
                $aux = [];
                $id = 0;

                foreach ($listas as $item)
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
                        $listsOrdered[] = $listas[$element['id']];
                    }
                } while (count($listsOrdered) < count($listas));

                break;

            case "nome":
                $aux = [];
                $id = 0;

                foreach ($listas as $item)
                {
                    $nome = "";

                    if ($item instanceof Listas\VaiVolta)
                    {
                        $nome = "vai e volta";
                    }

                    else if ($item instanceof Listas\Pernoite)
                    {
                        $nome = "pernoite";
                    }

                    else
                    {
                        $nome = "saída";
                    }

                    $aux[] = [
                        "nome" => $nome,
                        "id" => $id
                    ];

                    $id++;
                }

                sort($aux);

                foreach ($aux as $item)
                {
                    $listsOrdered[] = $listas[$item["id"]];
                }

                break;

            default:
                $listsOrdered = $listas;
                break;
        }

        if ($way == "decrescente")
        {
            $aux = $listsOrdered;
            $listsOrdered = [];

            for ($i = count($aux) - 1; $i > -1; $i--)
            {
                $listsOrdered[] = $aux[$i];
            }
        }

        $content = "";

        foreach ($listsOrdered as $item)
        {
            if ($item instanceof Listas\VaiVolta)
            {
                $lista = "Vai e Volta";
                        
                $dataSaida = explode("-", $item->data, 4);
                $dataSaida = $dataSaida[2]."/".$dataSaida[1]."/".$dataSaida[0];
                
                $dataChegada = explode("-", $item->data, 4);
                $dataChegada = $dataChegada[2]."/".$dataChegada[1]."/".$dataChegada[0];

                $content .= View::render("pages/my_lists/actives/card", [
                    "id" => $item->id,
                    "lista" => $lista,
                    "name" => "vai_volta",
                    "data_saida" => $dataSaida,
                    "data_chegada" => $dataChegada
                ]);
            }

            else if ($item instanceof Listas\Pernoite)
            {
                $lista = "Pernoite";
                        
                $dataSaida = explode("-", $item->dataSaida, 4);
                $dataSaida = $dataSaida[2]."/".$dataSaida[1]."/".$dataSaida[0];
                
                $dataChegada = explode("-", $item->dataChegada, 4);
                $dataChegada = $dataChegada[2]."/".$dataChegada[1]."/".$dataChegada[0];

                $content .= View::render("pages/my_lists/actives/card", [
                    "id" => $item->id,
                    "lista" => $lista,
                    "name" => "pernoite",
                    "data_saida" => $dataSaida,
                    "data_chegada" => $dataChegada,
                ]);
            }

            else 
            {
                $lista = "Saída";

                $dataSaida = explode("-", $item->dataSaida, 4);
                $dataSaida = $dataSaida[2]."/".$dataSaida[1]."/".$dataSaida[0];
                
                $dataChegada = explode("-", $item->dataChegada, 4);
                $dataChegada = $dataChegada[2]."/".$dataChegada[1]."/".$dataChegada[0];

                $content .= View::render("pages/my_lists/actives/card", [
                    "id" => $item->id,
                    "lista" => $lista,
                    "name" => "saida",
                    "data_saida" => $dataSaida,
                    "data_chegada" => $dataChegada,
                ]);
            }
        }

        return $content;
    }

    /**
     * Retorna as opções de filtros de busca
     * @param Request $request
     * @return string
     */
    private static function getFilters($request)
    {
        return View::render("pages/my_lists/filters/index", [
            "filter" => self::getFilter($request),
            "order" => self::getOrder($request)
        ]);
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

            else if ($filters[$i] != "data_initial" && $filters[$i] != "data_final")
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

        $itens = "";
        $filterActives = 0;
        $dataAlreadyIn = false;

        for ($i = 0; $i < count($filters); $i++)
        {
            if ($postVars[$filters[$i]] == "on" || ($postVars[$filters[$i]] != "null" && $values[$i]['name'] == "data") && !$dataAlreadyIn)
            {
                if ($values[$i]['name'] == "data")
                {
                    $dataAlreadyIn = true;
                }

                $itens .= View::render("pages/my_lists/filters/filter/active_card", [
                    "name_oficial" => $values[$i]['name_oficial'],
                    "name" => $values[$i]['name']
                ]);

                $filterActives++;
            }
        }

        if (!$blockActives)
        {
            $actives = View::render("pages/my_lists/filters/filter/title", [
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

        return View::render("pages/my_lists/filters/filter/index", $params);
    }

    /**
     * Retorna os objetos de ordenação de busca
     * @param Request $request
     * @return string
     */
    private static function getOrder($request)
    {
        $postVars = $request->getPostVars();
        $order = $postVars['order'] ?? null;
        $way = $postVars['way'] ?? null;

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
            $active = "<hr>".View::render("pages/my_lists/filters/order_by/title", [
                "item" => View::render("pages/my_lists/filters/order_by/active_card", [
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

        $content = View::render("pages/my_lists/filters/order_by/index", $item);

        return $content;
    }
}