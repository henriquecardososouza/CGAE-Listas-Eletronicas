<?php

namespace App\Model\Entity;

use App\Utils\Database\Database;

class Admin
{
    /**
     * ID do usuário
     * @var int
     */
    public int $id;

    /**
     * Nome do administrador
     * @var string
     */
    public string $nome;

    /**
     * Email do administrador
     * @var string
     */
    public string $email;

    /**
     * Senha de acesso do administrador
     * @var string
     */
    public string $senha;

    /**
     * Construtor da classe
     * @param string $nome
     * @param string $email
     * @param string $senha
     */
    public function __construct($id = -1, $nome = null, $email = null, $senha = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
    }

    /**
     * Retorna um administrador com base no seu email
     * @param string $email
     * @return Admin
     */
    public static function getAdminByEmail($email)
    {
        return self::processData(self::getAdmins("email = '".$email."'"))[0] ?? null;
    }

    /**
     * Retorna um administrador com base no seu id
     * @param int $id
     * @return Admin
     */
    public static function getAdminById($id)
    {
        return self::processData(self::getAdmins("id = ".$id))[0] ?? null;
    }


    /**
     * Retorna administardores
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return \PDOStatement
     */
    public static function getAdmins($where = null, $order = null, $limit = null, $field = "*")
    {
        return (new Database("admin"))->select($where, $order, $limit, $field);
    }

    /**
     * Cadastra o administrador atual no banco de dados
     * @return bool
     */
    public function cadastrar()
    {
        $this->id = (new Database("admin"))->insert([
            "nome" => $this->nome,
            "email" => $this->email,
            "senha" => $this->senha,
        ]);

        return true;
    }

    /**
     * Atualiza os dados do banco
     * @return bool
     */
    public function atualizar()
    {
        return (new Database("admin"))->update("id = '".$this->id."'", [
            "nome" => $this->nome,
            "email" => $this->email,
            "senha" => $this->senha,
        ]);
    }

    /**
     * Exclui o administrador do banco
     * @return bool
     */
    public function excluir()
    {
        return (new Database("admin"))->delete("id = ".$this->id);
    }

    /**
     * Processa dados PDOStatement para instâncias de Admin
     * @param \PDOStatement
     * @return array
     */
    public static function processData($data)
    {
        $results = $data->fetchAll();

        if (empty($results))
        {
            return null;
        }

        $itens = [];

        foreach ($results as $result)
        {
            $itens[] = new self($result['id'], $result['nome'], $result['email'], $result['senha']);
        }

        return $itens;
    }
}

?>