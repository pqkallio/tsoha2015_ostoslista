<?php

/**
 * A controller class to handle requests relating to {@link ShoppingList} model.
 * 
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class ShoppingListController extends BaseController {
    
    /**
     * Prepare variables needed to render the user's <em>shopping lists'</em> view and render it.
     */
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

    /**
     * Takes a {@link ShoppingList}'s id as a parameter, asks for the corresponding object from the model class and renders its <em>show</em> view.
     * 
     * @param integer $id an id of a particular {@link ShoppingList} object
     */
    public static function show($id) {
        self::check_logged_in();
        $user = parent::get_user_logged_in();
        
        if ($user) {
            if (ShoppingList::find($id)->owner == $user->id 
                    || ShoppingList::has_right_to_list($user->id, $id)) {
                $lists = ShoppingList::find_by_user($user->id);
                $active_list = ShoppingList::find($id);
                $purchases = Purchase::find_by_list($id);
                $products = array();
                $units = array();
                $users = User::all();
                $departments = array();
                $all_units = Unit::all();
                $all_departments = Department::all();

                if ($active_list->owner != $user->id) {
                    $list_owner = User::find($active_list->owner);
                } else {
                    $list_owner = User::find($user->id);
                }

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
                    'users' => $users, 'list_owner' => $list_owner));
            } else {
                self::redirect_to('/lists');
            }
        } else {
            self::redirect_to('/login');
        }
    }
    
    /**
     * Takes a list's id as parameter and passes it to the {@link ShoppingList} model class to set it as the user's active list.
     * 
     * @param integer $list_id
     */
    public static function set_active($list_id) {
        self::check_logged_in();
        $user_id = parent::get_user_logged_in()->id;
        User::set_active_list($user_id, $list_id);
        
        self::redirect_to('/lists');
    }
    
    private static function get_active_list($user_id) {
        return User::get_active_list($user_id);
    }
    
    /**
     * Passes the params passed with a HTTP post request and passes them to the {@link ShoppingList} class to create a new ShoppingList.
     */
    public static function create() {
        self::check_logged_in();
        $user = parent::get_user_logged_in();
        
        $params = $_POST;
        $params['owner'] = $user->id;
        
        $shopping_list = new ShoppingList($params);
        
        if (count($shopping_list->errors()) == 0) {
            $list_id = ShoppingList::create($params);
            self::redirect_to('/list/' . $list_id, array('message' => 'Lista luotu!'));
        } else {
            self::redirect_to('/lists', array('errors' => $shopping_list->errors()));
        }
    }
    
    /**
     * Takes a {@link ShoppingList}'s id as a parameter, asks the model class to delete the corresponding row from the database and redirects to the user's <em>all lists</em> view.
     * 
     * @param integer $id an id of a particular {@link ShoppingList} object
     */
    public static function destroy($id) {
        self::check_logged_in();
        $user_id = self::get_user_logged_in()->id;
        
        if (ShoppingList::find($id)->owner == $user_id) {
            User::set_active_list_to_null($user_id);
            ShoppingList::delete($id);

            self::redirect_to('/lists', array('message' => 'Lista poistettu!'));
        } else {
            self::redirect_to('/lists', array('error' => 'Et voi poistaa toisen käyttäjän listaa!'));
        }
    }
    
    /**
     * Prepares the variables needed for rendering the <em>share shopping list</em> view and renders it.
     * 
     * @param type $list_id
     */
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
    
    /**
     * Passes the params passed with a HTTP post request and passes them to the {@link ShoppingList} class to share a ShoppingList with a particular {@link User}.
     * 
     * @param integer $list_id the ShoppingList's id that is to be shared
     */
    public static function share_list($list_id) {
        $params = $_POST;
        $user_id = $params['user'];
        ShoppingList::share_list($list_id, $user_id);
        
        self::redirect_to('/lists', array('message' => 'Lista jaettu!'));
    }
}
