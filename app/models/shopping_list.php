<?php

/**
 * An abstraction of a shopping list.
 *
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class ShoppingList extends BaseModel {
    public $id, $name, $owner;
    
    /**
     * A function that validates the ShoppingList's name.
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
            $previous_lists = self::find_by_owner(BaseController::get_user_logged_in()->id);

            foreach ($previous_lists as $l) {
                if (strcasecmp($l->name, $this->name) == 0) {
                    $errors[] = 'Olet jo lisännyt samannimisen listan.';
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Shopping List's owning {@link User}.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_owner() {
        $errors = array();
        
        if (!is_numeric($this->owner)) {
            $errors[] = 'Virheellinen käyttäjä.';
        }
        
        if ($this->owner != null) {
            if (User::find($this->owner) == null) {
                $errors[] = 'Käyttäjää ei löydy tietokannasta.';
            }
        }
        
        return $errors;
    }
    
    /**
     * A constructor inherited from {@link BaseModel}. Validates the object based on validation rules.
     * 
     * @param array $attributes attributes used to construct an object (default null)
     */
    public function __construct($attributes = null) {
        $attributes['name'] = StringUtil::trim_name($attributes['name']);
        
        parent::__construct($attributes);
        
        $this->validators = array('validate_name', 'validate_owner');
    }
    
    /**
     * Retrieves all rows from the table <em>List</em> and constructs ShoppingList objects from them finally returning them as an array.
     * 
     * @return array an array of ShoppingList objects
     */
    public static function all() {
        $shopping_lists = array();
        $rows = DB::query('SELECT * FROM List ORDER BY name ASC');
        
        foreach ($rows as $row) {
            $shopping_lists[] = self::create_list($row);
        }
        
        return $shopping_lists;
    }
    
    /**
     * Finds a row from the table <em>List</em> with the id given as parameter, constructs a ShoppingList object of it and finally returns it.
     * 
     * @param integer $id
     * @return \ShoppingList the ShoppingList object corresponding to the id
     */
    public static function find($id) {
        if ($id != null) {
            $rows = DB::query('SELECT * FROM List');
        
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    if ($row['id'] == $id) {
                        $shopping_list = self::create_list($row);
                    }
                }
            }

            return $shopping_list;
        } else {
            return null;
        }
        
    }
    
    /**
     * Queries the database for all rows from the table <em>List</em> that have the user's id as owner, constructs objects out of them and returns them as an array.
     * 
     * @param integer $user_id the user's id whose lists are to be found
     * @return array an array of ShoppingList objects
     */
    public static function find_by_owner($user_id) {
        $shopping_lists = array();
        $rows = DB::query('SELECT * FROM List WHERE owner = :user ORDER BY name ASC', array('user' => $user_id));
        
        foreach ($rows as $row) {
            $shopping_lists[] = self::create_list($row);
        }
        
        return $shopping_lists;
    }
    
    /**
     * Queries the database for all rows from the table <em>List</em> that the user owns or has right to, constructs objects out of them and returns them as an array.
     * 
     * @param integer $user_id 
     * @return array an array of ShoppingList objects
     */
    public static function find_by_user($user_id) {
        $lists = array_merge(self::find_by_owner($user_id), self::find_by_right_to_list($user_id));
        
        return $lists;
    }
    
    /**
     * Queries the database for all rows from the table <em>List</em> that the user has a right to, constructs objects out of them and returns them as an array.
     * 
     * @param integer $user_id 
     * @return array an array of ShoppingList objects
     */
    public static function find_by_right_to_list($user_id) {
        $rows = DB::query('SELECT * FROM Right_to_list WHERE user_id = :user_id', array('user_id' => $user_id));
        $lists = array();
        
        foreach ($rows as $row) {
            $list_id = $row['list'];
            $lists[] = self::find($list_id);
        }
        
        return $lists;
    }
    
    /**
     * Inserts a new row to the table <em>List</em> based upon the params. Returns the id of the new row.
     * 
     * @param array $params
     * @return integer the new ShoppingList's id
     */
    public static function create($params) {
        $rows = DB::query('INSERT INTO List (name, owner) '
                        . 'VALUES (:name, :owner) RETURNING id', 
                        array('name' => $params['name'], 
                            'owner' => $params['owner']));

        $row = $rows[0];
        $last_id = $row[0];
        return $last_id;
    }
    
    /**
     * Deletes the row of the table <em>List</em> corresponding to the id given as parameter.
     * 
     * @param integer $id
     */
    public static function delete($id) {
        DB::query('DELETE FROM List WHERE id = :id', array('id' => $id));
    }
    
    /**
     * Inserts a row to the <em>Right_to_list</em> table with the list's and the user's ids.
     * 
     * @param integer $list_id
     * @param integer $user_id
     */
    public static function share_list($list_id, $user_id) {
        
        DB::query('INSERT INTO Right_to_list (list, user_id) '
                . 'VALUES (:list, :user_id) RETURNING id',
                  array('list' => $list_id, 'user_id' => $user_id));
        
    }
    
    /**
     * Checks if the user given as $user_id has right to the list given as $list_id
     * 
     * @param integer $user_id
     * @param integer $list_id
     * @return boolean true if user has the right, false otherwise
     */
    public static function has_right_to_list($user_id, $list_id) {
        $rows = DB::query('SELECT * FROM Right_to_list WHERE list = :list AND user_id = :user_id',
                          array('list' => $list_id, 'user_id' => $user_id));
        
        if (count($rows) == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Creates a <em>ShoppingList</em> object based on a row from the table <em>List</em>.
     * 
     * @param type $row
     * @return \ShoppingList
     */
    private static function create_list($row) {
        return new ShoppingList(array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'owner' => $row['owner']
                ));
    }
}
