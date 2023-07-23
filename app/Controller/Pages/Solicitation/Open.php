<?php

namespace App\Controller\Pages\Solicitation;

use App\Controller\Pages\Page;
use App\Model\Entity\Solicitation;
use App\Utils\View;
use App\Utils\Filter\SolicitationConfig;

class Open extends Page
{
    /**
     * Retorna a view da página de minhas solcitações em aberto
     * @return string
     */
    public static function getSolicitation($request)
    {
        $content = View::render("pages/solicitation/open/index", [
            "filters" => self::getFilters($request),
            "itens" => self::getItens($request)
        ]);

        return parent::getPage("Minhas Solicitações", $content);
    }

    /**
     * Configura a view de acordo com os filtros
     * @return string
     */
    public static function setSolicitation($request)
    {
        $content = View::render("pages/solicitation/open/index", [
            "filters" => self::getFilters($request),
            "itens" => self::getItens($request)
        ]);

        return parent::getPage("Minhas Solicitações", $content);
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
        
        $order = is_null($way) ? null : $order;
        $way = is_null($order) ? null : $way;

        $solicitations = [];

        if ($request->getHttpMethod() == "POST")
        {
            $postVars = $request->getPostVars();

            foreach (SolicitationConfig::$filters as $filter => $data)
            {
                if (!isset($postVars[$filter]) || empty($postVars[$filter]))
                {
                    $postVars[$filter] = $data['value'];
                }

                $filters[] = $filter;
                $values[] = $data['value'];
            }

            $solicitations = Solicitation::processData(Solicitation::getSolicitation("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id'], "id DESC"));
        
            $aux = $solicitations;
            $solicitations = [];

            if ($postVars['vai_volta'] == "on")
            {
                foreach ($aux as $item)
                {
                    if ($item->lista == "vai_volta")
                    {
                        $solicitations[] = $item;
                    }
                }
            }

            if ($postVars['pernoite'] == "on")
            {
                foreach ($aux as $item)
                {
                    if ($item->lista == "pernoite")
                    {
                        $solicitations[] = $item;
                    }
                }
            }

            if ($postVars['saida'] == "on")
            {
                foreach ($aux as $item)
                {
                    if ($item->lista == "saida")
                    {
                        $solicitations[] = $item;
                    }
                }
            }

            if ($postVars['vai_volta'] == "off" && $postVars['pernoite'] == "off" && $postVars['saida'] == "off")
            {
                $solicitations = $aux;
            }

            if ($postVars['data_initial'] != "null")
            {
                $aux = $solicitations;
                $solicitations = [];
                
                foreach ($aux as $item)
                {
                    $data = explode(" ", $item->dataAbertura, 10)[0];

                    if ($postVars['data_initial'] <= $data && $data <= $postVars['data_final'])
                    {
                        $solicitations[] = $item;
                    }
                }
            }

            if (!($postVars['excluir'] == "on" && $postVars['editar'] == "on") && ($postVars['excluir'] == "on" || $postVars['editar'] == "on"))
            {
                
                if ($postVars['excluir'] == "on")
                {
                    $aux = $solicitations;
                    $solicitations = [];
                    
                    foreach ($aux as $item)
                    {
                        if ($item->acao == "excluir")
                        {
                            $solicitations[] = $item;
                        }
                    }
                }

                else 
                {
                    $aux = $solicitations;
                    $solicitations = [];
                    
                    foreach ($aux as $item)
                    {
                        if ($item->acao == "editar")
                        {
                            $solicitations[] = $item;
                        }
                    }
                }
            }
        }

        else
        {
            $solicitations = Solicitation::processData(Solicitation::getSolicitation("ativa = true AND aluno = ".$_SESSION['user']['usuario']['id']));
        }
        
        if (empty($solicitations))
        {
            return View::render("pages/solicitation/open/no_solicitation");
        }

        $solicitationsOrdered = [];

        switch ($order)
        {
            case "data":
                $aux = [];
                $id = 0;

                foreach ($solicitations as $item)
                {
                    $aux[] = [
                        "data" => $item->dataAbertura,
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
                        $solicitationsOrdered[] = $solicitations[$element['id']];
                    }
                } while (count($solicitationsOrdered) < count($solicitations));

                break;

            case "nome":
                $aux = [];
                $id = 0;

                foreach ($solicitations as $item)
                {
                    $nome = "";

                    switch ($item->lista)
                    {
                        case "vai_volta":
                            $nome = "vai e volta";
                            break;

                        case "pernoite":
                            $nome = "pernoite";
                            break;

                        case "saida":
                            $nome = "saída";
                            break;
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
                    $solicitationsOrdered[] = $solicitations[$item["id"]];
                }

                break;

            default:
                $solicitationsOrdered = $solicitations;
                break;
        }

        if ($way == "decrescente")
        {
            $aux = $solicitationsOrdered;
            $solicitationsOrdered = [];

            for ($i = count($aux) - 1; $i > -1; $i--)
            {
                $solicitationsOrdered[] = $aux[$i];
            }
        }

        $content = "";

        foreach ($solicitationsOrdered as $item)
        {
            $lista = "";

            switch ($item->lista)
            {
                case "vai_volta":
                    $lista = "Vai e Volta";
                    break;

                case "pernoite":
                    $lista = "Pernoite";
                    break;

                case "saida":
                    $lista = "Saída";
                    break;
            }
                    
            $dataAbertura = explode("-", $item->dataAbertura, 4);
            $dataAbertura = explode(" ", $dataAbertura[2], 4)[0]."/".$dataAbertura[1]."/".$dataAbertura[0];

            $content .= View::render("pages/solicitation/open/item", [
                "id" => $item->id,
                "lista" => $lista,
                "acao" => ucfirst($item->acao),
                "data_abertura" => $dataAbertura
            ]);
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
        return View::render("pages/solicitation/filters/index", [
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
        $filters = array_keys(SolicitationConfig::$filters);
        $values = array_values(SolicitationConfig::$filters);

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

                $itens .= View::render("pages/solicitation/filters/filter/active_card", [
                    "name_oficial" => $values[$i]['name_oficial'],
                    "name" => $values[$i]['name']
                ]);

                $filterActives++;
            }
        }

        if (!$blockActives)
        {
            $actives = View::render("pages/solicitation/filters/filter/title", [
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

        return View::render("pages/solicitation/filters/filter/index", $params);
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
        
        $order = is_null($way) ? null : $order;
        $way = is_null($order) ? null : $way;

        $active = "";
        $idOrder = -1;
        $idWay = -1;

        for ($i = 0; $i < count(SolicitationConfig::$orders['order']); $i++)
        {
            if (SolicitationConfig::$orders['order'][$i] == $order)
            {
                $idOrder = $i;
                break;
            }
        }
        
        for ($i = 0; $i < count(SolicitationConfig::$orders['way']); $i++)
        {
            if (SolicitationConfig::$orders['way'][$i] == $way)
            {
                $idWay = $i;
                break;
            }
        }

        if (!is_null($order))
        {
            $active = "<hr>".View::render("pages/solicitation/filters/order_by/title", [
                "item" => View::render("pages/solicitation/filters/order_by/active_card", [
                    "name" => ucfirst($order)
                ])
            ]);
        }

        $item = [
            "active" => $active
        ];

        for ($i = 0; $i < count(SolicitationConfig::$orders['order']); $i++)
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

        for ($i = 0; $i < count(SolicitationConfig::$orders['way']); $i++)
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

        $content = View::render("pages/solicitation/filters/order_by/index", $item);

        return $content;
    }
}