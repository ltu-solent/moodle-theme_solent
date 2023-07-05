@theme @theme_solent @sol @javascript @mod_assign
Feature: Select all grades state changes depending on filters and assignment type
  In order to prevent partial grade release
  As a teacher
  The "Select all" option is controlled depending on any active filters in use on a Summative assignment

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

  Scenario: Allow select single and select all on Formative assignments when active filters
    Given I log in as "teacher1"
    And I am on "Course1" course homepage
    And I follow "Formative1"
    And I follow "View all submissions"
    When I click on "A" "link" in the ".initialbar.firstinitial .page-item.A" "css_element"
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And "[data-quercus='disable-selectall']" "css_element" should not exist
    And the "[name=selectall]" "css_element" should be enabled
    And the "Select Abe Lincoln" "checkbox" should be enabled

  Scenario: Disallow select single and select all on Summative assignments when active filters
    Given I log in as "teacher1"
    And I am on "Course1" course homepage
    And I follow "Quercus1"
    And I follow "View all submissions"
    When I click on "A" "link" in the ".initialbar.firstinitial .page-item.A" "css_element"
    Then I should see "Abe Lincoln"
    And I should not see "Ben Franklin"
    And "[data-quercus='disable-selectall']" "css_element" should exist
    And the "[name=selectall]" "css_element" should be disabled
    And the "Select Abe Lincoln" "checkbox" should be disabled
    When I click on "All" "link" in the ".initialbar.firstinitial" "css_element"
    Then the "[name=selectall]" "css_element" should be enabled
    And the "Select Abe Lincoln" "checkbox" should be enabled

  Scenario: Select one, select all on Summative assignments
    Given I log in as "teacher1"
    And I am on "Course1" course homepage
    And I follow "Quercus1"
    And I follow "View all submissions"
    Then "[data-quercus='disable-selectall']" "css_element" should not exist
    And the "[name=selectall]" "css_element" should be enabled
    And the "Select Abe Lincoln" "checkbox" should be enabled
    And the field "Select Abe Lincoln" matches value ""
    When I set the field "Select Abe Lincoln" to "checked"
    Then the field "Select Abe Lincoln" matches value "checked"
    And the field "Select Ben Franklin" matches value "checked"
