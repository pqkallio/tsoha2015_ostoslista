<?php

/**
 * Description of purchas
 *
 * @author kallionpetri
 */
class Purchase extends BaseModel {
    public $id, $shopping_list, $product, $department, $unit, $amount, $purchase_date;
    
    public function __construct($attributes = null) {
        parent::__construct($attributes);
    }
    
    public static function all() {
        $purchases = array();
        $rows = DB::query('SELECT * FROM Purchase');
        
        foreach ($rows as $row) {
            $purchases[] = new Purchase(array(
                'id' => $row['id'],
                'shopping_list' => $row['list'],
                'product' => $row['product'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'amount' => $row['amount'],
                'purchase_date' => $row['purchase_date']
            ));
        }
        
        return $purchases;
    }
    
    public static function find($id) {
        $rows = DB::query('SELECT * FROM Purchase WHERE id = :id LIMIT 1', array('id' => $id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            $purchase = new Purchase(array(
                'id' => $row['id'],
                'shopping_list' => $row['list'],
                'product' => $row['product'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'amount' => $row['amount'],
                'purchase_date' => $row['purchase_date']
            ));
        }
        
        return $purchase;
    }
    
    public static function find_by_list($shopping_list) {
        $purchases = array();
        $rows = DB::query('SELECT * FROM Purchase WHERE list = :shopping_list', 
                array('shopping_list' => $shopping_list));
        
        
        foreach ($rows as $row) {
            $purchases[] = new Purchase(array(
                'id' => $row['id'],
                'shopping_list' => $row['list'],
                'product' => $row['product'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'amount' => $row['amount'],
                'purchase_date' => $row['purchase_date']
            ));
        }
        
        return $purchases;
    }
    
    public static function create($params) {
        $rows = DB::query('INSERT INTO Purchase (list, product, department, unit, amount) '
                . 'VALUES (:list, :product, :department, :unit, :amount) RETURNING id',
                array('list' => $params['shopping_list'], 
                    'product' => $params['product'],
                    'department' => $params['department'],
                    'unit' => $params['unit'],
                    'amount' => $params['amount']));
        
        $last_id = $rows[0];
        return $last_id['id'];
    }
    
    public static function delete($id) {
        DB::query('DELETE FROM Purchase WHERE id = :id', array('id' => $id));
    }
}
