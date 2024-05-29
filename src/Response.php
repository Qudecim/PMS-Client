<?php

namespace PMS;

class Response {
    private string $id;
    private string $value;
    private array $items;
    private int $error;

    private function __construct(string $id, string $value, array $items, int $error)
    {
        $this->id = $id;
        $this->value = $value;
        $this->items = $items;
        $this->error = $error;
    }

    public static function createFromArray(array $item): self
    {
        return new self($item['i'], $item['v'], $item['t'] ?? [], $item['e']);
    }

    public static function createError(string $id, int $error): self
    {
        return new self($id, '', [], $error);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getItems(): array
    {
        return $this->items;
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