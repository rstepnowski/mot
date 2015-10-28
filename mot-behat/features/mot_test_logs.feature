Feature: MOT Logs
  As a User
  I want to download MOT Test Logs
  So that I can view MOT Test Log data

  Scenario: Area Office Downloads Today's Test Logs
    Given I am logged in as a Tester
    And I create an mot test
    And I am logged in as an Area Office User
    When I download my test logs for today
    Then I will see the correct MOT Test Log Data