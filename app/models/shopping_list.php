<?php

/**
 * Description of list
 *
 * @author kallionpetri
 */
class ShoppingList extends BaseModel {
    public $id, $name, $owner, $shop, $active;
    
    public function __construct($attributes = null) {
        parent::__construct($attributes);
    }
    
    public static function all() {
        $shopping_lists = array();
        $rows = DB::query('SELECT * FROM List ORDER BY name ASC');
        
        foreach ($rows as $row) {
            $shopping_lists[] = new ShoppingList(array(
               'id' => $row['id'],
                'name' => $row['name'],
                'owner' => $row['owner'],
                'shop' => $row['shop'],
                'active' => $row['active']
            ));
        }
        
        return $shopping_lists;
    }
    
    public static function find($id) {
        $rows = DB::query('SELECT * FROM List WHERE id = :id LIMIT 1', array('id' => $id));
        $shopping_list = null;
        
        if (count($rows) > 0) {
            $row = $rows(0);
            
            $shopping_list = new ShoppingList(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'owner' => $row['owner'],
                'shop' => $row['shop'],
                'active' => $row['active']
            ));
        }
        
        return $shopping_list;
    }
    
    public static function find_by_owner($user) {
        $shopping_lists = array();
        $rows = DB::query('SELECT * FROM List WHERE owner = :user ORDER BY name ASC', array('user' => $user));
        
        foreach ($rows as $row) {
            $shopping_lists[] = new ShoppingList(array(
               'id' => $row['id'],
                'name' => $row['name'],
                'owner' => $row['owner'],
                'shop' => $row['shop'],
                'active' => $row['active']
            ));
        }
        
        return $shopping_lists;
    }
    
    public static function get_ids_and_names($user_id) {
        $list_ids_and_names = array();
        $rows = DB::query('SELECT id, name, active FROM List '
                        . 'WHERE owner = :user_id '
                        . 'ORDER BY name',
                        array('user_id' => $user_id));
        
        foreach ($rows as $row) {
            $list_ids_and_names[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'active' => $row['active']);
        }
        
        return $list_ids_and_names;
    }

    public static function create($params) {
        self::remove_users_active_list($params['user'].id);
        
        $rows = DB::query('INSERT INTO List (name, owner, shop) '
                        . 'VALUES (:name, :owner, null) RETURNING id', 
                        array('name' => $params['name'], 
                            'owner' => $params['user']));

        $last_id = $rows[0];
        return $last_id;
    }
    
    public static function delete($id) {
        DB::query('DELETE FROM List WHERE id = :id', array('id' => $id));
    }
    
    public static function remove_users_active_list($user_id) {
        DB::query('UPDATE List SET active = false '
                . 'WHERE owner = :owner AND active = true',
                array('owner' => $user_id));
    }
    
    public static function set_as_active_list($id, $user_id) {
        self::remove_users_active_list($user_id);
        
        DB::query('UPDATE List SET active = TRUE '
                . 'WHERE id = :id AND owner = :owner',
                array('owner' => $user_id, 'id' => $id));
    }
    
    public static function get_active_list($user_id) {
        $list = null;
        $rows = DB::query('SELECT * FROM List '
                        . 'WHERE owner = :owner AND active = TRUE LIMIT 1',
                        array('owner' => $user_id));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            $id = $row['id'];
            $list = self::find($id);
        }
        
        return $list;
    }
}
