<?php

/**
 * Description of Product
 *
 * @author kallionpetri
 */
class Product extends BaseModel {
    public $id, $name, $department, $unit, $owner, $favorite;
    
    public function __construct($attributes = null) {
        parent::__construct($attributes);
    }
    
    public static function all() {
        $products = array();
        $rows = DB::query('SELECT * FROM Product ORDER BY name ASC');
        
        foreach ($rows as $row) {
            $products[] = new Product(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'owner' => $row['owner']
            ));
        }
        
        return $products;
    }
    
    public static function find($id) {
        $rows = DB::query('SELECT * FROM Product WHERE id = :id LIMIT 1', array('id' => $id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $product = new Product(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'owner' => $row['owner']
            ));
        }
        
        return $product;
    }
    
    public static function create($params) {
        $rows = DB::query('INSERT INTO Product (name, department, unit, owner) '
                . 'VALUES (:name, :department, :unit, :owner) RETURNING id',
                array('name' => $params['name'], 
                    'department' => $params['department'],
                    'unit' => $params['unit'],
                    'owner' => $params['owner']));
        
        $last_id = $rows[0];
        return $last_id['id'];
    }
    
    public static function delete($id) {
        DB::query('DELETE FROM Product WHERE id = :id', array('id' => $id));
    }
}
