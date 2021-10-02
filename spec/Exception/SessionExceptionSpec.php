<?php

namespace spec\App\Exception;

use App\Exception\SessionException;
use PhpSpec\ObjectBehavior;

class SessionExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(SessionException::class);
    }

    public function it_constructs_all_fields()
    {
        $this->beConstructedWith('a');
        $this->getMessage()->shouldReturn('a');
    }
}
