<?php

  // Root route

  $app->get('/', function() {
      UserController::login();
  });
  
  
  // Sign in, sign out and sign up routes
  
  $app->get('/login', function() {
      UserController::login();
  });
  
  $app->post('/login', function() {
      UserController::handle_login();
  });
  
  $app->post('/logout', function() {
      UserController::logout();
  });
  
  // User routes
  $app->get('/users', function() {
      UserController::index();
  });
  
  $app->get('/user/:id', function($id) {
      UserController::show($id);
  });
  
  $app->get('/signup', function() {
      UserController::signup();
  });
  
  $app->post('/signup', function() {
      UserController::store();
  });
  
  
  // ShoppingLists routes
  
  $app->get('/lists', function() {
      ShoppingListController::index();
  });
  
  $app->get('/list/start', function() {
      ShoppingListController::start();
  });
  
  $app->post('/list/new', function() {
      ShoppingListController::create();
  });
  
  $app->get('/list/:id', function($id) {
      ShoppingListController::show($id);
  });
  
  $app->post('/list/:id/update', function($id) {
      PurchaseController::set_purchase_date($id);
  });
  
  $app->post('/list/:id/delete', function($id) {
      ShoppingListController::destroy($id);
  });
  
  $app->get('/list/:id/set_active', function($id) {
      ShoppingListController::set_active($id);
  });
  
  $app->get('/list/:id/share', function($id) {
      ShoppingListController::share($id);
  });
  
  $app->post('/list/:id/share', function($id) {
      ShoppingListController::share_list($id);
  });
  
  
  // Products routes
  
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
  
  $app->get('/product/favorites', function() {
      ProductController::favorites();
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
      ProductController::destroy($id);
  });
  
  
  // Purchases routes

  $app->get('/purchases/:id/edit', function($id) {
      PurchaseController::edit($id);
  });
  
  $app->post('/purchases/:id/edit', function($id) {
      PurchaseController::update($id);
  });
  
  $app->post('/purchases', function() {
      PurchaseController::store();
  });
  
  $app->post('/purchases/:id/delete', function($id) {
      PurchaseController::destroy($id);
  });
  
  
  // Units routes
  
  $app->get('/units', function() {
      UnitController::index();
  });
  
  $app->get('/unit/new', function() {
      UnitController::create();
  });
  
  $app->get('/unit/:id', function($id) {
      UnitController::show($id);
  });
  
  $app->get('/unit/:id/edit', function($id) {
      UnitController::edit($id);
  });
  
  $app->post('/unit/:id/edit', function($id) {
      UnitController::update($id);
  });
  
  $app->post('/units/:id/delete', function($id) {
      UnitController::destroy($id);
  });
  
  $app->post('/units', function() {
      UnitController::store();
  });
  
  
  // Departments routes
  
  $app->get('/departments', function() {
      DepartmentController::index();
  });
  
  $app->get('/departments/new', function() {
      DepartmentController::create();
  });
  
  $app->get('/department/:id', function($id) {
      DepartmentController::show($id);
  });
  
  $app->get('/department/:id/edit', function($id) {
      DepartmentController::edit($id);
  });
  
  $app->post('/departments', function() {
      DepartmentController::store();
  });
  
  $app->post('/department/:id/edit', function($id) {
      DepartmentController::update($id);
  });
  
  $app->post('/department/:id/delete', function($id) {
      DepartmentController::destroy($id);
  });
  