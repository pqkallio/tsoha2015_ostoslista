<?php

/**
 * This model is an abstraction of a single product used to form a {@link Purchase}.
 *
 * @author kallionpetri
 */
class Product extends BaseModel {
    public $id, $name, $department, $unit, $owner, $favorite;
    
    /**
     * A function that validates the Product's name.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_name() {
        $errors = array();
        
        if ($this->name == '' || $this->name == null) {
            $errors[] = 'Nimi ei saa olla tyhjä.';
        }
        
        if (strlen($this->name) < 2 || strlen($this->name) > 50) {
            $errors[] = 'Nimen pituuden tulee olla vähintään 2 ja enintaan 50 merkkiä.';
        }
        
        if (BaseController::get_user_logged_in()) {
            $users_other_products = $this->users_other_products(BaseController::get_user_logged_in()->id);

            foreach ($users_other_products as $p) {
                if (strcasecmp($p->name, $this->name) == 0) {
                    $errors[] = 'Olet jo lisännyt samannimisen tuotteen.';
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Product's department.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_department() {
        $errors = array();
        
        if ($this->department != null) {
            if (!is_numeric($this->department)) {
                $errors[] = 'Virheellinen osasto.';
            } else if (Department::find($this->department) == null) {
                $errors[] = 'Osastoa ei löydy tietokannasta.';
            }
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Product's unit.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_unit() {
        $errors = array();
        
        if ($this->unit != null) {
            if (!is_numeric($this->unit)) {
                $errors[] = 'Virheellinen yksikkö.';
            } else if (Unit::find($this->unit) == null) {
                $errors[] = 'Yksikköä ei löydy tietokannasta.';
            }
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Product's owner.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_owner() {
        $errors = array();
        
        if ($this->owner == null || User::find($this->owner) == null) {
            $errors[] = 'Virheellinen omistaja. Täällä ongelma!!!';
        }
        
        return $errors;
    }
    
    private function users_other_products($user_id) {
        $users_other_products = array();
        $rows = DB::query('SELECT * FROM Product WHERE id != :id AND owner = :owner', 
                                        array('id' => $this->id, 
                                            'owner' => $user_id));
        
        foreach ($rows as $row) {
            $users_other_products[] = self::create_product($row);
        }
        
        return $users_other_products;
    }
    
    /**
     * A constructor inherited from {@link BaseModel}. Validates the object based on validation rules.
     * 
     * @param array $attributes attributes used to construct an object (default null)
     */
    public function __construct($attributes = null) {
        parent::__construct($attributes);
        $this->favorite = false;
    
        $this->validators = array('validate_name', 'validate_department',
            'validate_unit', 'validate_owner');
    }
    
    /**
     * Retrieves all rows from the table <em>Product</em> and constructs Product objects from them finally returning them as an array.
     * 
     * @return array an array of Product objects
     */
    public static function all() {
        $products = array();
        $rows = DB::query('SELECT * FROM Product ORDER BY name ASC');
        
        foreach ($rows as $row) {
            $products[] = self::create_product($row);
        }
        
        return $products;
    }
    
    /**
     * Finds a row from the table <em>Product</em> with the id given as parameter, constructs a Product object of it and finally returns it.
     * 
     * @param integer $id
     * @return \Product the Product object corresponding to the id
     */
    public static function find($id) {
        $product = null;
        $rows = DB::query('SELECT * FROM Product WHERE id = :id LIMIT 1', array('id' => $id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $product = self::create_product($row);
        }
        
        return $product;
    }

    /**
     * Queries the database for a row corresponding to a user's id and the name of product, constructs an object and finally returns it.
     * 
     * @param integer $user_id
     * @param string $name
     * @return \Product
     */
    public static function find_by_name($user_id, $name) {
        $product = null;
        $rows = DB::query('SELECT * FROM Product '
                        . 'WHERE owner = :owner AND name = :name LIMIT 1',
                array('owner' => $user_id, 'name' => $name));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $product = self::create_product($row);
        }
        
        return $product;
    }
    
    /**
     * Queries the database for all rows from the table <em>Product</em> that have the user's id as owner, constructs objects out of them and returns them as an array.
     * 
     * @param integer $user_id the user's id whose products are to be found
     * @return array an array of Product objects
     */
    public static function find_by_owner($user_id) {
        $products = array();
        $rows = DB::query('SELECT * FROM Product WHERE owner = :owner ORDER BY name ASC',
                array('owner' => $user_id));
        
        foreach ($rows as $row) {
            $products[] = self::create_product($row);
        }
        
        return $products;
    }
    
    /**
     * Returns a user's favorite products.
     * 
     * @param array $params
     * @return array an array of the user's favorite products
     */
    public static function find_owners_favorites($params) {
        $users_products = self::find_by_owner($params['user']);
        $users_favorites = array();
        
        foreach ($users_products as $p) {
            if ($p->favorite) {
                array_push($users_favorites, $p);
            }
        }
        
        return $users_favorites;
    }
    
    /**
     * Inserts a new row to the table <em>Product</em> based upon the params. Returns the id of the new row.
     * 
     * @param array $params
     * @return integer the new products id
     */
    public static function create($params) {
        $rows = DB::query('INSERT INTO Product (name, department, unit, owner) '
                . 'VALUES (:name, :department, :unit, :owner) RETURNING id',
                array('name' => strtolower(trim($params['name'])), 
                    'department' => $params['department'],
                    'unit' => $params['unit'],
                    'owner' => $params['owner']));
        
        $last_id = $rows[0];
        return $last_id['id'];
    }
    
    /**
     * Updates the <em>Product</em> table's row corresponding to the id given as parameter with the values given in the $params array.
     * 
     * @param integer $id
     * @param array $params
     */
    public static function update($id, $params) {
        DB::query('UPDATE Product '
                . 'SET name = :name, department = :department, unit = :unit '
                . 'WHERE id = :id', 
                array('name' => $params['name'],
                      'department' => $params['department'],
                      'unit' => $params['unit'],
                      'id' => $id));
    }
    
    /**
     * Deletes the row of the table <em>Product</em> corresponding to the id given as parameter.
     * 
     * @param integer $id
     */
    public static function delete($id) {
        DB::query('DELETE FROM Product WHERE id = :id', array('id' => $id));
    }
    
    /**
     * Queries the table <em>Favorite_product</em> for a corresponding row based on $product_id and $user_id and returns true if it exists and false otherwise.
     * 
     * @param integer $product_id
     * @param integer $user_id
     * @return boolean
     */
    private static function is_favorite($product_id, $user_id) {
        $rows = DB::query('SELECT * FROM Favorite_product WHERE product = :product_id '
                . 'AND user_id = :user_id LIMIT 1', array('product_id' => $product_id, 
                    'user_id' => $user_id));
        
        if (count($rows) == 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Either inserts or deletes a row of the table <em>Favorite_product</em> based on whether a row with $product_id and $user_id already exists or not.
     * 
     * @param type $product_id
     * @param type $user_id
     */
    public static function fave($product_id, $user_id) {
        if (self::is_favorite($product_id, $user_id)) {
            DB::query('DELETE FROM Favorite_product '
                    . 'WHERE product = :product_id AND user_id = :user_id',
                    array('product_id' => $product_id, 'user_id' => $user_id));
        } else {
            DB::query('INSERT INTO Favorite_product (product, user_id) '
                    . 'VALUES (:product_id, :user_id)',
                    array('product_id' => $product_id, 'user_id' => $user_id));
        }
    }
    
    /**
     * Creates a <em>Product</em> object based on a row from the table <em>Product</em>.
     * 
     * @param type $row
     * @return \Product
     */
    private static function create_product($row) {
        $product = new Product(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'owner' => $row['owner']
            ));
        
        if ($product) {
            $product->favorite = self::is_favorite($product->id, $product->owner);
        }
        
        return $product;
    }
}
