<?php

/**
 * An abstraction of a single <em>Purchase</em> object.
 *
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class Purchase extends BaseModel {
    public $id, $shopping_list, $product, $department, $unit, $amount, $purchase_date;   
    
    /**
     * A function that validates the Purchase's shopping list.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_shopping_list() {
        $errors = array();
        
        if ($this->shopping_list != null && is_numeric($this->shopping_list)) {
            if (ShoppingList::find($this->shopping_list) == null) {
                $errors[] = 'Osastoa ei löydy tietokannasta.';
            }
            
        } else {
            $errors[] = 'Virheellinen kauppalista.';
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Purchase's product.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_product() {
        $errors = array();
        
        if ($this->product != null && is_numeric($this->product)) {
            $product = Product::find($this->product);
            $shopping_list = null;
            
            if (count(self::validate_shopping_list() == 0)) {
                $shopping_list = ShoppingList::find($this->shopping_list);
            }
            
            if ($product == null || $shopping_list == null 
                    || ($product->owner != $shopping_list->owner
                    && !ShoppingList::has_right_to_list($product->owner, $this->shopping_list))) {
                $errors[] = 'Tuotetta ei löydy tietokannasta.';
            }
            
        } else {
            $errors[] = 'Virheellinen tuote.';
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Purchase's department.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_department() {
        $errors = array();
        
        if ($this->department != null) {
            if (!is_numeric($this->department) 
                    || Department::find($this->department) == null) {
                $errors[] = 'Virheellinen osasto.';
            }
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Purchase's unit.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_unit() {
        $errors = array();
        
        if ($this->unit != null) {
            if (!is_numeric($this->unit) 
                    || Unit::find($this->unit) == null) {
                $errors[] = 'Virheellinen osasto.';
            }
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Purchase's amount.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_amount() {
        $errors = array();
        
        if (!is_numeric($this->amount) || $this->amount < 1 
                || $this->amount > 1000000) {
            $errors[] = 'Määrän tulee olla välillä 1-1000000.';
        }
        
        return $errors;
    }
    
    /**
     * A construction method inherited from {@link BaseModel}. Validates the object based on validation rules.
     * 
     * @param array $attributes
     */
    public function __construct($attributes = null) {
        parent::__construct($attributes);
        
        $this->validators = array('validate_shopping_list', 'validate_product',
            'validate_department', 'validate_unit', 'validate_amount');
    }
    
    /**
     * Retrieves all rows from the table <em>Purchase</em> and constructs Purchase objects from them finally returning them as an array.
     * 
     * @return array an array of Purchase objects
     */
    public static function all() {
        $purchases = array();
        $rows = DB::query('SELECT * FROM Purchase');
        
        foreach ($rows as $row) {
            $purchases[] = self::create_purchase($row);
        }
        
        return $purchases;
    }
    
    /**
     * Finds one row from the Table <em>Purchase</em> based on the id given as parameter, constructs a new Purchase object based on it, finally returning the created object. 
     * 
     * @param integer $id
     * @return Purchase
     */
    public static function find($id) {
        $rows = DB::query('SELECT * FROM Purchase WHERE id = :id LIMIT 1', array('id' => $id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            $purchase = self::create_purchase($row);
        }
        
        return $purchase;
    }
    
    /**
     * Queries the database for all rows from the table <em>Purchase</em> that have the {@link ShoppingList}'s id as $shopping_list, constructs objects out of them and returns them as an array.
     * 
     * @param integer $shopping_list the shopping list's id which purchases are to be found
     * @return array an array of Purchase objects
     */
    public static function find_by_list($shopping_list) {
        $purchases = array();
        $rows = DB::query('SELECT * FROM Purchase '
                        . 'WHERE list = :shopping_list AND purchase_date IS NULL', 
                            array('shopping_list' => $shopping_list));
        
        
        foreach ($rows as $row) {
            $purchases[] = self::create_purchase($row);
        }
        
        return $purchases;
    }
    
    /**
     * Creates a new row to the table <em>Purchase</em> based on the parameters and returns the id of the new row
     * 
     * @param array $params
     * @return integer the id of the newly created row
     */
    public static function create($params) {
        if ($params['unit'] == "null") {
            $params['unit'] = null;
        }
        
        if ($params['department'] == "null") {
            $params['department'] = null;
        }
        
        $rows = DB::query('INSERT INTO Purchase (list, product, department, unit, amount) '
                . 'VALUES (:list, :product, :department, :unit, :amount) RETURNING id',
                array('list' => $params['list'], 
                    'product' => $params['product'],
                    'department' => $params['department'],
                    'unit' => $params['unit'],
                    'amount' => $params['amount']));
        
        $last_id = $rows[0];
        return $last_id['id'];
    }
    
    /**
     * Sets current time as purchase_date to all the rows which ids are given as parameters
     * 
     * @param array $purchase_ids
     */
    public static function set_purchase_date($purchase_ids) {
        foreach (array_keys($purchase_ids) as $id) {
            DB::query('UPDATE Purchase '
                    . 'SET purchase_date = NOW() '
                    . 'WHERE id = :id',
                    array('id' => $id));
        }
    }

    /**
     * Deletes the row that corresponds to the id given as parameter
     * 
     * @param integer $id
     */
    public static function delete($id) {
        DB::query('DELETE FROM Purchase WHERE id = :id', array('id' => $id));
    }
    
    /**
     * Creates a Purchase object from a array made of a Purchase table row
     * 
     * @param array $row
     * @return \Purchase
     */
    private static function create_purchase($row) {
        return new Purchase(array(
                'id' => $row['id'],
                'shopping_list' => $row['list'],
                'product' => $row['product'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'amount' => $row['amount'],
                'purchase_date' => $row['purchase_date']
            ));
    }
    
    /**
     * Updates a row in the <em>Purchase</em> table based on the params given
     * 
     * @param array $params
     */
    public static function update($params) {
        DB::query('UPDATE Purchase '
                . 'SET product = :product, '
                . '    amount = :amount, '
                . '    unit = :unit, '
                . '    department = :department '
                . 'WHERE id = :id',
                array('product' => $params['product'],
                      'amount' => $params['amount'],
                      'unit' => $params['unit'],
                      'department' => $params['department'],
                      'id' => $params['id']));
    }
}
