<?php

namespace spec\App;

use App\Authenticator;
use PhpSpec\ObjectBehavior;

class AuthenticatorSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('https://dev.karrot.world/api/');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Authenticator::class);
    }

    public function it_isLoggedIn()
    {
        $this->isLoggedIn()->shouldReturn(false);
    }

    public function it_logIn_empty_password()
    {
        $this->isLoggedIn()->shouldReturn(false);
        $this->shouldThrow('App\Exception\InvalidCredentialsException')->duringLogIn('a', '', array(1));
        $this->isLoggedIn()->shouldReturn(false);
    }

    public function it_logIn_invalid()
    {
        $this->isLoggedIn()->shouldReturn(false);
        $this->shouldThrow('App\Exception\InvalidCredentialsException')->duringLogIn('a', 'b', array(1));
        $this->isLoggedIn()->shouldReturn(false);
    }

    public function it_getUserId_not_set()
    {
        $this->shouldThrow('App\Exception\SessionException')->duringGetUserId();
    }

    public function it_getUserName_not_set()
    {
        $this->shouldThrow('App\Exception\SessionException')->duringGetUserName();
    }

    public function it_getUserPhotoUrl_not_set()
    {
        $this->shouldThrow('App\Exception\SessionException')->duringGetUserPhotoUrl();
    }

    public function it_logs_out_while_logged_out()
    {
        $this->isLoggedIn()->shouldReturn(false);
        $this->logOut();
        $this->isLoggedIn()->shouldReturn(false);
    }
}
