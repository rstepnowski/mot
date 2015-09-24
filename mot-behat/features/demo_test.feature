Feature: Demo MOT Test
  As a Trainer
  I want to watch Testers perform a Demo MOT Test
  So that I can see how they interact with the system

  Scenario: Tester creates Demo MOT Test
    Given I am logged in as a Tester
    When I start a Demo MOT Test
    Then an MOT test number should be allocated

  Scenario Outline: Complete an MOT Test with No Odometer or Reading
    Given I start a Demo MOT test as a Tester
    And the Tester adds an Odometer Reading "<type>"
    And the Tester adds a Class 3-7 Roller Brake Test Result
    When the Tester Passes the Mot Test
    Then the MOT Test Status is "PASSED"
  Examples:
    | type     |
    | NOT READ |
    | NO METER |

  Scenario Outline: Complete MOT Test with Specific Mot Mileage/Kilometre
    Given I am logged in as a Tester
    When I start a Demo MOT Test
    And the Tester adds an Odometer Reading of <distance>
    And the Tester adds a Class 3-7 Roller Brake Test Result
    When the Tester Passes the Mot Test
    Then the MOT Test Status is "PASSED"
  Examples:
    | distance |
    | 499 km   |
    | 501 mi   |

  Scenario: Complete MOT Test with Decelerometer
    Given I am logged in as a Tester
    When I start a Demo MOT Test
    And the Tester adds an Odometer Reading of 1000 mi
    And the Tester adds a Class 3-7 Decelerometer Brake Test
    When the Tester Passes the Mot Test
    Then the MOT Test Status is "PASSED"

  Scenario: Add a Reason for Rejection
    Given I start a Demo MOT test as a Tester
    And the Tester adds an Odometer Reading of 1000 mi
    And the Tester adds a Class 3-7 Decelerometer Brake Test
    And the Tester adds a Reason for Rejection
    When the Tester Fails the Mot Test
    Then the MOT Test Status is "FAILED"

  Scenario: Unauthenticated user attempts to create Demo MOT Test
    Given I am not logged in
    When I attempt to create a Demo MOT Test
    Then I should receive an Unauthorised response

  @defect @VM-7620 @disabled
  Scenario: Special Notice Broadcast user attempts to create Demo MOT Test
    Given I am logged in as a Special Notice broadcast user
    When I attempt to create a Demo MOT Test
    Then an MOT test number should not be allocated

  @defect @VM-7620 @disabled
  Scenario: Area Office user attempts to create Demo MOT Test
    Given I am logged in as an Area Office User
    When I attempt to create a Demo MOT Test
    Then an MOT test number should not be allocated
