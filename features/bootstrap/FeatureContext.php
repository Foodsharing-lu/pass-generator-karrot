<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use App\Config;
use App\Interactor;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private const CONFIG_FILE_PATH = 'public/config/config.behat.php';

    private Interactor $interactor;
    private MockHandler $mock;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->mock = new MockHandler();
        $clientHandler = HandlerStack::create($this->mock);
        $this->interactor = new Interactor($clientHandler);
    }

    /**
     * @Given I am logged in
     */
    public function iAmLoggedIn()
    {
        return $this->interactor->isLoggedIn();
    }

    /**
     * @When I log in
     */
    public function iLogIn()
    {
        $this->mock->reset();
        $this->mock->append(
            new Response(200, [], '{"token":"c"}'),
            new Response(200, [], '{"id":0,"display_name":"d e","photo_urls":{"full_size":"public/assets/images/logo.png"}}'),
            new Response(200, [], '{"members":[0]}'),
        );
        $this->interactor->logIn('a', 'b');
    }

    /**
     * @When I log out
     */
    public function iLogOut()
    {
        $this->mock->reset();
        $this->mock->append(new Response(200));
        $this->interactor->logOut();
    }

    /**
     * @Then I am logged out
     */
    public function iAmLoggedOut()
    {
        return !$this->interactor->isLoggedIn();
    }

    /**
     * @Given there is a configuration file
     */
    public function thereIsAConfigurationFile()
    {
        if (!file_exists(self::CONFIG_FILE_PATH)) {
            $content = "<?php\n\nreturn " . var_export([], true) . ";\n";
            file_put_contents(self::CONFIG_FILE_PATH, $content);
        }
    }

    /**
     * @Given the option :option is configured to :value
     */
    public function theOptionIsConfiguredTo($option, $value)
    {
        $config = include self::CONFIG_FILE_PATH;

        if (!is_array($config)) {
            $config = [];
        }

        $config[$option] = $value;

        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";

        file_put_contents(self::CONFIG_FILE_PATH, $content);
    }

    /**
     * @When I load the configuration file
     */
    public function iLoadTheConfigurationFile()
    {
        $this->config = Config::load(self::CONFIG_FILE_PATH);
    }

    /**
     * @Then I should get :value as :option option
     */
    public function iShouldGetAsOption($value, $option)
    {
        $actual = $this->config->get($option);

        if (!strcmp($value, $actual) == 0) {
            throw new Exception("Expected {$actual} to be '{$option}'.");
        }
    }

    /**
     * @When there is a pass
     */
    public function thereIsAPass()
    {
        return $this->interactor->hasPass();
    }

    /**
     * @Then the pass is shown
     */
    public function thePassIsShown()
    {
        return file_exists($this->interactor->getAbsolutePathToPassImage());
    }

    /**
     * @Given I have a full name
     */
    public function iHaveAFullName()
    {
        return $this->interactor->hasUserFullName();
    }

    /**
     * @Given I have a photo
     */
    public function iHaveAPhoto()
    {
        return $this->interactor->hasUserPhoto();
    }

    /**
     * @When I generate the pass
     */
    public function iGenerateThePass()
    {
        $this->interactor->createPass();
    }

    /**
     * @When I delete the pass
     */
    public function iDeleteThePass()
    {
        $this->interactor->deletePass();
    }

    /**
     * @Then the pass is not shown
     */
    public function thePassIsNotShown()
    {
        return !file_exists($this->interactor->getAbsolutePathToPassImage());
    }

    /**
     * @Then there is no pass
     */
    public function thereIsNoPass()
    {
        return !$this->interactor->hasPass();
    }
}
