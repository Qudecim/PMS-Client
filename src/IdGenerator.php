<?php

namespace PMS;

class IdGenerator
{

    private const ID_LENGTH = 5;

    private function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function generate(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $randomString .= $characters[random_int(0,$charactersLength - 1)];
        }
        return $randomString;
    }

}