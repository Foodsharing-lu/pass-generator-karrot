Feature: Configuration files
  In order to configure my application
  As a developer
  I need to be able to store configuration options in a file

  Scenario: Getting the URL option
    Given there is a configuration file
    And the option 'karrot-url' is configured to 'https://dev.karrot.world'
    When I load the configuration file
    Then I should get 'https://dev.karrot.world' as 'karrot-url' option

  Scenario: Getting the group option
    Given there is a configuration file
    And the option 'karrot-group-id' is configured to '0'
    When I load the configuration file
    Then I should get '0' as 'karrot-group-id' option

  Scenario: Getting the pass folder option
    Given there is a configuration file
    And the option 'pass-folder-path' is configured to 'passes/'
    When I load the configuration file
    Then I should get 'passes/' as 'pass-folder-path' option

  Scenario: Getting the display error details option
    Given there is a configuration file
    And the option 'display-error-details' is configured to 'true'
    When I load the configuration file
    Then I should get 'true' as 'display-error-details' option

  Scenario: Getting the pass URL prefix option
    Given there is a configuration file
    And the option 'pass-url-prefix' is configured to 'https://example.net/passes/'
    When I load the configuration file
    Then I should get 'https://example.net/passes/' as 'pass-url-prefix' option

  Scenario: Getting the site name option
    Given there is a configuration file
    And the option 'site-name' is configured to 'Foodsharing Group'
    When I load the configuration file
    Then I should get 'Foodsharing Group' as 'site-name' option
