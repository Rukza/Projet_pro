Feature:
  In order to navigate on the application
  As a user
  I want to access on different pages

  Scenario: A user can access to home page
    When a user sends a request to "/"
    Then the status code should be "200"

   Scenario: A anonymous user cannot access to link wristlet page
    When a user sends a request to "/account/link/link"
    Then the status code should be "302"
    Then I should be redirected to "/login"
    
