Feature: Product
  In order to use product resource
  As an API client
  I need to be able to get, add, update, delete products

  Scenario: Getting product by id
    Given the "Accept" request header is "application/json"
    When I request "/api/products/1"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":{"id":"1","name":"FoodVegRedSour","availability":"1","price":"1.99","brand":"Russia Kolhoz"}}
      """

  Scenario: Getting product by id If NOT FOUND
    Given the "Accept" request header is "application/json"
    When I request "/api/products/10000"
    Then the response code is 404
    Then the response body is:
      """
      {"meta":{"number_of_records":0},"payload":{}}
      """

  Scenario: Getting all products on page 1 and per_page 5
    Given the "Accept" request header is "application/json"
    When I request "/api/products?page=1&per_page=5"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":5},"payload":[{"id":"1","name":"FoodVegRedSour","availability":"1","price":"1.99","brand":"Russia Kolhoz"},{"id":"2","name":"FoodVegRedSweet","availability":"1","price":"1.50","brand":"Gruzin"},{"id":"3","name":"FoodVegGreenSour","availability":"1","price":"1.50","brand":"Russia Kolhoz"},{"id":"4","name":"FoodVegGreenSweet","availability":"1","price":"1.50","brand":"Country"},{"id":"5","name":"FoodFruitRedSour","availability":"1","price":"1.50","brand":"Dacha"}]}
      """

  Scenario: Getting products by name
    Given the "Accept" request header is "application/json"
    When I request "/api/products?name=FoodFruitRedSour"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":[{"id":"5","name":"FoodFruitRedSour","availability":"1","price":"1.50","brand":"Dacha"}]}
      """

  Scenario: Getting products by brand
    Given the "Accept" request header is "application/json"
    When I request "/api/products?brand=Gruzin"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":[{"id":"2","name":"FoodVegRedSweet","availability":"1","price":"1.50","brand":"Gruzin"}]}
      """

  Scenario: Getting products by multy brand
    Given the "Accept" request header is "application/json"
    When I request "/api/products?brand=Gruzin|Russia Kolhoz"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":4},"payload":[{"id":"1","name":"FoodVegRedSour","availability":"1","price":"1.99","brand":"Russia Kolhoz"},{"id":"2","name":"FoodVegRedSweet","availability":"1","price":"1.50","brand":"Gruzin"},{"id":"3","name":"FoodVegGreenSour","availability":"1","price":"1.50","brand":"Russia Kolhoz"},{"id":"9","name":"FoodVegRedSour2","availability":"1","price":"1.99","brand":"Russia Kolhoz"}]}
      """

  Scenario: Getting products by multy params
    Given the "Accept" request header is "application/json"
    When I request "/api/products?availability=1&price=1.50&brand=Dacha"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":[{"id":"5","name":"FoodFruitRedSour","availability":"1","price":"1.50","brand":"Dacha"}]}
      """

  Scenario: Getting products by piece of name
    Given the "Accept" request header is "application/json"
    When I request "/api/products?name=%Veg%"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":5},"payload":[{"id":"1","name":"FoodVegRedSour","availability":"1","price":"1.99","brand":"Russia Kolhoz"},{"id":"2","name":"FoodVegRedSweet","availability":"1","price":"1.50","brand":"Gruzin"},{"id":"3","name":"FoodVegGreenSour","availability":"1","price":"1.50","brand":"Russia Kolhoz"},{"id":"4","name":"FoodVegGreenSweet","availability":"1","price":"1.50","brand":"Country"},{"id":"9","name":"FoodVegRedSour2","availability":"1","price":"1.99","brand":"Russia Kolhoz"}]}
      """

  Scenario: Getting products from section by id section
    Given the "Accept" request header is "application/json"
    When I request "/api/sections/4/products"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":2},"payload":[{"id":"1","name":"FoodVegRedSour","availability":"1","price":"1.99","brand":"Russia Kolhoz"},{"id":"9","name":"FoodVegRedSour2","availability":"1","price":"1.99","brand":"Russia Kolhoz"}]}
      """

  Scenario: Getting products with filter from section by id section
    Given the "Accept" request header is "application/json"
    When I request "/api/sections/4/products?page=1&per_page=2"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":2},"payload":[{"id":"1","name":"FoodVegRedSour","availability":"1","price":"1.99","brand":"Russia Kolhoz"},{"id":"9","name":"FoodVegRedSour2","availability":"1","price":"1.99","brand":"Russia Kolhoz"}]}
      """

  Scenario: Getting products from section by id section If NOT FOUND
    Given the "Accept" request header is "application/json"
    When I request "/api/sections/1/products"
    Then the response code is 404
    Then the response body is:
      """
      {"meta":{"number_of_records":0},"payload":{}}
      """

  Scenario: Getting products from all subsections where root section by id section
    Given the "Accept" request header is "application/json"
    When I request "/api/sections/10/sub/products"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":2},"payload":[{"id":"5","name":"FoodFruitRedSour","availability":"1","price":"1.50","brand":"Dacha"},{"id":"6","name":"FoodFruitRedSweet","availability":"1","price":"1.50","brand":"Polsky"}]}
      """

  Scenario: Getting products from all subsections where root section by id If NOT FOUND
    Given the "Accept" request header is "application/json"
    When I request "/api/sections/100/sub/products"
    Then the response code is 404
    Then the response body is:
      """
      {"meta":{"number_of_records":0},"payload":{}}
      """

  Scenario: Getting products with filter from all subsections where root section by id
    Given the "Accept" request header is "application/json"
    When I request "/api/sections/10/sub/products?name=FoodFruitRedSour"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":[{"id":"5","name":"FoodFruitRedSour","availability":"1","price":"1.50","brand":"Dacha"}]}
      """

  Scenario: Create new product
    Given the request body is:
    """
    {
        "name":"New Product",
        "availability":"1",
        "price":"5.50",
        "brand":"SomeBrand",
        "section_id":"15"
    }
    """
    When I request "/api/products" using HTTP POST
    Then the response code is 201
    Then the "Location" response header is "/api/products/10"
    When I request "/api/products/10"
    Then the response code is 200

  Scenario: Delete product
    When I request "/api/products/1" using HTTP DELETE
    Then the response code is 204
    When I request "/api/products/1"
    Then the response code is 404

  Scenario: Update product
    Given the request body is:
    """
    {"name":"Updated Product","availability":"1","price":"5.50","brand":"SomeBrand"}
    """
    When I request "/api/products/1" using HTTP PUT
    Then the response code is 201
    Then the "Location" response header is "/api/products/1"
    When I request "/api/products/1"
    Then the response code is 200

  Scenario: Getting Error when get product by invalid id
    Given the "Accept" request header is "application/json"
    When I request "/api/products/1a"
    Then the response code is 422
    Then the response body is:
      """
      {"meta":[],"payload":{},"errors":["\"1a\" must be a finite number","\"1a\" must be an integer number"]}
      """