<?php

namespace App\Controller\Admin\Modules\Signatures;

use App\Controller\Admin\Alert;
use App\Controller\Admin\Page;
use App\Model\Entity\Student;
use App\Model\Entity\Listas;
use App\Utils\View;

class NewSignature extends Page
{
    /**
     * Retorna a view da página de cadastro de assinatura
     * @param string $name
     * @param string $message
     * @param bool $success
     * @return string
     */
    public static function getNewSignature($name = "null", $message = null, $success = false)
    {
        parent::configNavbar("signatures");

        $content = View::render("admin/modules/lists/signature/new/index", [
            "name" => $name,
            "status" => is_null($message) ? "" : ($success ? Alert::getSuccess($message) : Alert::getError($message))
        ]);

        return parent::getPage("Assinatura", $content);
    }

    /**
     * Cadastra a assinatura
     * @param Request $request
     * @return string
     */
    public static function setNewSignature($request)
    {
        $postVars = $request->getPostVars();
        $message = null;
        $success = false;
        $pass = true;

        $aluno = $postVars['aluno'];
        $obAluno = Student::getStudentByIdRefeitorio($aluno);

        if ($obAluno == null)
        {
            $message = "Aluno não encontrado!";
        }

        else
        {
            $hourFinal = "23:00:00";
            $hourInitial = "07:00:00";

            switch ($postVars['type'])
            {
                case "vai_volta":
                    $destino = $postVars['destino'];
                    $data = $postVars['data'];
                    $horaSaida = $postVars['hora_saida'].":00";
                    $horaChegada = $postVars['hora_chegada'].":00";

                    date_default_timezone_set("America/Sao_Paulo");
                    $dataAtual = date("Y-m-d", time());
                    $horaAtual = date("H:i:s", time() + 60);

                    $ob = Listas\VaiVolta::getListByStudent($obAluno->id);

                    if (!empty($ob))
                    {
                        foreach ($ob as $item)
                        {
                            if ($item->ativa)
                            {
                                $message = "O aluno já possui uma assinatura ativa cadastrada!";
                                $pass = false;
                                break;
                            }
                        }
                    }
                    
                    if (!$pass)
                    {
                        break;
                    }

                    if (!($hourInitial < $horaSaida && $horaSaida < $hourFinal))
                    {
                        $message = "O horário de saída não é válido";
                    }

                    else if (!($hourInitial < $horaChegada && $horaChegada < $hourFinal))
                    {
                        $message = "O horário de chegada não é válido";
                    }

                    else if ($horaSaida >= $horaChegada)
                    {
                        $message = "O horário de chegada não é válido!";
                    }

                    else if ($dataAtual > $data)
                    {
                        $message = "A data informada não é válida!";
                    }

                    else if ($dataAtual == $data && $horaAtual > $horaSaida)
                    {
                        $message = "O horário de saída não é válido!";
                    }

                    else
                    {
                        $message = "Cadastrado com sucesso!";
                        $success = true;
                        $obList = new Listas\VaiVolta(0, $obAluno->id, true, $destino, $data, $horaSaida, $horaChegada);
                        $obList->cadastrar();
                    }

                    break;

                case "saida":
                    $destino = $postVars['destino'];
                    $dataSaida = $postVars['data_saida'];
                    $dataChegada = $postVars['data_chegada'];
                    $horaSaida = $postVars['hora_saida'].":00";
                    $horaChegada = $postVars['hora_chegada'].":00";

                    date_default_timezone_set("America/Sao_Paulo");
                    $dataAtual = date("Y-m-d", time());
                    $horaAtual = date("H:i:s", time() + 60);

                    $ob = Listas\Saida::getListByStudent($obAluno->id);

                    if (!empty($ob))
                    {
                        foreach ($ob as $item)
                        {
                            if ($item->ativa)
                            {
                                $message = "O aluno já possui uma assinatura ativa cadastrada!";
                                $pass = false;
                                break;
                            }
                        }
                    }

                    if (!$pass)
                    {
                        break;
                    }

                    if (!($hourInitial < $horaSaida && $horaSaida < $hourFinal))
                    {
                        $message = "O horário de saída não é válido";
                    }

                    else if (!($hourInitial < $horaChegada && $horaChegada < $hourFinal))
                    {
                        $message = "O horário de chegada não é válido";
                    }

                    else if ($dataSaida > $dataChegada)
                    {
                        $message = "A Data de Chegada não é Válida!";
                    }

                    else if ($horaSaida >= $horaChegada && $dataSaida == $dataChegada)
                    {
                        $message = "O Horário de Chegada não é Válido!";
                    }

                    else if ($dataAtual > $dataSaida)
                    {
                        $message = "A Data de Saída Informada não é Válida!";
                    }

                    else if ($dataAtual == $dataSaida && $horaAtual > $horaSaida)
                    {
                        $message = "O Horário de Saída não é Válido!";
                    }

                    else
                    {
                        $message = "Cadastrado com sucesso!";
                        $success = true;
                        $obList = new Listas\Saida(0, $obAluno->id, true, $destino, $dataSaida, $dataChegada, $horaSaida, $horaChegada);

                        $obList->cadastrar();
                    }

                    break;

                case "pernoite":
                    $endereco = $postVars['endereco'];
                    $nomeResponsavel = $postVars['nome_responsavel'];
                    $telefone = trim($postVars['telefone']);
                    $dataSaida = $postVars['data_saida'];
                    $dataChegada = $postVars['data_chegada'];
                    $horaSaida = $postVars['hora_saida'].":00";
                    $horaChegada = $postVars['hora_chegada'].":00";
            
                    date_default_timezone_set("America/Sao_Paulo");
                    $dataAtual = date("Y-m-d", time());
                    $horaAtual = date("H:i:s", time() + 60);
            
                    $ob = Listas\Pernoite::getListByStudent($obAluno->id);

                    if (!empty($ob))
                    {
                        foreach ($ob as $item)
                        {
                            if ($item->ativa)
                            {
                                $message = "O aluno já possui uma assinatura ativa cadastrada!";
                                $pass = false;
                                break;
                            }
                        }
                    }

                    if (!$pass)
                    {
                        break;
                    }

                    if (!($hourInitial < $horaSaida && $horaSaida < $hourFinal))
                    {
                        $message = "O horário de saída não é válido";
                    }

                    else if (!($hourInitial < $horaChegada && $horaChegada < $hourFinal))
                    {
                        $message = "O horário de chegada não é válido";
                    }

                    else if ($dataSaida > $dataChegada)
                    {
                        $message = "A data de chegada não é válida!";
                    }
            
                    else if ($dataAtual > $dataSaida)
                    {
                        $message = "A data de saída informada não é válida!";
                    }
            
                    else if ($horaSaida >= $horaChegada && $dataSaida == $dataChegada)
                    {
                        $message = "O horário de chegada não é válido!";
                    }
            
                    else if ($dataAtual == $dataSaida && $horaAtual > $horaSaida)
                    {
                        $message = "O horário de saída não é válido!";
                    }

                    else
                    {
                        $message = "Cadastrado com sucesso!";
                        $success = true;
                        $obList = new Listas\Pernoite(0, $obAluno->id, true, $endereco, $nomeResponsavel, $telefone, $dataSaida, $dataChegada, $horaSaida, $horaChegada);

                        $obList->cadastrar();
                    }

                    break;
            }
        }

        return self::getNewSignature($postVars['type'], $message, $success);
    }
}