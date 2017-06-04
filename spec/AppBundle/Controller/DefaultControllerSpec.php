<?php

namespace spec\AppBundle\Controller;

use AppBundle\Controller\DefaultController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DefaultControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DefaultController::class);
    }
}
