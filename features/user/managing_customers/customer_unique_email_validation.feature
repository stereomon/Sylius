@managing_customers
Feature: Customer uniqueness of email validation
    In order to uniquely identify customer
    As an Administrator
    I want to be prevented from adding two customers with the same email

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Try to add a new customer with taken email
        Given the store has customer "f.baggins@example.com"
        And I want to create a new customer
        When I specify their first name as "Geralt"
        And I specify their last name as "Riv"
        And I specify their email as "f.baggins@example.com"
        And I try to add it
        Then I should be notified that email must be unique
        And there should still be only one customer with email "f.baggins@example.com"
