<?php

  $app->get('/', function() {
    HelloWorldController::index();
  });

  $app->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  $app->get('/lists', function() {
    HelloWorldController::list1();
  });
  
  $app->get('/login', function() {
    HelloWorldController::login();
  });
  
  $app->get('/products', function() {
    HelloWorldController::products();
  });
  
  $app->get('/products/all', function() {
    HelloWorldController::products();
  });
  
  $app->get('/products/favorites', function() {
    HelloWorldController::favorites();
  });
  
  $app->get('/lists/1', function() {
    HelloWorldController::list1();
  });
  
  $app->get('/lists/2', function() {
    HelloWorldController::list2();
  });
  
  $app->get('/products/1', function() {
    HelloWorldController::product1();
  });
