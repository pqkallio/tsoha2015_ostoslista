<?php

/**
 * Description of users_controller
 *
 * @author kallionpetri
 */
class UserController extends BaseController {
    
    public static function index() {
        $users = User::all();
        
        self::render_view('user/index.html', array('users' => $users));
    }
    
    public static function show($id) {
        $user = User::find($id);
        
        if (!$user) {
            self::redirect_to('/users', array('message' => 'Käyttäjää ei löytynyt!'));
        } else {
            self::render_view('user/show.html', array('user' => $user));
        }
    }

    public static function login() {
        self::render_view('user/login.html');
    }
    
    public static function handle_login() {
        $params = $_POST;
        
        $user = User::authenticate($params['username'], $params['password']);
        
        if (!$user) {
            self::redirect_to('/login', 
                    array('error' => 'Väärä käyttäjätunnus tai salasana!'));
        } else {
            $_SESSION['user'] = $user->id;
            
            self::redirect_to('/lists', array('message' => 'Tervetuloa takaisin ' 
                . $user->first_name . '!'));
        }
    }
    
    public static function logout() {
        $_SESSION['user'] = null;
        
        self::redirect_to('/login');
    }
}
