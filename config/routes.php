<?php

  $app->get('/', function() {
      UserController::login();
  });
  
  $app->get('/users', function() {
      UserController::index();
  });
  
  $app->get('/user/:id', function($id) {
      UserController::show($id);
  });
  
  $app->get('/login', function() {
      UserController::login();
  });
  
  $app->post('/login', function() {
      UserController::handle_login();
  });
  
  $app->post('/logout', function() {
      UserController::logout();
  });
  
  $app->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  $app->get('/lists', function() {
      ShoppingListController::index();
  });
  
  $app->get('/list/:id', function($id) {
      ShoppingListController::show($id);
  });
  
  $app->get('/list/:id/set_active', function($id) {
      ShoppingListController::set_active($id);
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
  
  $app->post('/product/:id/fave', function($id) {
      ProductController::fave($id);
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
  
  $app->get('/product/:id/edit', function($id) {
      ProductController::edit($id);
  });
  
  $app->post('/product/:id/edit', function($id) {
      ProductController::update($id);
  }); 
  
  $app->post('/product/:id/delete', function($id) {
      ProductController::delete($id);
  });
