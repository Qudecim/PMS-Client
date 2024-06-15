<?php

namespace PMS;

use Exception;
use WebSocket\BadOpcodeException;
use WebSocket\Client as WebClient;

class Client {

    private WebClient $connection;
    private IdGenerator $idGenerator;

    private array $data;

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
        return $this->makeQuery($key, 'g');
    }

    public function set(string $key, string $value): void {
        $request = new Request($this->idGenerator->generate(), 's', $key, $value);
        try {
            $this->connection->send($request->asString());
        } catch (Exception $e) {}
    }

    public function increment(string $key): Response {
        return $this->makeQuery($key, 'i');
    }

    public function decrement(string $key): Response {
        return $this->makeQuery($key, 'd');
    }

    public function push(string $key, string $itemKey): Response {
        return $this->makeQuery($key, 'p', $itemKey);
    }

    public function pull(string $key): Response {
        return $this->makeQuery($key, 'u');
    }

    private function wait(string $id): bool
    {
        for ($i = 0; $i < 10; $i++) {
            $responseString = $this->connection->receive();
            $response = $this->getResponse($responseString);
            $this->data[$response->getId()] = $response;
            if ($id == $response->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @throws BadOpcodeException
     */
    private function send(Request $request): void
    {
        $this->connection->send($request->asString());
    }

    private function getResponse(string $responseString): Response
    {
        $rBody = json_decode($responseString, true);
        return Response::createFromArray($rBody);
    }

    private function makeQuery(string $key, string $method, string $value = ''): Response
    {
        $request = new Request($this->idGenerator->generate(), $method, $key, $value);

        try {
            $this->send($request);

            if (isset($this->data[$request->getId()])) {
                return $this->data[$request->getId()];
            }

            if ($this->wait($request->getId())) {
                $response = $this->data[$request->getId()];
                unset($this->data[$request->getId()]);
                return $response;
            }
        } catch (Exception $e) {
            return Response::createError($request->getId(), $e->getMessage());
        }


        return Response::createError($request->getId(), 'Empty response');
    }

}