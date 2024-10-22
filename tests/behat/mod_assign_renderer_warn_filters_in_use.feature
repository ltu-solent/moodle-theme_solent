@theme @theme_solent @sol @javascript @mod_assign
Feature: Show warning on grading page when users are using filters
  In order to prevent partial grade release
  As a teacher
  I should see a warning when I've used any filters for a Summative assignment

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course1  | C1        | 0        |
    And the following "users" exist:
      | username  | firstname | lastname | email                 |
      | teacher1  | Teacher   | 1        | teacher1@example.com  |
      | alincoln  | Abe       | Lincoln  | alincoln@example.com  |
      | bfranklin | Ben       | Franklin | bfranklin@example.com |
      | cfate     | Celia     | Fate     | cfate@example.com     |
      | sday      | Sunny     | Day      | sday@example.com      |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | alincoln  | C1     | student        |
      | bfranklin | C1     | student        |
      | cfate     | C1     | student        |
      | sday      | C1     | student        |
    And the following "activities" exist:
      | activity | name           | course | idnumber   | markingworkflow | blindmarking |
      | assign   | Quercus1       | C1     | Quercus1   | 1               | 0            |
      | assign   | Formative1     | C1     |            | 1               | 0            |
    And the following config values are set as admin:
      | config | value  | plugin |
      | theme  | solent |        |

  Scenario: Formative assignment shows no message
    Given I log in as "teacher1"
    And I am on "Course1" course homepage
    And I follow "Formative1"
    And I follow "View all submissions"
    When I click on "A" "link" in the ".initialbar.firstinitial .page-item.A" "css_element"
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And I should not see "Reset your table preferences"
    And I should not see "You are not displaying all users and will not be able to release your grades"
    # Return to the page without the filters in the url (this will check the preferences).
    And I am on "Course1" course homepage
    And I follow "Formative1"
    When I follow "View all submissions"
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And I should not see "Reset your table preferences"
    And I should not see "You are not displaying all users and will not be able to release your grades"
    When I click on "All" "link" in the ".initialbar.firstinitial" "css_element"
    Then I should see "Abe Lincoln"
    And I should see "Celia Fate"
    When I set the field "Workflow filter" to "In review"
    Then I should not see "You are not displaying all users and will not be able to release your grades"
    And I should not see "Set all Options to \"No filter\""

  Scenario: Summative assignment shows messages
    Given I log in as "teacher1"
    And I am on "Course1" course homepage
    And I follow "Quercus1"
    And I follow "View all submissions"
    When I click on "A" "link" in the ".initialbar.firstinitial .page-item.A" "css_element"
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And I should see "Reset your table preferences"
    And I should see "You are not displaying all users and will not be able to release your grades"
    # Return to the page without the filters in the url (this will check the preferences).
    And I am on "Course1" course homepage
    And I follow "Quercus1"
    When I follow "View all submissions"
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And I should see "Reset your table preferences"
    And I should see "You are not displaying all users and will not be able to release your grades"
    When I click on "All" "link" in the ".initialbar.firstinitial" "css_element"
    Then I should see "Abe Lincoln"
    And I should see "Celia Fate"
    When I set the field "Workflow filter" to "In review"
    Then I should see "You are not displaying all users and will not be able to release your grades"
    And I should see "Set all Options to \"No filter\""
    When I set the field "Workflow filter" to ""
    Then I should not see "You are not displaying all users and will not be able to release your grades"
