<?php

namespace spec\App\Exception;

use App\Exception\UserNotInGroupException;
use PhpSpec\ObjectBehavior;

class UserNotInGroupExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(UserNotInGroupException::class);
    }

    public function it_constructs_all_fields()
    {
        $this->beConstructedWith('a');
        $this->getMessage()->shouldReturn('a');
    }
}
