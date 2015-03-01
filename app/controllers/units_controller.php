<?php

/**
 * A controller class to handle requests relating to {@link Unit} model.
 * 
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class UnitController extends BaseController {
    
    /**
     * Prepare variables needed to render the <em>all units</em> view and render it.
     */
    public static function index() {
        self::check_logged_in();
        $units = Unit::all();
        
        self::render_view('unit/index.html', array('units' => $units));
    }
    
    /**
     * Takes a {@link Unit}'s id as a parameter, asks for the corresponding object from the model class and renders its <em>show</em> view.
     * 
     * @param integer $id an id of a particular {@link Unit} object
     */
    public static function show($id) {
        self::check_logged_in();
        $unit = Unit::find($id);
        
        self::render_view('unit/show.html', array('unit' => $unit));
    }
    
    /**
     * Takes a {@link Unit}'s id as a parameter, asks for the corresponding object from the model class and renders its <em>edit</em> view.
     * 
     * @param integer $id an id of a particular {@link Unit} object
     */
    public static function edit($id) {
        self::check_logged_in();
        $unit = Unit::find($id);
        
        self::render_view('unit/edit.html', array('attributes' => $unit));
    }
    
    /**
     * Asks for the {@link Unit} model class to update the row in the database with the corresponding id based on the parameters given with HTTP Post request and either
     * <ol>
     *  <li>redirects to the all {@link Unit}s view or</li>
     *  <li>if the new object didn't pass the validations, renders the {@link Unit}'s <em>edit unit</em> view displaying the errors that prohibited the update from being made</li>
     * </ol>
     * 
     * @see Unit::validate_abbreviation()
     * @see Unit::validate_name_plural()
     * @see Unit::validate_name_singular()
     * @param integer $id
     */
    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
        
        $attributes = array(
            'id' => $id,
            'name_singular' => $params['name_singular'],
            'name_plural' => $params['name_plural'],
            'abbreviation' => $params['abbreviation']
        );
        
        $unit = new Unit($attributes);
        
        if (count($unit->errors()) == 0) {
            Unit::update($id, $attributes);
        
            self::redirect_to('/units', array('message' => 'Yksikköä muokattiin onnistuneesti!'));
        } else {
            self::render_view('/unit/edit.html', array('attributes' => $attributes, 'errors' => $unit->errors(), 'name_original' => Unit::find($id)->name_singular));
        }
    }
    
    /**
     * Renders the <em>create new unit</em> view.
     */
    public static function create() {
        self::check_logged_in();
        self::render_view('unit/new.html');
    }
    
    /**
     * Asks for the {@link Unit} model class to insert a new row to the database based on the parameters given with HTTP Post request and either
     * <ol>
     *  <li>redirects to the <em>all units</em> view or</li>
     *  <li>if the new object didn't pass the validations, renders the <em>create new product</em> view displaying the errors that prohibited the object from being saved</li>
     * </ol>
     * 
     * @see Unit::validate_abbreviation()
     * @see Unit::validate_name_plural()
     * @see Unit::validate_name_singular()
     */
    public static function store() {
        self::check_logged_in();
        $params = $_POST;
        
        $attributes = array(
            'name_singular' => $params['name_singular'],
            'name_plural' => $params['name_plural'],
            'abbreviation' => $params['abbreviation']
        );
        
        $unit = new Unit($attributes);
        
        if (count($unit->errors()) == 0) {
            Unit::create($attributes);
        
            self::redirect_to('/units', array('message' => 'Uusi yksikkö luotu onnistuneesti!'));
        } else {
            self::render_view('/unit/new.html', array('errors' => $unit->errors(), 'attributes' => $attributes));
        }
    }
    
    /**
     * Takes a {@link Unit}'s id as a parameter, asks the model class to delete the corresponding row from the database and redirects to the <em>all units</em> view.
     * 
     * @param integer $id an id of a particular {@link Unit} object
     */
    public static function destroy($id) {
        self::check_logged_in();
        
        Unit::delete($id);
        
        self::redirect_to('/units', array('message' => "Yksikkö poistettu!"));
    }
}
