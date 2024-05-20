<?php

namespace PMS;

use WebSocket\Client as WebClient;

class Client {

    private WebClient $connection;

    private function __construct(WebClient $connection) {
        $this->connection = $connection;
    }

    public static function create(string $dsn): self {
        $connection = new WebClient($dsn);;
        return new self($connection);
    }

    public function get(string $key): string {
        $this->connection->send("Hello WebSocket Server!");
        $response = $this->connection->receive();
        return "";
    }

    public function set(string $key, string $value): void {
        $this->connection->send("Hello WebSocket Server!");
    }
}