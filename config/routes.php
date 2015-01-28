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
      ProductController::index();
  });
  
  $app->post('/products', function() {
      ProductController::store();
  });
  
  $app->get('/product/new', function() {
      ProductController::create();
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
  
  $app->get('/product/:id', function($id) {
      ProductController::show($id);
  });
  
  $app->post('/product/:id/delete', function($id) {
      ProductController::delete($id);
  });
