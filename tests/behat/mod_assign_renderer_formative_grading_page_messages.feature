@theme @theme_solent @sol @javascript @mod_assign
Feature: Guidance message is displayed to those who can view the grading page
  In order to warn that Formative assignment grades are not uploaded to SRS
  As a teacher
  I should see some guidance on a Formative assignment page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course1  | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config | value  | plugin |
      | theme  | solent |        |

  Scenario: View Summative assignment grading page with no message
    Given the following "activities" exist:
      | activity | name           | course | idnumber   |
      | assign   | Quercus1       | C1     | Quercus1   |
    And I am on the "Quercus1" Activity page logged in as teacher1
    When I follow "View all submissions"
    Then I should not see "The marks for these assignments will not be uploaded to Quercus or Gateway(SITS) as this is not a Summative Assignment."

  Scenario: Formative assignment page warning
    Given the following "activities" exist:
      | activity | name           | course | idnumber   |
      | assign   | Formative1     | C1     |            |
    And I log in as "teacher1"
    And I am on "Course1" course homepage
    And I follow "Formative1"
    When I follow "View all submissions"
    Then I should see "The marks for these assignments will not be uploaded to Quercus or Gateway(SITS) as this is not a Summative Assignment."
