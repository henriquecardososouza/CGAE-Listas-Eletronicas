<?php

namespace App\Controller\Admin\Modules\Solicitation;

use App\Controller\Admin\Page;
use App\Model\Entity\Solicitation as EntitySolicitation;
use App\Model\Entity\Student;
use App\Utils\View;
use App\Utils\Filter\Admin\SolicitationConfig;
use App\Utils\Database\Pagination;

class Open extends Page
{
    /**
     * Retorna a view de solicitações em aberto
     * @param Request $request
     * @return string
     */
    public static function getOpenSolicitations($request)
    {
        parent::configNavbar("solicitations");

        $content = View::render("admin/modules/solicitations/open/index", [
            "filters" => self::getFilters($request),
            "table" => self::getTable($request, $obPagination),
            "pagination" => parent::getPagination($request, $obPagination),
        ]);

        return parent::getPage("Solicitações", $content);
    }

    /**
     * Retorna os objetos de filtros disponíveis
     * @param Request $request
     * @return string
     */
    private static function getFilters($request)
    {
        $content = [
            "filter" => self::getFilter($request),
            "order" => self::getOrder($request)
        ];

        return View::render("admin/modules/solicitations/filters/index", $content);
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
            if ($filters[$i] == "list")
            {
                if ($postVars[$filters[$i]] == "null")
                {
                    $checked[] = "";
                    $checked[] = "";
                    $checked[] = "";
                }

                else
                {
                    if ($postVars['list'] == "vai_volta")
                    {
                        $checked[] = "checked";
                        $checked[] = "";
                        $checked[] = "";
                    }
                    
                    else if ($postVars['list'] == "pernoite")
                    {
                        $checked[] = "";
                        $checked[] = "checked";
                        $checked[] = "";
                    }

                    else
                    {
                        $checked[] = "";
                        $checked[] = "";
                        $checked[] = "checked";
                    }
                }
            }
            
            else if ($postVars[$filters[$i]] == "off")
            {
                $checked[] = "";
            }

            else if ($filters[$i] == "acao")
            {
                if ($postVars[$filters[$i]] == "null")
                {
                    $checked[] = "";
                    $checked[] = "";
                }

                else
                {
                    if ($postVars['acao'] == "editar")
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
            if ($postVars[$filters[$i]] == "on" 
                || ($postVars[$filters[$i]] != "null" && $values[$i]['name'] == "list") 
                || ($postVars[$filters[$i]] != "null" && $values[$i]['name'] == "acao")
                || ($postVars[$filters[$i]] != "null" && $values[$i]['name'] == "sexo")
                || ($postVars[$filters[$i]] != "null" && $values[$i]['name'] == "data" && !$dataAlreadyIn))
            {
                if ($values[$i]['name'] == "data")
                {
                    $dataAlreadyIn = true;
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
            $actives = View::render("admin/modules/solicitations/filters/filter/title", [
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

        return View::render("admin/modules/solicitations/filters/filter/index", $params);
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
            $active = "<hr>".View::render("admin/modules/lists/filters/order_by/title", [
                "item" => View::render("admin/modules/lists/filters/order_by/active_card", [
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

        $content = View::render("admin/modules/solicitations/filters/order_by/index", $item);

        return $content;
    }

    /**
     * Retorna os elementos encontrados ordenados em uma tabela
     * @param Request $request
     * @return string
     */
    private static function getTable($request, &$obPagination)
    {
        $content = View::render("admin/modules/solicitations/no_itens");

        $itens = self::getItens($request, $obPagination);

        if (!is_null($itens))
        {
            $content = View::render("admin/modules/solicitations/table", [
                "itens" => $itens
            ]);
        }
        
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
        $filters = [];
        $values = [];
        $order = [];

        foreach (SolicitationConfig::$filters as $filter => $data)
        {
            if (!isset($postVars[$filter]) || empty($postVars[$filter]))
            {
                $postVars[$filter] = $data['value'];
            }

            $filters[] = $filter;
            $values[] = $data['value'];
        }

        if (isset($postVars['order']))
        {
            $order = [
                $postVars['order'] => $postVars['way']
            ];
        }
        
        $solicitations = EntitySolicitation::processData(EntitySolicitation::getSolicitation("ativa = true"));

        if ($postVars['data_initial'] != "null")
        {
            $aux = [];
            $dataMin = $postVars['data_initial'];
            $dataMax = $postVars['data_final'];

            foreach ($solicitations as $item)
            {
                if ($item->dataAbertura >= $dataMin && $item->dataAbertura <= $dataMax)
                {
                    $aux[] = $item;
                }
            }

            $ob = $aux;
        }

        if ($postVars['list'] != "null")
        {
            $aux = [];
            
            foreach ($solicitations as $item)
            {
                if ($item->lista == $postVars['list'])
                {
                    $aux[] = $item;
                }
            }

            $solicitations = $aux;
        }

        if ($postVars['acao'] != "null")
        {
            $aux = [];
            
            foreach ($solicitations as $item)
            {
                if ($item->acao == $postVars['acao'])
                {
                    $aux[] = $item;
                }
            }

            $solicitations = $aux;
        }

        if ($postVars['sexo'] != "null")
        {
            $aux = [];
            
            foreach ($solicitations as $item)
            {
                if (Student::getStudentById($item->aluno)->sexo == $postVars['sexo'])
                {
                    $aux[] = $item;
                }
            }

            $solicitations = $aux;
        }

        if (!empty($order) && !empty($solicitations))
        {
            $obOrdered = [];

            switch (array_keys($order)[0])
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
                            $obOrdered[] = $solicitations[$element['id']];
                        }
                    } while (count($obOrdered) < count($solicitations));

                    break;

                case "nome":
                    $aux = [];

                    foreach ($solicitations as $item)
                    {
                        $aux[] = Student::getStudentById($item->aluno)->nome;
                    }

                    $collator = collator_create("pt_BR");
                    collator_asort($collator, $aux);
                    
                    $keys = array_keys($aux);
                
                    foreach ($keys as $key)
                    {
                        $obOrdered[] = $solicitations[$key];
                    }

                    break;

                default:
                    $obOrdered = $solicitations;
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

            $solicitations = $obOrdered;
        }

        if (empty($solicitations))
        {
            $content = null;
            $obPagination = new Pagination(0, 1, 0);
        }

        else 
        {
            $currentPage = $request->getQueryParams()['page'] ?? 1;
            $obPagination = new Pagination(count($solicitations), $currentPage, 10);

            $aux = [];

            for ($i = ($currentPage - 1) * $obPagination->getLimit(); $i < ($currentPage - 1) * $obPagination->getLimit() + $obPagination->getLimit(); $i++)
            {
                if (isset($solicitations[$i]))
                {
                    $aux[] = $solicitations[$i];
                }
            }

            $solicitations = $aux;
        }

        if (empty($solicitations))
        {
            $content = null;
            $obPagination = new Pagination(0, 1, 0);
        }

        if (!is_null($solicitations))
        {
            foreach ($solicitations as $item)
            {
                $id = $item->id;
                $aluno = Student::getStudentById($item->aluno)->nome;
                $data = $item->dataAbertura;
                $lista = $item->lista == "vai_volta" ? "Vai e Volta" : ucfirst($item->lista);
                $acao = ucfirst($item->acao);

                $data = explode("-", $data, 4);
                $data = explode(" ", $data[2], 10)[0]."/".$data[1]."/".$data[0]." ".explode(" ", $data[2], 10)[1];

                $content .= View::render("admin/modules/solicitations/card", [
                    "id" => $id,
                    "aluno" => $aluno,
                    "data" => $data,
                    "lista" => $lista,
                    "acao" => $acao
                ]);
            }
        }

        return $content;
    }
}

?>