Feature: Login system
  Use the Karrot API to
  - log in
  - get user details
  - check whether I am part of the group configured
  - log out

  Scenario: Log in
    Given I am logged out
    When I log in
    Then I am logged in

  Scenario: Log out
    Given I am logged in
    When I log out
    Then I am logged out
