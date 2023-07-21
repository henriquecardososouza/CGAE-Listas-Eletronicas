<?php

namespace App\Model\Entity\Edit_Lists;

use \App\Utils\Database\Database;

class Saida
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
    public $saida;

    /**
     * Local de destino do aluno
     * @var string
     */
    public $destino;

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
     * @param int $saida
     * @param string $destino
     * @param string $dataSaida
     * @param string $dataChegada
     * @param string $horaSaida
     * @param string $horaChegada
     */
    public function __construct($id = -1, $saida = -1, $destino = null, $dataSaida = null, $dataChegada = null, $horaSaida = null, $horaChegada = null)
    {
        $this->id = $id;
        $this->saida = $saida;
        $this->destino = $destino;
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
        $this->id = (new Database("edit_saida"))->insert([
            "saida" => $this->saida,
            "destino" => $this->destino,
            "data_saida" => $this->dataSaida,
            "data_chegada" => $this->dataChegada,
            "hora_saida" => $this->horaSaida,
            "hora_chegada" => $this->horaChegada,
        ]);

        return true;
    }

    /**
     * Retorna uma edição com base no seu id
     * @param int $id
     * @return Saida
     */
    public static function getListById($id)
    {
        return self::processData(self::getLists("id = ".$id))[0];
    }

    /**
     * Retorna uma edição com base na lista original
     * @param int $id
     * @return array
     */
    public static function getListByList($id)
    {
        return self::processData(self::getLists("saida = ".$id));
    }

    /**
     * Retorna as edições das assinaturas
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return \PDOStatement
     */
    public static function getLists($where = null, $order = null, $limit = null, $fields = "*")
    {
        return (new Database("edit_saida"))->select($where, $order, $limit, $fields);
    }

    /**
     * Processa dados PDOStatement para instâncias de Saida
     * @param \PDOStatement $data
     * @return array
     */
    public static function processData($data)
    {
        $results = $data->fetchAll();

        if (empty($results))
        {
            return [];
        }

        $itens = [];

        foreach ($results as $result)
        {
            $itens[] = new self($result['id'], $result['saida'], $result['destino'], $result['data_saida'], $result['data_chegada'], $result['hora_saida'], $result['hora_chegada']);
        }

        return $itens;
    }
}