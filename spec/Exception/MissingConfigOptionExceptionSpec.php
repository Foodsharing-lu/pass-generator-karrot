<?php

namespace spec\App\Exception;

use App\Exception\MissingConfigOptionException;
use PhpSpec\ObjectBehavior;

class MissingConfigOptionExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(MissingConfigOptionException::class);
    }

    public function it_constructs_all_fields()
    {
        $this->beConstructedWith('a');
        $this->getMessage()->shouldReturn('a');
    }
}
