<?php

namespace spec\App;

use App\Config;
use PhpSpec\ObjectBehavior;

class ConfigSpec extends ObjectBehavior
{
    private const CONFIG_FILE_PATH = 'public/config/config.php';

    public function it_is_initializable()
    {
        $this->shouldHaveType(Config::class);
    }

    public function it_loads_config()
    {
        $this->load(self::CONFIG_FILE_PATH)->shouldHaveType('App\Config');
    }

    public function it_loads_non_existing_config()
    {
        $this->load('a')->shouldHaveType('App\Config');
    }

    public function it_misses_config_option()
    {
        $this->shouldThrow('App\Exception\MissingConfigOptionException')->duringGet('a');
    }
}
