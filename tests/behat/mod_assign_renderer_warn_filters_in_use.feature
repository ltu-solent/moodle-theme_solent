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
    And I navigate to "Submissions" in current page administration
    And I click on "Filter by name" "combobox"
    When I select "A" in the "First name" "core_course > initials bar"
    And I press "Apply"
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And I should not see "Clear all filters"
    And I should not see "You are not displaying all users and will not be able to release your grades"
    # Return to the page without the filters in the url (this will check the preferences).
    And I am on "Course1" course homepage
    And I follow "Formative1"
    When I navigate to "Submissions" in current page administration
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And I should not see "Clear all filters"
    And I should not see "You are not displaying all users and will not be able to release your grades"
    When I click on "Clear all" "link" in the ".tertiary-navigation" "css_element"
    Then I should see "Abe Lincoln"
    And I should see "Celia Fate"
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And I set the field "Marking state" in the ".extrafilters .dropdown-menu" "css_element" to "In review"
    When I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    Then I should not see "You are not displaying all users and will not be able to release your grades"
    And I should not see "Clear all filters"

  Scenario: Summative assignment shows messages
    Given I am on the "Quercus1" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I click on "Filter by name" "combobox"
    When I select "A" in the "First name" "core_course > initials bar"
    And I press "Apply"
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And I should see "Clear all filters"
    And I should see "You are not displaying all users and will not be able to release your grades"
    # Return to the page without the filters in the url (this will check the preferences).
    And I am on the "Quercus1" Activity page
    And I navigate to "Submissions" in current page administration
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And I should see "Clear all filters"
    And I should see "You are not displaying all users and will not be able to release your grades"
    When I click on "Clear all" "link" in the ".tertiary-navigation" "css_element"
    Then I should see "Abe Lincoln"
    And I should see "Celia Fate"
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And I set the field "Marking state" in the ".extrafilters .dropdown-menu" "css_element" to "In review"
    When I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    Then I should see "You are not displaying all users and will not be able to release your grades"
    And I should see "Clear all filters"
    When I follow "Clear all filters"
    Then I should not see "You are not displaying all users and will not be able to release your grades"
