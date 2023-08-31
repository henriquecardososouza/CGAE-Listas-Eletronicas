<?php

namespace App\Model\Entity\Edit_Lists;

use \App\Utils\Database\Database;

/**
 * Correspondente a tabela edit_pernoite
 */
class Pernoite
{
    /**
     * ID da lista
     * @var int
     */
    public $id;

    /**
     * ID da assinatura original
     * @var int
     */
    public $pernoite;

    /**
     * Data da edição
     * @var string
     */
    public $dataEdicao;
    
    /**
     * Hora da edição
     * @var string
     */
    public $horaEdicao;

    /**
     * Local de destino do aluno
     * @var string
     */
    public $endereco;

    /**
     * Nome do responsável pelo aluno
     * @var string
     */
    public $nomeResponsavel;

    /**
     * Telefone do responsável pelo aluno
     * @var string
     */
    public $telefone;

    /**
     * Data da saída do aluno
     * @var string
     */
    public $dataSaida;

    /**
     * Data da chegada do aluno
     * @var string
     */
    public $dataChegada;

    /**
     * Horário de saída do aluno
     * @var string
     */
    public $horaSaida;

    /**
     * Horário de chegada do aluno
     * @var string
     */
    public $horaChegada;

    /**
     * Construtor da classe
     * @param int $id
     * @param int $pernoite
     * @param string $dataEdicao
     * @param string $horaEdicao
     * @param string $nomeResponsavel
     * @param string $endereco
     * @param string $telefone
     * @param string dataSaida
     * @param string $dataChegada
     * @param string $horaSaida
     * @param string $horaChegada
     */
    public function __construct($id = -1, $pernoite = -1, $dataEdicao = null, $horaEdicao = null, $nomeResponsavel = null, $endereco = null, $telefone = null, $dataSaida = null, $dataChegada = null, $horaSaida = null, $horaChegada = null)
    {
        $this->id = $id;
        $this->pernoite = $pernoite;
        $this->dataEdicao = $dataEdicao;
        $this->horaEdicao = $horaEdicao;
        $this->nomeResponsavel = $nomeResponsavel;
        $this->endereco = $endereco;
        $this->telefone = $telefone;
        $this->dataSaida = $dataSaida;
        $this->dataChegada = $dataChegada;
        $this->horaSaida = $horaSaida;
        $this->horaChegada = $horaChegada;
    }

    /**
     * Cadastra a edição atual no banco de dados
     * @return bool
     */
    public function cadastrar()
    {
        $this->id = (new Database("edit_pernoite"))->insert([
            "pernoite" => $this->pernoite,
            "data_edicao" => $this->dataEdicao,
            "hora_edicao" => $this->horaEdicao,
            "nome_responsavel" => $this->nomeResponsavel,
            "endereco" => $this->endereco,
            "telefone" => $this->telefone,
            "data_saida" => $this->dataSaida,
            "data_chegada" => $this->dataChegada,
            "hora_saida" => $this->horaSaida,
            "hora_chegada" => $this->horaChegada,
        ]);

        return true;
    }

    /**
     * Retorna uma edição com base no seu id
     * @param int $id ID a ser procurado
     * @return Pernoite|null Instância de edição de assinatura
     */
    public static function getSignatureById($id)
    {
        return self::processData(self::getSignatures("id = ".$id))[0] ?? null;
    }

    /**
     * Retorna uma edição com base na assinatura original
     * @param int $id ID a ser procurado
     * @return array Array de instâncias de edição de assinatura
     */
    public static function getSignatureByList($id)
    {
        return self::processData(self::getSignatures("pernoite = ".$id));
    }

    /**
     * Retorna as edições das assinaturas
     * @param string $where Consição de busca
     * @param string $order Ordem dos resultados
     * @param string $limit Limite de resultados
     * @param string $fields Campos a serem retornados
     * @return \PDOStatement Resultados da busca
     */
    public static function getSignatures($where = null, $order = null, $limit = null, $fields = "*")
    {
        return (new Database("edit_pernoite"))->select($where, $order, $limit, $fields);
    }

    /**
     * Processa dados PDOStatement para instâncias de Pernoite
     * @param \PDOStatement $data Resultado de uma query
     * @return array Array de instâncias de edição de assinatura
     */
    public static function processData($data)
    {
        // TRANSFORMA OS RESULTADOS EM UM ARRAY
        $results = $data->fetchAll();

        // VERIFICA SE EXISTE ALGUM RESULTADO
        if (empty($results))
        {
            return [];
        }

        // INSTÂNCIA OS OBJETOS DE EDIÇÃO DE ASSINATURA
        $itens = [];

        foreach ($results as $result)
        {
            $itens[] = new self($result['id'], $result['pernoite'], $result['data_edicao'], $result['hora_edicao'], $result['nome_responsavel'], $result['endereco'], $result['telefone'], $result['data_saida'], $result['data_chegada'], $result['hora_saida'], $result['hora_chegada']);
        }

        return $itens;
    }
}