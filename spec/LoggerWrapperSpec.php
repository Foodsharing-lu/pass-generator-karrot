<?php

namespace spec\App;

use App\LoggerWrapper;
use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\FailureException;

class LoggerWrapperSpec extends ObjectBehavior
{
    private const TEST_MESSAGE = 'Test';

    public function getMatchers(): array
    {
        return [
            'logFileLastMessageBeTestOfType' => function ($subject, $type) {
                $file = LOGGERWRAPPER::LOG_FILE_PATH;
                $lastLine = `tail -n 1 $file`;
                if (!str_contains($lastLine, $type)) {
                    throw new FailureException(
                        sprintf(
                            'the log message does not include the type "%s": "%s"',
                            $type,
                            $lastLine
                        )
                    );
                }
                if (!str_contains($lastLine, self::TEST_MESSAGE)) {
                    throw new FailureException(
                        sprintf(
                            'the log message does not include the message "%s": "%s"',
                            self::TEST_MESSAGE,
                            $lastLine
                        )
                    );
                }
                return true;
            }
        ];
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(LoggerWrapper::class);
    }

    public function it_returns_logger()
    {
        $this->getLogger()->shouldHaveType(\Monolog\Logger::class);
    }

    public function it_logs_info()
    {
        $this->info(self::TEST_MESSAGE)->shouldLogFileLastMessageBeTestOfType('INFO');
    }

    public function it_logs_warning()
    {
        $this->warning(self::TEST_MESSAGE)->shouldLogFileLastMessageBeTestOfType('WARN');
    }

    public function it_logs_error()
    {
        $this->error(self::TEST_MESSAGE)->shouldLogFileLastMessageBeTestOfType('ERROR');
    }
}
