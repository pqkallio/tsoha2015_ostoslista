<?php

/**
 * Description of shopping_lists_controller
 *
 * @author kallionpetri
 */
class ShoppingListController extends BaseController {
    
    public static function index() {
        self::check_logged_in();
        $user = parent::get_user_logged_in();
        
        if ($user) {
            $lists = ShoppingList::find_by_user($user->id);
            
            if (count($lists) > 0) {
                $active_list = self::get_active_list($user->id);
                
                if (is_null($active_list)) {
                    $active_list = $lists[0]->id;
                }
                
                self::redirect_to('/list/' . $active_list);
            } else {
                self::render_view('/list/start.html', array('message' => 'Aloita tekemällä uusi kauppalista!'));
            }
        } else {
            self::redirect_to('/login', array('message' => 'Kirjaudu sisään käyttääksesi sovellusta!'));
        }
    }
    
    public static function show($id) {
        self::check_logged_in();
        $user = parent::get_user_logged_in();
        
        if ($user) {
            $lists = ShoppingList::find_by_user($user->id);
            $active_list = ShoppingList::find($id);
            $purchases = Purchase::find_by_list($id);
            $products = array();
            $units = array();
            $users = User::all();
            $departments = array();
            $all_units = Unit::all();
            $all_departments = Department::all();
            
            foreach ($purchases as $purchase) {
                $products[] = Product::find($purchase->product);
                
                if ($purchase->unit) {
                    $units[] = Unit::find($purchase->unit);
                } else {
                    $units[] = Unit::find(1);
                }
                
                if ($purchase->department) {
                    $departments[] = Department::find($purchase->department);
                } else {
                    $departments[] = null;
                }
            }
        
            self::render_view('list/show.html', array(
                'lists' => $lists, 'active_list' => $active_list,
                'purchases' => $purchases, 'products' => $products, 
                'units' => $units, 'departments' => $departments,
                'all_units' => $all_units, 'all_departments' => $all_departments,
                'users' => $users));
        } else {
            self::redirect_to('/login');
        }
    }
    
    public static function set_active($list_id) {
        self::check_logged_in();
        $user_id = parent::get_user_logged_in()->id;
        User::set_active_list($user_id, $list_id);
        
        self::redirect_to('/lists');
    }
    
    private static function get_active_list($user_id) {
        return User::get_active_list($user_id);
    }
    
    public static function create() {
        self::check_logged_in();
        $user = parent::get_user_logged_in();
        
        $params = $_POST;
        $params['user'] = $user->id;
        
        $list_id = ShoppingList::create($params);
        
        self::redirect_to('/list/' . $list_id, array('message' => 'Lista luotu!'));
    }
    
    public static function destroy($id) {
        self::check_logged_in();
        $user_id = self::get_user_logged_in()->id;
        
        User::set_active_list_to_null($user_id);
        ShoppingList::delete($id);
        
        self::redirect_to('/lists', array('message' => 'Lista poistettu!'));
    }
    
    public static function share($list_id) {
        $list = ShoppingList::find($list_id);
        $all_users = User::all();
        $users = array();
        
        foreach ($all_users as $user) {
            if ($user != self::get_user_logged_in() 
                    && !ShoppingList::has_right_to_list($user->id, $list_id)) {
                $users[] = $user;
            }
        }
        
        self::render_view('/list/share.html', array('shopping_list' => $list, 'users' => $users));
    }

    public static function share_list($list_id) {
        $params = $_POST;
        $user_id = $params['user'];
        ShoppingList::share_list($list_id, $user_id);
        
        self::redirect_to('/lists', array('message' => 'Lista jaettu!'));
    }
}
