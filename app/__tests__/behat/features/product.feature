Feature: Product
    In order to show product
    As an API client
    I need to be able to get products

    Scenario: Getting product by id
      Given the "Accept" request header is "application/json"
      When I request "/api/products/1"
      Then the response code is 200