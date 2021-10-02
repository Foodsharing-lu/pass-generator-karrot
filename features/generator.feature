Feature: Pass Generator

  Scenario: View the pass
    Given I log in
    When there is a pass
    Then the pass is shown

  Scenario: Generate the pass
    Given I log in
    And I have a full name
    And I have a photo
    When I generate the pass
    Then the pass is shown

  Scenario: Delete the pass
    Given I log in
    And the pass is shown
    When I delete the pass
    Then the pass is not shown
    And there is no pass
