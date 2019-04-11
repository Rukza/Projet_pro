Feature: User authentication
  In order to gain access to user management area
  
  I need to be able to login

  Scenario: Logging in
    When there is an registred user "ju.casanova@free.fr" with password "Taz291283"
      And I am on "/login"
      When I fill in "email" with "ju.casanova@free.fr"
      And I fill in "Mot de passe" with "Taz291283"
      And I press "Connexion"
    Then I should be on "/account/profile/logged"