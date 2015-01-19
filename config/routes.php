<?php

  $app->get('/', function() {
    HelloWorldController::index();
  });

  $app->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  $app->get('/lists', function() {
    HelloWorldController::lists();
  });
  
  $app->get('/login', function() {
    HelloWorldController::login();
  });
  
  $app->get('/products', function() {
    HelloWorldController::products();
  });
  
  $app->get('/list/1', function() {
    HelloWorldController::list1();
  });
  
  $app->get('/list/2', function() {
    HelloWorldController::list2();
  });
