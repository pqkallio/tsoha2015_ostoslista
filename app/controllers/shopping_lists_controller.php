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
            $lists = ShoppingList::find_by_owner($user->id);
            
            if (count($lists) > 0) {
                $active_list = self::get_active_list($lists);
                
                self::redirect_to('/list/' . $active_list->id);
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
            $lists = ShoppingList::find_by_owner($user->id);
            $active_list = self::get_active_list($lists);
            $purchases = Purchase::find_by_list($id);
            $products = array();
            $units = array();
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
                'all_units' => $all_units, 'all_departments' => $all_departments));
        } else {
            self::redirect_to('/login');
        }
    }
    
    public static function set_active($id) {
        self::check_logged_in();
        $user_id = parent::get_user_logged_in()->id;
        ShoppingList::set_as_active_list($id, $user_id);
        
        self::redirect_to('/lists');
    }
    
    public static function get_active_list($lists) {
        if (!empty($lists) && $lists != null) {
            $active_list = $lists[count($lists) - 1];

            foreach ($lists as $list) {
                if ($list->active) {
                    $active_list = $list;
                }
            }

            return $active_list;
        } else {
            return null;
        }
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
        
        ShoppingList::delete($id);
        
        self::redirect_to('/lists', array('message' => 'Lista poistettu!'));
    }
}
