<?php

namespace spec\App;

use App\NameChecker;
use PhpSpec\ObjectBehavior;

class NameCheckerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(NameChecker::class);
    }

    public function it_has_no_name()
    {
        $this->hasMoreThanOneName('')->shouldReturn(false);
    }

    public function it_has_one_name()
    {
        $this->hasMoreThanOneName('a')->shouldReturn(false);
    }

    public function it_has_two_names()
    {
        $this->hasMoreThanOneName('a b')->shouldReturn(true);
    }
}
