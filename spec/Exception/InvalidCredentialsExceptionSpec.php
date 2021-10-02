<?php

namespace spec\App\Exception;

use App\Exception\InvalidCredentialsException;
use PhpSpec\ObjectBehavior;

class InvalidCredentialsExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(InvalidCredentialsException::class);
    }

    public function it_constructs_all_fields()
    {
        $this->beConstructedWith('a', new \Exception());
        $this->getMessage()->shouldReturn('a');
        $this->getPrevious()->shouldReturnAnInstanceOf(\Exception::class);
    }
}
