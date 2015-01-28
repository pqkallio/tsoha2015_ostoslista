<?php

/**
 * Description of department
 *
 * @author kallionpetri
 */
class Department extends BaseModel {
    public $id, $name, $abbreviation;
    
    public function __construct($attributes = null) {
        parent::__construct($attributes);
    }
    
    public static function all() {
        $departments = array();
        
        $rows = DB::query('SELECT * FROM Department');
        
        foreach ($rows as $row) {
            $departments[] = new Department(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'abbreviation' => $row['abbreviation']
            ));
        }
        
        return $departments;
    }
    
    public static function find($id) {
        $rows = DB::query('SELECT * FROM Department WHERE id = :id LIMIT 1', array('id' => $id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $product = new Product(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'abbreviation' => $row['abbreviation']
            ));
        }
        
        return $product;
    }
}
