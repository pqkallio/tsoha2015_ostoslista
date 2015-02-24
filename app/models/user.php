<?php

/**
 * An abstraction of the application's user
 *
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class User extends BaseModel {
    public $id, $first_name, $last_name, $user_name, $email, $joined, 
            $last_signin, $available, $password, $role, $account_status, $active_list;
    
    /**
     * A function that validates the User's first name.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_first_name() {
        $errors = array();
        
        if ($this->first_name == '' || $this->first_name == null) {
            $errors[] = 'Etunimi ei saa olla tyhjä.';
        }
        
        if (strlen($this->first_name) < 2 || strlen($this->first_name) > 20) {
            $errors[] = 'Etunimen pituuden tulee olla vähintään 2 ja enintään 20 merkkiä.';
        }
        
        if (preg_match('/^[\p{L}-]*$/u', $this->first_name) != 1) {
            $errors[] = 'Etunimi ei voi sisältää numeroita tai erikoismerkkejä';
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the User's last name.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_last_name() {
        $errors = array();
        
        if ($this->last_name == '' || $this->last_name == null) {
            $errors[] = 'Sukunimi ei saa olla tyhjä.';
        }
        
        if (strlen($this->last_name) < 2 || strlen($this->last_name) > 20) {
            $errors[] = 'Sukunimen pituuden tulee olla vähintään 2 ja enintään 20 merkkiä.';
        }
        
        if (preg_match('/^[\p{L}-]*$/u', $this->last_name) != 1) {
            $errors[] = 'Sukunimi ei voi sisältää numeroita tai erikoismerkkejä';
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the User's username.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_user_name() {
        $errors = array();
        
        if ($this->user_name == '' || $this->user_name == null) {
            $errors[] = 'Käyttäjätunnus ei saa olla tyhjä.';
        }
        
        if (strlen($this->user_name) < 5 || strlen($this->user_name) > 10) {
            $errors[] = 'Käyttäjätunnuksen pituuden tulee olla vähintään 5 ja enintään 10 merkkiä.';
        }
        
        foreach (self::all() as $u) {
            if ($this->user_name == $u->user_name) {
                $errors[] = 'Käyttäjätunnus on varattu';
            }
        }
        
        return $errors;
    }
        
    /**
     * A function that validates the User's email.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_email() {
        $errors = array();
        
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Tarkista sähköpostiosoite';
        }
        
        foreach (self::all() as $u) {
            if ($this->email === $u->email) {
                $errors[] = 'Sähköpostiosoite on jo käytössä';
            }
        }
        
        return $errors;
    }

    /**
     * A function that validates the User's password.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_key() {
        $errors = array();
        
        if (preg_match('/[A-Z]/', $this->password) != 1
                || preg_match('/[a-z]/', $this->password) != 1
                || preg_match('/[0-9]/', $this->password) != 1) {
            $errors[] = 'Salasanan tulee sisältää vähintään yksi numero sekä yksi pieni ja yksi iso kirjain';
        }
        
        if (strlen($this->password) < 8) {
            $errors[] = 'Salasanan pituuden tulee olla vähintään 8 merkkiä';
        } else if (strlen($this->password) > 50) {
            $errors[] = 'Salasanan pituuden tulee olla alle 50 merkkiä';
        }
        
        return $errors;
    }

    /**
     * A constructor inherited from {@link BaseModel}. Validates the object based on validation rules.
     * 
     * @param array $attributes attributes used to construct an object (default null)
     */
    public function __construct($attributes = null) {
        parent::__construct($attributes);
        
        $this->validators = array('validate_first_name', 'validate_last_name', 
            'validate_user_name', 'validate_key', 'validate_email');
    }
    
    /**
     * Retrieves all rows from the table <em>Registered_user</em> and constructs User objects from them finally returning them as an array.
     * 
     * @return array an array of User objects
     */
    public static function all() {
        $users = array();
        $rows = DB::query('SELECT * FROM Registered_user');
        
        foreach ($rows as $row) {
            $users[] = self::create_user($row);
        }
        
        return $users;
    }
    
    /**
     * Finds a row from the table <em>Registered_user</em> with the id given as parameter, constructs a User object of it and finally returns it.
     * 
     * @param integer $id
     * @return \User the User object corresponding to the id
     */
    public static function find($id) {
        $rows = DB::query('SELECT * FROM Registered_user WHERE id = :id LIMIT 1',
                array('id' => $id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $user = self::create_user($row);                   
        }
        
        return $user;
    }
    
    /**
     * Queries the database for a row corresponding to a user's username, constructs an object and finally returns it.
     * 
     * @param string $username
     * @return \User
     */
    public static function find_by_username($username) {
        $user = null;
        $rows = DB::query('SELECT * FROM Registered_user '
                        . 'WHERE user_name = :username LIMIT 1',
                array('username' => $username));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $user = self::create_user($row);                   
        }
        
        return $user;
    }
    
    /**
     * Inserts a new row to the table <em>Registered_user</em> based upon the params. Returns the id of the new row.
     * 
     * @param array $params
     * @return integer the new user's id
     */
    public static function create($params) {
        $rows = DB::query('INSERT INTO Registered_user (first_name, last_name, user_name, email, joined, key) '
                . 'VALUES (:first_name, :last_name, :user_name, :email, NOW(), :key) RETURNING id',
                array('first_name' => $params['first_name'], 
                    'last_name' => $params['last_name'],
                    'user_name' => $params['user_name'],
                    'email' => $params['email'],
                    'key' => $params['password']));
        
        $last_id = $rows[0];
        return $last_id['id'];
    }

    /**
     * Authenticates the user comparing the given username and password to the ones stored in the database. Returns the user if the authentication was successful and null otherwise.
     * 
     * @param string $username
     * @param string $password
     * @return User
     */
    public static function authenticate($username, $password) {
        $user = self::find_by_username($username);
        
        if ($user != null) {
            if (strcmp($user->password, $password) == 0) {
                return $user;
            }
        }
        
        return null;
    }
    
    /**
     * Sets the list_id given as parameter as the user's active list.
     * 
     * @param integer $user_id the User whose active_list is to be updated
     * @param integer $list_id the ShoppingList's id to be set as active list
     */
    public static function set_active_list($user_id, $list_id) {
        DB::query("UPDATE Registered_user "
                . "SET active_list = :list_id "
                . "WHERE id = :id",
                array('id' => $user_id, 'list_id' => $list_id));
    }
    
    /**
     * Returns the id of the user's active list.
     * 
     * @param integer $user_id
     * @return integer the id of the user's active list
     */
    public static function get_active_list($user_id) {
        $user = self::find($user_id);
        
        return $user->active_list;
    }
    
    /**
     * Sets the user's active list to null
     * 
     * @param integer $user_id
     */
    public static function set_active_list_to_null($user_id) {
        DB::query("UPDATE Registered_user "
                . "SET active_list = NULL "
                . "WHERE id = :id",
                array('id' => $user_id));
    }
    
    /**
     * Creates a <em>User</em> object based on a row from the table <em>Registered_user</em>.
     * 
     * @param type $row
     * @return \User
     */
    private static function create_user($row) {
        return new User(array(
                'id' => $row['id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'user_name' => $row['user_name'],
                'email' => $row['email'],
                'password' => $row['key'],
                'active_list' => $row['active_list']
            ));
    }
}
