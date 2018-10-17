Feature: Section
  In order to use section resource
  As an API client
  I need to be able to get, add, update, delete sections

  Scenario: Getting section by id
    Given the "Accept" request header is "application/json"
    When I request "/api/sections/1"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":{"id":"1","name":"Food"}}
      """

  Scenario: Getting all section in JSON AST
    Given the "Accept" request header is "application/json"
    When I request "/api/sections"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":[{"id":"1","name":"Food","level":"0","children":{"1":{"id":"2","name":"Vegetable","level":"1","children":{"2":{"id":"3","name":"Red","level":"2","children":{"3":{"id":"4","name":"Sour","level":"3"},"4":{"id":"5","name":"Sweet","level":"3"}}},"5":{"id":"6","name":"Green","level":"2","children":{"6":{"id":"7","name":"Sour","level":"3"},"7":{"id":"8","name":"Sweet","level":"3"}}}}},"8":{"id":"9","name":"Fruit","level":"1","children":{"9":{"id":"10","name":"Red","level":"2","children":{"10":{"id":"11","name":"Sour","level":"3"},"11":{"id":"12","name":"Sweet","level":"3"}}},"12":{"id":"13","name":"Green","level":"2","children":{"13":{"id":"14","name":"Sour","level":"3"},"14":{"id":"15","name":"Sweet","level":"3"}}}}}}}]}
      """

  Scenario: Getting section by id If NOT FOUND
    Given the "Accept" request header is "application/json"
    When I request "/api/sections/10000"
    Then the response code is 404
    Then the response body is:
      """
      {"meta":{"number_of_records":0},"payload":{}}
      """

  Scenario: Getting all section in pretty view (html list)
    Given the "Accept" request header is "application/json"
    When I request "/api/sections?pretty"
    Then the response code is 200
    Then the response body is:
      """
      <ul><li>id [1] Food<ul><li>id [2] Vegetable<ul><li>id [3] Red<ul><li>id [4] Sour</li><li>id [5] Sweet</li></ul></li><li>id [6] Green<ul><li>id [7] Sour</li><li>id [8] Sweet</li></ul></li></ul></li><li>id [9] Fruit<ul><li>id [10] Red<ul><li>id [11] Sour</li><li>id [12] Sweet</li></ul></li><li>id [13] Green<ul><li>id [14] Sour</li><li>id [15] Sweet</li></ul></li></ul></li></ul></li></ul>
      """

  Scenario: Create new section
    Given the request body is:
    """
    {
        "name":"New Section",
        "parent_id":"10"
    }
    """
    When I request "/api/sections" using HTTP POST
    Then the response code is 201
    Then the "Location" response header is "/api/sections/16"
    When I request "/api/sections/16"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":{"id":"16","name":"New Section"}}
      """

  Scenario: Update section
    Given the request body is:
    """
    {"name":"Updated Section"}
    """
    When I request "/api/sections/1" using HTTP PUT
    Then the response code is 201
    Then the "Location" response header is "/api/sections/1"
    When I request "/api/sections/1"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":{"id":"1","name":"Updated Section"}}
      """

  Scenario: Delete section
    When I request "/api/sections/9" using HTTP DELETE
    Then the response code is 204
    When I request "/api/sections"
    Then the response code is 200
    Then the response body is:
      """
      {"meta":{"number_of_records":1},"payload":[{"id":"1","name":"Food","level":"0","children":{"1":{"id":"2","name":"Vegetable","level":"1","children":{"2":{"id":"3","name":"Red","level":"2","children":{"3":{"id":"4","name":"Sour","level":"3"},"4":{"id":"5","name":"Sweet","level":"3"}}},"5":{"id":"6","name":"Green","level":"2","children":{"6":{"id":"7","name":"Sour","level":"3"},"7":{"id":"8","name":"Sweet","level":"3"}}}}}}}]}
      """
    When I request "/api/products?page1&per_page=10"
    Then the response body is:
      """
      {"meta":{"number_of_records":5},"payload":[{"id":"1","name":"FoodVegRedSour","availability":"1","price":"1.99","brand":"Russia Kolhoz"},{"id":"2","name":"FoodVegRedSweet","availability":"1","price":"1.50","brand":"Gruzin"},{"id":"3","name":"FoodVegGreenSour","availability":"1","price":"1.50","brand":"Russia Kolhoz"},{"id":"4","name":"FoodVegGreenSweet","availability":"1","price":"1.50","brand":"Country"},{"id":"9","name":"FoodVegRedSour2","availability":"1","price":"1.99","brand":"Russia Kolhoz"}]}
      """