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

    public function get(string $key): Response {
        $request = new Request($this->genId(), 'g', $key, '');
        $this->connection->send($request->asString());
        $responseString = $this->connection->receive();
        var_dump($responseString);
        $rBody = json_decode($responseString, true);
        return Response::createFromArray($rBody);
    }

    public function set(string $key, string $value): void {
        $request = new Request($this->genId(), 's', $key, $value);
        $this->connection->send($request->asString());
        $responseString = $this->connection->receive();
    }

    private function genId(): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 5; $i++) {
            $randomString .= $characters[random_int(0,$charactersLength)];
        }
        return $randomString;
    }
}