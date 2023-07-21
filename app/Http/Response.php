<?php

namespace App\Http;

class Response
{
    /**
     * Código do status HTTP
     * @var integer
     */
    private $httpCode = 200;

    /**
     * Cabeçalho da resposta
     * @var array
     */
    private $headers = [];

    /**
     * Tipo de conteúdo que está sendo retornado
     * @var string
     */
    private $contentType;

    /**
     * Conteúdo da resposta
     * @var mixed
     */
    private $content;

    /**
     * Construtor da classe
     * @param integer $httpCode
     * @param mixed $content
     * @param string $contentType
     */
    public function __construct($httpCode, $content, $contentType = "text/html")
    {
        $this->httpCode = $httpCode;
        $this->content  = $content;
        $this->setContentType($contentType);
    }

    /**
     * Altera o content-type da resposta
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeader("Content-Type", $contentType);
    }

    /**
     * Adiciona um resgistro ao cabeçalho da resposta
     * @param string $key
     * @param string $value
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Envia os headers ao navegador
     */
    private function sendHeaders()
    {
        http_response_code($this->httpCode);

        foreach ($this->headers as $key => $value)
        {
            header($key.":".$value);
        }
    }

    /**
     * Envia a resposta ao usuário
     */
    public function sendResponse()
    {
        $this->sendHeaders();

        switch ($this->contentType) {
            case "text/html": 
                echo $this->content;
                break;
                
            case "application/json": 
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                break;
        }
    }
}

?>