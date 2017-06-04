Feature: Navigation
  In order to view behat report
  As a Jenkins user
  I need to be able to do a basic test

  Scenario: View Google homepage
    Given I am on "http://www.google.fr"
    Then I should see "google"