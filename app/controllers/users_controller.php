<?php

/**
 * A controller class to handle requests relating to {@link User} model.
 * 
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class UserController extends BaseController {
    
    /**
     * Prepare variables needed to render the <em>all users</em> view and render it.
     */
    public static function index() {
        $users = User::all();
        
        self::render_view('user/index.html', array('users' => $users));
    }
    
    /**
     * Takes a {@link User}'s id as a parameter, asks for the corresponding object from the model class and renders its <em>show</em> view.
     * 
     * @param integer $id an id of a particular {@link User} object
     */
    public static function show($id) {
        $user = User::find($id);
        
        if (!$user) {
            self::redirect_to('/users', array('message' => 'Käyttäjää ei löytynyt!'));
        } else {
            self::render_view('user/show.html', array('user' => $user));
        }
    }

    /**
     * Renders the <em>login</em> view
     */
    public static function login() {
        self::render_view('user/login.html');
    }
    
    /**
     * Handles a login post request and either starts a new session and redirects the user to his/her <em>active list</em> view or if the login was unsuccessful, back to the login page
     */
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
    
    /**
     * Resets the session logging the current user out and redirecting the user back to the login page
     */
    public static function logout() {
        $_SESSION['user'] = null;
        
        self::redirect_to('/login');
    }
    
    /**
     * Renders the <em>sign up</em> view
     */
    public static function signup() {
        self::render_view('user/signup.html');
    }
    
    /**
     * Asks for the {@link User} model class to insert a new row to the database based on the parameters given with HTTP Post request and either
     * <ol>
     *  <li>redirects to the login page if successful or</li>
     *  <li>if the new object didn't pass the validations, renders the <em>sign up</em> view displaying the errors that prohibited the object from being saved</li>
     * </ol>
     * 
     * @see User::validate_email()
     * @see User::validate_first_name()
     * @see User::validate_key()
     * @see User::validate_last_name()
     * @see User::validate_user_name()
     */
    public static function store() {
        $params = $_POST;
        
        if ($params['password'] != $params['password_repeat']) {
            self::render_view('user/signup.html', array('error' => 'Salasanat eivät täsmää!'));
        }
        
        $attributes = array('first_name' => StringUtil::trim_name($params['first_name']),
                            'last_name' => StringUtil::trim_name($params['last_name']),
                            'user_name' => StringUtil::trim($params['user_name']),
                            'email' => StringUtil::trim($params['email']),
                            'password' => $params['password']);
        
        $user = new User($attributes);
        
        if (count($user->errors()) != 0) {
            self::render_view('user/signup.html', array('errors' => $user->errors()));
        } else {
            $user_id = User::create($attributes);
            self::redirect_to('/login', array('message' => 'Uusi käyttäjä luotu!'));
        }
    }
}
