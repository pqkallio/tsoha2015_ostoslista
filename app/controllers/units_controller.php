<?php

/**
 * Description of units_controller
 *
 * @author kallionpetri
 */
class UnitController extends BaseController {
    
    public static function index() {
        self::check_logged_in();
        $units = Unit::all();
        
        self::render_view('unit/index.html', array('units' => $units));
    }
    
    public static function show($id) {
        $unit = Unit::find($id);
        
        self::render_view('unit/show.html', array('unit' => $unit));
    }
    
    public static function edit($id) {
        $unit = Unit::find($id);
        
        self::render_view('unit/edit.html', array('attributes' => $unit));
    }
    
    public static function update($id) {
        $params = $_POST;
        
        $attributes = array(
            'name_singular' => $params['name_singular'],
            'name_plural' => $params['name_plural'],
            'abbreviation' => $params['abbreviation']
        );
        
        Unit::update($id, $attributes);
        
        $units = Unit::all();
        self::redirect_to('/units', array('message' => 'Yksikköä muokattiin onnistuneesti!', 'units' => $units));
    }
    
    public static function create() {
        self::render_view('unit/new.html');
    }
    
    public static function store() {
        $params = $_POST;
        
        $attributes = array(
            'name_singular' => $params['name_singular'],
            'name_plural' => $params['name_plural'],
            'abbreviation' => $params['abbreviation']
        );
        
        Unit::create($attributes);
        
        self::redirect_to('/units', array('message' => 'Yksikkö luotu onnistuneesti!'));
    }
}
