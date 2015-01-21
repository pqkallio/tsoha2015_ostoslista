<?php

  class HelloWorldController extends BaseController{

    public static function index(){
        echo 'Tämä on etusivu!';
    }
    
    public static function lists() {
        self::render_view('suunnitelmat/lists.html');
    }
    
    public static function login() {
        self::render_view('suunnitelmat/login.html');
    }
    
    public static function products() {
        self::render_view('suunnitelmat/products.html');
    }
    
    public static function favorites() {
        self::render_view('suunnitelmat/favorites.html');
    }
    
    public static function list1() {
        self::render_view('suunnitelmat/list1.html');
    }
    
    public static function list2() {
        self::render_view('suunnitelmat/list2.html');
    }
    
    public static function product1() {
        self::render_view('suunnitelmat/product1.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä	
      self::render_view('helloworld.html');
    }
  }
