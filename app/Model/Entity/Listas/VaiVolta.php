<?php

namespace App\Model\Entity\Listas;

use \App\Utils\Database\Database;

class VaiVolta
{
    /**
     * ID da lista
     * @var int
     */
    public int $id;

    /**
     * ID do aluno assinante da lista
     * @var int
     */
    public int $aluno;

    /**
     * Indica se a assinatura está ativa
     * @var bool
     */
    public bool $ativa;

    /**
     * Local de destino do aluno
     * @var string
     */
    public string $destino;

    /**
     * Data da saída do aluno
     * @var string
     */
    public string $data;

    /**
     * Horário de saída do aluno
     * @var string
     */
    public string $horaSaida;

    /**
     * Horário de chegada do aluno
     * @var string
     */
    public string $horaChegada;

    /**
     * Construtor da classe
     * @param int $id
     * @param int $aluno
     * @param bool $ativa
     * @param string $destino
     * @param string $data
     * @param string $horaSaida
     * @param string $horaChegada
     */
    public function __construct($id = 0, $aluno = 0, $ativa = true, $destino = null, $data = null, $horaSaida = null, $horaChegada = null)
    {
        $this->id = $id;
        $this->aluno = $aluno;
        $this->ativa = $ativa;
        $this->destino = $destino;
        $this->data = $data;
        $this->horaSaida = $horaSaida;
        $this->horaChegada = $horaChegada;
    }

    /**
     * Cadastra a assinatura atual no banco de dados
     * @return bool
     */
    public function cadastrar()
    {
        $this->id = (new Database("vai_volta"))->insert([
            "aluno" => $this->aluno,
            "destino" => $this->destino,
            "data" => $this->data,
            "hora_saida" => $this->horaSaida,
            "hora_chegada" => $this->horaChegada,
        ]);

        return true;
    }

    /**
     * Atualiza as assinaturas
     * @param string $where
     * @param array $values
     * @return bool
     */
    public function atualizar($values)
    {
        return (new Database('vai_volta'))->update("id = ".$this->id, $values);
    }
    
    /**
     * Atualiza as assinaturas
     * @param string $where
     * @param array $values
     * @return bool
     */
    public static function atualizarLists($where, $values)
    {
        return (new Database('vai_volta'))->update($where, $values);
    }

    /**
     * Exclui a assinatura atual
     * @return bool
     */
    public function excluir()
    {
        return (new Database('vai_volta'))->delete("id = ".$this->id);
    }

    /**
     * Retorna uma assinatura com base no seu id
     * @param int $id
     * @return VaiVolta
     */
    public static function getListById($id)
    {
        return self::processData(self::getLists("id = ".$id))[0] ?? null;
    }

    /**
     * Retorna uma assinatura com base no aluno a assinou
     * @param int $id
     * @return array
     */
    public static function getListByStudent($id)
    {
        return self::processData(self::getLists("aluno = ".$id));
    }

    /**
     * Retorna assinaturas
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return \PDOStatement
     */
    public static function getLists($where = null, $order = null, $limit = null, $fields = "*")
    {
        return (new Database("vai_volta"))->select($where, $order, $limit, $fields);
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
            $itens[] = new self($result['id'], $result['aluno'], $result['ativa'], $result['destino'], $result['data'], $result['hora_saida'], $result['hora_chegada']);
        }

        return $itens;
    }
}