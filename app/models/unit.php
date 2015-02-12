<?php

/**
 * Description of Unit
 *
 * @author kallionpetri
 */

class Unit extends BaseModel {
    public $id, $name_singular, $name_plural, $abbreviation; 
    
    public function __construct($attributes = null) {
        parent::__construct($attributes);
    }

    public static function all() {
        $units = array();
        $rows = DB::query('SELECT * FROM Unit ORDER BY name_singular ASC');
        
        foreach ($rows as $row) {
            $units[] = new Unit(array(
                'id' => $row['id'],
                'name_singular' => $row['name_singular'],
                'name_plural' => $row['name_plural'],
                'abbreviation' => $row['abbreviation']
            ));
        }
        
        return $units;
    }
    
    public static function find($id) {
        $rows = DB::query('SELECT * FROM Unit WHERE id = :id LIMIT 1', array('id' => $id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $unit = new Unit(array(
                'id' => $row['id'],
                'name_singular' => $row['name_singular'],
                'name_plural' => $row['name_plural'],
                'abbreviation' => $row['abbreviation']
            ));
        }
        
        return $unit;
    }
    
    public static function update($id, $attributes) {
        DB::query('UPDATE Unit '
                . 'SET name_singular = :name_singular, '
                . '    name_plural = :name_plural, '
                . '    abbreviation = :abbreviation '
                . 'WHERE id = :id',
                array('name_singular' => $attributes['name_singular'],
                      'name_plural'   => $attributes['name_plural'],
                      'abbreviation'  => $attributes['abbreviation'],
                      'id'            => $id));
    }
    
    public static function create($attributes) {
        $rows = DB::query('INSERT INTO Unit (name_singular, name_plural, abbreviation) '
                        . 'VALUES (:name_singular, :name_plural, :abbreviation) '
                        . 'RETURNING ID',
                        array('name_singular' => $attributes['name_singular'],
                              'name_plural'   => $attributes['name_plural'],
                              'abbreviation'  => $attributes['abbreviation']
                        ));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $last_id = $row['id'];
        }
        
        return $last_id;
    }
}
