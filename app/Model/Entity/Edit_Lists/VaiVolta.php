<?php

namespace App\Model\Entity\Edit_Lists;

use \App\Utils\Database\Database;

/**
 * Correspondente a tabela edit_vai_volta
 */
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
    public function __construct($id = 0, $vaiVolta = 0, $dataEdicao = null, $horaEdicao = null, $destino = null, $data = null, $horaSaida = null, $horaChegada = null)
    {
        $this->id = $id;
        $this->vaiVolta = $vaiVolta;
        $this->dataEdicao = $dataEdicao;
        $this->horaEdicao = $horaEdicao;
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
            "data_edicao" => $this->dataEdicao,
            "hora_edicao" => $this->horaEdicao,
            "destino" => $this->destino,
            "data" => $this->data,
            "hora_saida" => $this->horaSaida,
            "hora_chegada" => $this->horaChegada,
        ]);

        return true;
    }

    /**
     * Retorna uma edição com base no seu id
     * @param int $id ID a ser procurado
     * @return VaiVolta|null Instância de dição de assinatura
     */
    public static function getSignatureById($id)
    {
        return self::processData(self::getSignatures("id = ".$id))[0];
    }

    /**
     * Retorna uma edição com base na lista original
     * @param int $id ID a ser procurado
     * @return array Array de instâncias de edição de assinatura
     */
    public static function getSignatureBySignature($id)
    {
        return self::processData(self::getSignatures("vai_volta = ".$id));
    }

    /**
     * Retorna as edições das assinaturas
     * @param string $where Condição de busca
     * @param string $order Ordem dos resultados
     * @param string $limit Limite de resultados
     * @param string $fields Campos a serem retornados
     * @return \PDOStatement Resultados da busca
     */
    public static function getSignatures($where = null, $order = null, $limit = null, $fields = "*")
    {
        return (new Database("edit_vai_volta"))->select($where, $order, $limit, $fields);
    }

    /**
     * Processa dados PDOStatement para instâncias de VaiVolta
     * @param \PDOStatement $data Resultado de uma query
     * @return array Array de instâncias de edição assinatura
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
            $itens[] = new self($result['id'], $result['vai_volta'], $result['data_edicao'], $result['hora_edicao'], $result['destino'], $result['data'], $result['hora_saida'], $result['hora_chegada']);
        }

        return $itens;
    }
}