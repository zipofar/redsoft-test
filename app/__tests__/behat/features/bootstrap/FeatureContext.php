<?php

namespace ZipofarBehatFeatureContext;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Imbo\BehatApiExtension\Context\ApiContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends ApiContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through Behat.yml.
     */
    public function __construct()
    {
    }
}
