<?php

namespace Tests;


class DummyController
{
    public function __invoke($token)
    {
        return $token;
    }

    public function index($token)
    {
        return $token;
    }
}