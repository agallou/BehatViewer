@user
Feature: Login

    @reset @fixture:user.sql
    Scenario: Login with bad credentials
        Given I am not logged in
          And I am on the homepage
          And I follow "Log in"
          And I press "Log in"
         Then I should see "Bad credentials"

        Given I fill in "username" with "foo"
          And I fill in "password" with "bar"
          And I press "Log in"
         Then I should see "Bad credentials"

    @javascript
    Scenario: Login with existing credentials
        Given I am not logged in
          And I am on the homepage
          And I follow "Log in"
          And I fill in "username" with "user"
          And I fill in "password" with "user"
          And I press "Log in"
         Then I should be on "/project/create"
          And I should see "Logged in as user"

        Given I follow "Logged in as user"
         Then I should see "Profile"
          And I should see "Config"
          And I should see "Logout"

        Given I am not logged in
          And I am a logged in admin
          And I am on the homepage
          And I follow "Logged in as admin"
         Then I should see "Profile"
          And I should see "Admin"
          And I should see "Config"
          And I should see "Logout"

    @javascript @reset
    Scenario: Logout
        Given I am a logged in user
          And I am on the homepage
          And I follow "Logged in as user"
          And I follow "Logout"
         Then I should be on "/login"

    @reset @javascript @fixture:single-project.sql @fixture:single-build.sql
    Scenario: Logout
        Given I am a logged in user
          And I am on the homepage
          And I follow "Logged in as user"
          And I follow "Logout"
         Then I should be on "/user/foo-bar"
