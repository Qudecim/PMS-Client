<?php

namespace PMS;

class Response {
    private string $id;
    private string $value;
    private int $error;

    private function __construct(string $id, string $value, int $error)
    {
        $this->id = $id;
        $this->value = $value;
        $this->error = $error;
    }

    public static function createFromArray(array $item): self
    {
        return new self($item['i'], $item['v'], $item['e']);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function isError(): bool
    {
        return $this->error !== 0;
    }
}