<?php

namespace PMS;

use Exception;
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

    /**
     * @throws Exception
     */
    public function get(string $key): Response {
        return $this->makeQuery($key, 'g');
    }

    public function set(string $key, string $value): void {
        $request = new Request($this->idGenerator->generate(), 's', $key, $value);
        try {
            $this->connection->send($request->asString());
        } catch (Exception $e) {}
    }

    /**
     * @throws Exception
     */
    public function increment(string $key): Response {
        return $this->makeQuery($key, 'i');
    }

    /**
     * @throws Exception
     */
    public function decrement(string $key): Response {
        return $this->makeQuery($key, 'd');
    }

    public function push(string $key, string $itemKey): Response {
        $request = new Request($this->idGenerator->generate(), 'p', $key, $itemKey);
        $this->send($request);

        if (isset($this->data[$request->getId()])) {
            return $this->data[$request->getId()];
        }

        if ($this->wait($request->getId())) {
            $response = $this->data[$request->getId()];
            unset($this->data[$request->getId()]);
            return $response;
        }

        throw new Exception('1');
    }

    public function pull(string $key): Response {
        $request = new Request($this->idGenerator->generate(), 'u', $key, '');
        $this->send($request);

         if (isset($this->data[$request->getId()])) {
             return $this->data[$request->getId()];
         }

         if ($this->wait($request->getId())) {
             $response = $this->data[$request->getId()];
             unset($this->data[$request->getId()]);
             return $response;
         }

         throw new Exception('1');
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

    private function send(Request $request): void
    {
        $this->connection->send($request->asString());
    }

    private function getResponse(string $responseString): Response
    {
        $rBody = json_decode($responseString, true);
        $response = Response::createFromArray($rBody);
        return $response;
    }

    /**
     * @throws Exception
     */
    private function makeQuery(string $key, string $method): Response
    {
        $request = new Request($this->idGenerator->generate(), $method, $key, '');
        $this->send($request);

        if (isset($this->data[$request->getId()])) {
            return $this->data[$request->getId()];
        }

        if ($this->wait($request->getId())) {
            $response = $this->data[$request->getId()];
            unset($this->data[$request->getId()]);
            return $response;
        }

        throw new Exception('1');
    }

}