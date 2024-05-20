<?php

namespace PMS;

use WebSocket\Client as WebClient;

class Client {

    private WebClient $connection;
    private IdGenerator $idGenerator;

    private function __construct(WebClient $connection, IdGenerator $idGenerator)
    {
        $this->connection = $connection;
        $this->idGenerator = $idGenerator;
    }

    public static function create(string $dsn): self {
        $connection = new WebClient($dsn);;
        $idGenerator = IdGenerator::create();
        return new self($connection, $idGenerator);
    }

    public function get(string $key): Response {
        $request = new Request($this->idGenerator->generate(), 'g', $key, '');

        try {
            $this->connection->send($request->asString());
            $responseString = $this->connection->receive();
        } catch (\Exception $_) {
            return Response::createError($request->getId(), Errors::ERROR_SEND_FAILED);
        }

        if (!is_string($responseString)) {
            return Response::createError($request->getId(), Errors::ERROR_BODY_INCORRECT);
        }

        $rBody = json_decode($responseString, true);
        if ($rBody === null) {
            return Response::createError($request->getId(), Errors::ERROR_BODY_INCORRECT_DECODE);
        }

        $response = Response::createFromArray($rBody);
        if ($request->getId() !== $response->getId()) {
            return Response::createError($request->getId(), Errors::ERROR_DIFFERENT_ANSWER);
        }

        return $response;
    }

    public function set(string $key, string $value): void {
        $request = new Request($this->idGenerator->generate(), 's', $key, $value);
        try {
            $this->connection->send($request->asString());
        } catch (\Exception $e) {}
    }
}