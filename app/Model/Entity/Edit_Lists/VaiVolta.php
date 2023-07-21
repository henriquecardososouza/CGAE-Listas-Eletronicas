<?php

namespace App\Model\Entity\Edit_Lists;

use \App\Utils\Database\Database;

class VaiVolta
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
    public $vaiVolta;

    /**
     * Local de destino do aluno
     * @var string
     */
    public $destino;

    /**
     * Data da saída do aluno
     * @var string
     */
    public $data;

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
     * @param int $aluno
     * @param int $vaiVolta
     * @param string $destino
     * @param string $data
     * @param string $horaSaida
     * @param string $horaChegada
     */
    public function __construct($id = 0, $vaiVolta = 0, $destino = null, $data = null, $horaSaida = null, $horaChegada = null)
    {
        $this->id = $id;
        $this->vaiVolta = $vaiVolta;
        $this->destino = $destino;
        $this->data = $data;
        $this->horaSaida = $horaSaida;
        $this->horaChegada = $horaChegada;
    }

    /**
     * Cadastra a edição atual no banco de dados
     * @return bool
     */
    public function cadastrar()
    {
        $this->id = (new Database("edit_vai_volta"))->insert([
            "vai_volta" => $this->vaiVolta,
            "destino" => $this->destino,
            "data" => $this->data,
            "hora_saida" => $this->horaSaida,
            "hora_chegada" => $this->horaChegada,
        ]);

        return true;
    }

    /**
     * Retorna uma edição com base no seu id
     * @param int $id
     * @return VaiVolta
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
        return self::processData(self::getLists("vai_volta = ".$id));
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
        return (new Database("edit_vai_volta"))->select($where, $order, $limit, $fields);
    }

    /**
     * Processa dados PDOStatement para instâncias de VaiVolta
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
            $itens[] = new self($result['id'], $result['vai_volta'], $result['destino'], $result['data'], $result['hora_saida'], $result['hora_chegada']);
        }

        return $itens;
    }
}