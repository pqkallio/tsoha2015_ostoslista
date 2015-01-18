<?php

  class DatabaseConfig{

    // Valitse käytettävä tietokanta (psql tai mysql)
    private static $use_database = 'psql';

    // Jos käytät MySQL-tietokantaa, aseta oikeat arvot KAYTTAJATUNNUS-kohtaan (käyttäjätunnuksesi) ja SALASANA-kohtaan (tietokantasi pääkäyttäjän salasana)
    private static $connection_config = array(
      'mysql' => array(
        'resource' => 'mysql:unix_socket=/home/KAYTTAJATUNNUS/mysql/socket;dbname=test',
        'username' => 'root',
        'password' => 'SALASANA'
      ),
      'psql' => array(
        'resource' => 'pgsql:'
      )
    );

    public static function connection_config(){
      $config = array(
        'db' => self::$use_database,
        'config' => self::$connection_config[self::$use_database]
      );

      return $config;
    }

  }
