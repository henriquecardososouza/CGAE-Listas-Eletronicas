<?php

namespace App\Model\Entity\Listas;

use \App\Utils\Database\Database;

class Pernoite
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
     * Endereço de destino do aluno
     * @var string
     */
    public string $endereco;

    /**
     * Nome do responsável pelo aluno
     * @var string
     */
    public string $nomeResponsavel;

    /**
     * Telefone do responsável do aluno
     * @var string
     */
    public string $telefone;

    /**
     * Data da saída do aluno
     * @var string
     */
    public string $dataSaida;

    /**
     * Data da chegada do aluno
     * @var string
     */
    public string $dataChegada;

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
     * @param string $endereco
     * @param string $nomeResponsavel
     * @param string $telefone
     * @param string $dataSaida
     * @param string $dataChegada
     * @param string $horaSaida
     * @param string $horaChegada
     */
    public function __construct($id = 0, $aluno = 0, $ativa = true, $endereco = null, $nomeResponsavel = null, $telefone = null, $dataSaida = null, $dataChegada = null, $horaSaida = null, $horaChegada = null)
    {
        $this->id = $id;
        $this->aluno = $aluno;
        $this->ativa = $ativa;
        $this->endereco = $endereco;
        $this->nomeResponsavel = $nomeResponsavel;
        $this->telefone = $telefone;
        $this->dataSaida = $dataSaida;
        $this->dataChegada = $dataChegada;
        $this->horaSaida = $horaSaida;
        $this->horaChegada = $horaChegada;
    }

    /**
     * Cadastra a assinatura atual no banco de dados
     * @return bool
     */
    public function cadastrar()
    {
        $this->id = (new Database("pernoite"))->insert([
            "aluno" => $this->aluno,
            "endereco" => $this->endereco,
            "nome_responsavel" => $this->nomeResponsavel,
            "telefone" => $this->telefone,
            "data_saida" => $this->dataSaida,
            "data_chegada" => $this->dataChegada,
            "hora_saida" => $this->horaSaida,
            "hora_chegada" => $this->horaChegada,
        ]);

        return true;
    }

    /**
     * Atualiza as assinaturas
     * @param array $values
     * @return bool
     */
    public function atualizar($values)
    {
        return (new Database('pernoite'))->update("id = ".$this->id, $values);
    }
    
    /**
     * Atualiza as assinaturas
     * @param string $where
     * @param array $values
     * @return bool
     */
    public static function atualizarLists($where, $values)
    {
        return (new Database('pernoite'))->update($where, $values);
    }

    /**
     * Exclui a assinatura atual
     * @return bool
     */
    public function excluir()
    {
        return (new Database('pernoite'))->delete("id = ".$this->id);
    }

    /**
     * Retorna uma assinatura com base no seu id
     * @param int $id
     * @return Pernoite
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
        return (new Database("pernoite"))->select($where, $order, $limit, $fields);
    }

    /**
     * Processa dados PDOStatement para instâncias de Pernoite
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
            $itens[] = new self($result['id'], $result['aluno'], $result['ativa'], $result['endereco'], $result['nome_responsavel'], $result['telefone'], $result['data_saida'], $result['data_chegada'], $result['hora_saida'], $result['hora_chegada']);
        }

        return $itens;
    }
}