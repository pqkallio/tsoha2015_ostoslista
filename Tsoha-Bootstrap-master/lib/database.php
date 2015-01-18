<?php

  require 'config/database.php';

  class DB{

      public static function connection(){
        $connection_config = DatabaseConfig::connection_config();
        $config = $connection_config['config'];

        try {
            if(isset($config['username'])){
              $connection = new PDO($config['resource'], $config['username'], $config['password']);
            }else{
              $connection = new PDO($config['resource']);
            }
        } catch (PDOException $e) {
            die('Virhe tietokantayhteydessä: ' . $e->getMessage());
        }

        return $connection;
      }

      public static function test_connection(){
        require 'vendor/ConnectionTest/connectiontest.php';

        exit();
      }

      public static function query($sql, $attributes = null){
        $connection = self::connection();

        $preparation = $connection->prepare($sql);

        try{
          if(!is_null($attributes)){
            $preparation->execute($attributes);
          }else{
            $preparation->execute();
          }
        } catch (Exception $e){
          die('Virhe tietokantakyselyssä: ' . $e->getMessage());
        }

        return $preparation->fetchAll();
      }

  }
