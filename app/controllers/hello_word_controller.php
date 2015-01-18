<?php

  class HelloWorldController extends BaseController{

    public static function index(){
        echo 'Tämä on etusivu!';
    }

    public static function sandbox(){
      // Testaa koodiasi täällä	
      self::render_view('helloworld.html');
    }
  }
