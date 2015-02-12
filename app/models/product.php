<?php

/**
 * Description of Product
 *
 * @author kallionpetri
 */
class Product extends BaseModel {
    public $id, $name, $department, $unit, $owner, $favorite;
    
    public function validate_name() {
        $errors = array();
        
        if ($this->name == '' || $this->name == null) {
            $errors[] = 'Nimi ei saa olla tyhjä.';
        }
        
        if (strlen($this->name) < 2 || strlen($this->name) > 50) {
            $errors[] = 'Nimen pituuden tulee olla vähintään 2 ja enintaan 50 merkkiä.';
        }
        if (BaseController::get_user_logged_in()) {
            $previous_products = self::find_by_owner(BaseController::get_user_logged_in()->id);

            foreach ($previous_products as $p) {
                if (strcasecmp($p->name, $this->name) == 0) {
                    $errors[] = 'Olet jo lisännyt samannimisen tuotteen.';
                }
            }
        }
        
        return $errors;
    }
    
    public function validate_department() {
        $errors = array();
        
        if (!is_numeric($this->department)) {
            $errors[] = 'Virheellinen osasto.';
        }
        
        if ($this->department != null) {
            if (Department::find($this->department) == null) {
                $errors[] = 'Osastoa ei löydy tietokannasta.';
            }
        }
        
        return $errors;
    }
    
    public function validate_unit() {
        $errors = array();
        
        if (!is_numeric($this->unit)) {
            $errors[] = 'Virheellinen yksikkö.';
        }
        
        if ($this->unit != null) {
            if (Unit::find($this->unit) == null) {
                $errors[] = 'Yksikköä ei löydy tietokannasta.';
            }
        }
        
        return $errors;
    }
    
    public function validate_owner() {
        $errors = array();
        
        if ($this->owner != null) {
            if (User::find($this->owner) == null) {
                $errors[] = 'Virheellinen omistaja.';
            }
        }
        
        return $errors;
    }
    
    public function __construct($attributes = null) {
        parent::__construct($attributes);
        $this->favorite = false;
    
        $this->validators = array('validate_name', 'validate_department',
            'validate_unit', 'validate_owner');
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
        $product = null;
        $rows = DB::query('SELECT * FROM Product WHERE id = :id LIMIT 1', array('id' => $id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $product = new Product(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'owner' => $row['owner'],
                'favorite' => false
            ));
        }
        
        return $product;
    }

    public static function find_by_name($user, $name) {
        $product = null;
        $rows = DB::query('SELECT * FROM Product '
                        . 'WHERE owner = :owner AND name = :name LIMIT 1',
                array('owner' => $user, 'name' => $name));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $product = new Product(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'owner' => $row['owner']
            ));
            
            $product->favorite = self::is_favorite($product->id, $product->owner);
        }
        
        return $product;
    }
    
    public static function find_by_owner($user) {
        $products = array();
        $rows = DB::query('SELECT * FROM Product WHERE owner = :owner ORDER BY name ASC',
                array('owner' => $user));
        
        foreach ($rows as $row) {
            $products[] = new Product(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'department' => $row['department'],
                'unit' => $row['unit'],
                'owner' => $row['owner']
            ));
        }
        
        foreach ($products as $product) {
            $product->favorite = self::is_favorite($product->id, $product->owner);
        }
        
        return $products;
    }
    
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
    
    public static function update($id, $attributes) {
        DB::query('UPDATE Product '
                . 'SET name = :name, department = :department, unit = :unit '
                . 'WHERE id = :id', 
                array('name' => $attributes['name'],
                      'department' => $attributes['department'],
                      'unit' => $attributes['unit'],
                      'id' => $id));
    }

    public static function delete($id) {
        DB::query('DELETE FROM Product WHERE id = :id', array('id' => $id));
    }
    
    public static function is_favorite($product_id, $user_id) {
        $rows = DB::query('SELECT * FROM Favorite_product WHERE product = :product_id '
                . 'AND user_id = :user_id LIMIT 1', array('product_id' => $product_id, 
                    'user_id' => $user_id));
        
        if (count($rows) == 0) {
            return false;
        }
        
        return true;
    }
    
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
}
