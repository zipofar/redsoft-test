<?php

namespace Tests;

use Psr\Container\ContainerInterface;

class DummyContainer implements ContainerInterface
{
    public function get($id)
    {
        return new $id ();
    }

    public function has($id)
    {
        // TODO: Implement has() method.
    }
}