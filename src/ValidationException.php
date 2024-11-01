<?php

namespace DataDrivenDesign\VoiceDesigner;

use Httpful\Response;

class ValidationException extends \Exception
{
    private $response;

    public function __construct(Response $response)
    {
        parent::__construct($response->body->message);
        $this->response = $response;
    }

    public function getErrors()
    {
        return $this->response->body->errors;
    }
}
