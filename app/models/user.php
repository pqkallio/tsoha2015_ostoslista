<?php

/**
 * Description of user
 *
 * @author kallionpetri
 */
class User extends BaseModel {
    public $id, $first_name, $last_name, $user_name, $email, $joined, 
            $last_signin, $available, $password, $role, $account_status;
    
    public function __construct($attributes = null) {
        parent::__construct($attributes);
    }
    
    public static function all() {
        $users = array();
        $rows = DB::query('SELECT * FROM Registered_user');
        
        foreach ($rows as $row) {
            $users[] = new User(array(
                'id' => $row['id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'user_name' => $row['user_name'],
                'password' => $row['key']
            ));
        }
        
        return $users;
    }
    
    public static function find($id) {
        $rows = DB::query('SELECT * FROM Registered_user WHERE id = :id LIMIT 1',
                array('id' => $id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $user = new User(array(
                'id' => $row['id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'user_name' => $row['user_name'],
                'password' => $row['key']
            ));                   
        }
        
        return $user;
    }
    
    public static function find_by_username($username) {
        $user = null;
        $rows = DB::query('SELECT * FROM Registered_user '
                        . 'WHERE user_name = :username LIMIT 1',
                array('username' => $username));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $user = new User(array(
                'id' => $row['id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'user_name' => $row['user_name'],
                'password' => $row['key']
            ));                   
        }
        
        return $user;
    }
    
    public static function authenticate($username, $password) {
        $user = self::find_by_username($username);
        
        if ($user != null) {
            if (strcmp($user->password, $password) == 0) {
                return $user;
            }
        }
        
        return null;
    }
}
