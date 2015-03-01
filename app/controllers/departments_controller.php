<?php

/**
 * A controller class to handle requests relating to {@link Department} model.
 * 
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class DepartmentController extends BaseController {
    
    /**
     * Prepare variables needed to render the <em>all deparments</em> view and render it.
     */
    public static function index() {
        self::check_logged_in();
        $departments = Department::all();
        
        self::render_view('department/index.html', array('departments' => $departments));
    }
    
    /**
     * Takes a {@link Department}'s id as a parameter, asks for the corresponding object from the model class and renders its <em>show</em> view.
     * 
     * @param integer $id an id of a particular {@link Department} object
     */
    public static function show($id) {
        self::check_logged_in();
        $department = Department::find($id);
        
        self::render_view('department/show.html', array('department' => $department));
    }
        
    /**
     * Prepares the variables needed to render the <em>create new department</em> view and renders it.
     */
    public static function create() {
        self::render_view('department/new.html');
    }

    /**
     * Asks for the {@link Department} model class to insert a new row to the database based on the parameters given with HTTP Post request and either
     * <ol>
     *  <li>redirects to the all {@link Department}'s view or</li>
     *  <li>if the new object didn't pass the validations, renders the <em>create new department</em> view displaying the errors that prohibited the object from being saved</li>
     * </ol>
     * 
     * @see Department::validate_name()
     * @see Department::validate_abbreviation()
     */
    public static function store() {
        self::check_logged_in();
        
        $params = $_POST;
        
        $attributes = self::clean_attributes($params);
        
        $errors = self::validation_errors($attributes);
        
        if (count($errors) != 0) {
            self::render_view('department/new.html', array('attributes' => $attributes, 
                'errors' => $errors));
        } else {
            Department::create($params);
            
            self::redirect_to('/departments', array('message' => 'Osasto lisätty!'));
        }
    }
    
    /**
     * Takes a {@link Department}'s id as a parameter, asks for the corresponding object from the model class and renders its <em>edit</em> view.
     * 
     * @param integer $id an id of a particular {@link Department} object
     */
    public static function edit($id) {
        self::check_logged_in();
        $department = Department::find($id);
        
        self::render_view('department/edit.html', array('attributes' => $department));
    }
    
    /**
     * Asks for the {@link Department} model class to update the row in the database with the corresponding id based on the parameters given with HTTP Post request and either
     * <ol>
     *  <li>redirects to the all {@link Department}s' view or</li>
     *  <li>if the new object didn't pass the validations, renders the {@link Department}'s <em>edit department</em> view displaying the errors that prohibited the update from being made</li>
     * </ol>
     * 
     * @see Department::validate_name()
     * @see Department::validate_abbreviation()
     * @param integer $id
     */
    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
        
        $attributes = self::clean_attributes($params);
        $attributes['id'] = $id;
        
        $errors = self::validation_errors($attributes);
        
        if (count($errors) == 0) {
            Department::update($id, $attributes);
        
            self::redirect_to('/departments', 
                    array('message' => 'Osastoa muokattiin onnistuneesti!'));
        } else {
            self::render_view('department/edit.html', 
                    array('errors' => $errors, 'attributes' => $attributes,
                        'name_original' => Department::find($id)->name));
        }
    }
    
    /**
     * Takes a {@link Department}'s id as a parameter, asks the model class to delete the corresponding row from the database and redirects to the user's <em>all departments</em> view.
     * 
     * @param integer $id an id of a particular {@link Department} object
     */
    public static function destroy($id) {
        self::check_logged_in();
        $department_name = Department::find($id)->name;
        Department::delete($id);
        
        self::redirect_to('/departments', array('message' => 'Osasto ' . $department_name . ' poistettu!'));
    }
    
    private static function clean_attributes($params) {
        if (is_null($params['abbreviation']) || $params['abbreviation'] == '') {
            $params['abbreviation'] = null;
        }
        
        $attributes = array(
            'name' => StringUtil::trim($params['name']),
            'abbreviation' => StringUtil::trim($params['abbreviation'])
        );
        
        return $attributes;
    }
    
    private static function validation_errors($attributes) {
        $department = new Department($attributes);
        
        return $department->errors();
    }
}
