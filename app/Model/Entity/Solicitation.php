<?php

namespace App\Model\Entity;

use \App\Utils\Database\Database;

class Solicitation
{
    /**
     * ID da instância
     * @var int
     */
    public $id;

    /**
     * ID do aluno que enviou a solicitação
     * @var int
     */
    public $aluno;

    /**
     * ID da lista em que a solicitação atua
     * @var int
     */
    public $idLista;

    /**
     * ID do formulário de edição
     * @var int
     */
    public $idEdit;

    /**
     * Lista em que a solicitação atua
     * @var string
     */
    public $lista;

    /**
     * Ação requisitada na solicitação
     * @var string
     */
    public $acao;

    /**
     * Motivo da abertura da solicitação
     * @var string
     */
    public $motivo;

    /**
     * Estado da solicitação
     * @var bool
     */
    public $ativa;

    /**
     * Indica se a solicitação foi aprovada
     * @var bool
     */
    public $aprovada;

    /**
     * Data de criação da solicitação
     * @var string
     */
    public $dataAbertura;

    /**
     * Data de conclusão da solicitação
     * @var string
     */
    public $dataEncerramento;

    /**
     * Construtor da classe
     */
    public function __construct($id = -1, $aluno = -1, $idLista = -1, $idEdit = -1, $lista = null, $acao = null, $motivo = null, $ativa = false, $aprovada = false, $dataAbertura = null, $dataEncerramento = null)
    {
        $this->id = $id;
        $this->aluno = $aluno;
        $this->idLista = $idLista;
        $this->idEdit = $idEdit;
        $this->lista = $lista;
        $this->acao = $acao;
        $this->motivo = $motivo;
        $this->ativa = $ativa;
        $this->aprovada = $aprovada;
        $this->dataAbertura = $dataAbertura;
        $this->dataEncerramento = $dataEncerramento;
    }

    /**
     * Retorna as solicitações de um aluno
     * @param int $id
     * @return array
     */
    public static function getSolicitationByStudent($id)
    {
        return self::processData(self::getSolicitation("aluno = ".$id));
    }

    /**
     * Retorna uma solicitação com base no seu ID
     * @return Solicitation
     */
    public static function getSolicitationById($id)
    {
        return self::processData(self::getSolicitation("id = ".$id))[0];
    }

    /**
     * Retorna uma solicitaçãon ativa com base no tipo de lista atuante
     * @param int $idLista
     * @param int $idAluno
     * @return array
     */
    public static function getActiveSolicitationByList($lista)
    {
        return self::processData(self::getSolicitation("lista = '".$lista."'"));
    }

    /**
     * Realiza uma busca na tabela
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return \PDOStatement
     */
    public static function getSolicitation($where = null, $order = null, $limit = null, $fields = "*")
    {
        return (new Database("solicitacao"))->select($where, $order, $limit, $fields);
    }

    /**
     * Cadastra a instância atual no banco de dados
     */
    public function cadastrar()
    {
        $this->id = (new Database('solicitacao'))->insert(
            [
                "aluno" => $this->aluno,
                "id_lista" => $this->idLista,
                "id_edit" => $this->idEdit,
                "lista" => $this->lista,
                "acao" => $this->acao,
                "motivo" => $this->motivo,
                "ativa" => $this->ativa,
                "aprovada" => $this->aprovada,
                "data_abertura" => $this->dataAbertura
            ]
        );
    }

    /**
     * Atualiza os dados do banco
     * @param array $values
     */
    public function atualizar($values)
    {
        return (new Database("solicitacao"))->update("id = ".$this->id, $values);
    }

    /**
     * Exclui a instância atual
     * @param array $values
     */
    public function excluir()
    {
        return (new Database("solicitacao"))->delete("id = ".$this->id);
    }

    /**
     * Processa dados PDOStatement para instâncias de Solicitation
     * @param \PDOStatement
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
            $itens[] = new self($result['id'], $result['aluno'], $result['id_lista'], $result['id_edit'], $result['lista'], $result['acao'], $result['motivo'], $result['ativa'], $result['aprovada'], $result['data_abertura'], $result['data_encerramento']);
        }

        return $itens;
    }
}

?>