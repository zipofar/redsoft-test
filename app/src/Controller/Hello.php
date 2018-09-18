<?php

namespace Zipofar\Controller;


use Symfony\Component\HttpFoundation\Response;

class Hello
{

    public function index($tokens)
    {
        echo 'Hello Controller';
    }
}