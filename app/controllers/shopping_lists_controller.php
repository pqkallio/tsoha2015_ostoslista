<?php

/**
 * Description of shopping_lists_controller
 *
 * @author kallionpetri
 */
class ShoppingListController extends BaseController {
    
    public static function index() {
        $user = parent::get_user_logged_in();
        
        if ($user) {
            $lists = ShoppingList::find_by_owner($user->id);
            
            if (count($lists) > 0) {
                $active_list = null;
                
                foreach ($lists as $list) {
                    if ($list->active) {
                        $active_list = $list;
                    }
                }
                
                self::redirect_to('/list/' . $active_list->id, array(
                        'lists' => $lists,
                        'active_list' => $active_list
                ));
            } else {
                self::redirect_to('/list/new', $message = 'Aloita tekemällä uusi kauppalista!');
            }
        } else {
            self::redirect_to('/login', $message = 'Kirjaudu sisään käyttääksesi sovellusta!');
        }
    }
    
    public static function show($params) {
        self::render_view('/list/show.html', array(
            'lists' => $params['lists'],
            'active_list' => $params['active_list']));
    }
    
    public static function set_active($id) {
        $user_id = parent::get_user_logged_in()->id;
        ShoppingList::set_as_active_list($id, $user_id);
        
        self::redirect_to('/lists');
    }
}
