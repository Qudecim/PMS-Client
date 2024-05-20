<?php

namespace PMS;

class Request {
    private string $id;
    private string $method;
    private string $key;
    private string $value;

    public function __construct(string $id, string $method, string $key, string $value)
    {
        $this->id = $id;
        $this->method = $method;
        $this->key = $key;
        $this->value = $value;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function asString(): string
    {
        return json_encode([
            'i' => $this->getId(),
            'm' => $this->getMethod(),
            'k' => $this->getKey(),
            'v' => $this->getVAlue()
        ]);
    }
}