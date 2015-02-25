<?php

/**
 * Description of departments_controller
 *
 * @author kallionpetri
 */
class DepartmentController extends BaseController {
    
    public static function index() {
        self::check_logged_in();
        $departments = Department::all();
        
        self::render_view('department/index.html', array('departments' => $departments));
    }
    
    public static function show($id) {
        self::check_logged_in();
        $department = Department::find($id);
        
        self::render_view('department/show.html', array('department' => $department));
    }
        
    public static function create() {
        self::render_view('department/new.html');
    }

    public static function store() {
        self::check_logged_in();
        
        $params = $_POST;
        
        if (is_null($params['abbreviation']) || $params['abbreviation'] == '') {
            $params['abbreviation'] = null;
        }
        
        $department = new Department($params);
        
        if (count($department->errors()) != 0) {
            self::render_view('/department/new.html', array('attributes' => $params, 
                'errors' => $department->errors()));
        } else {
            Department::create($params);
            
            self::redirect_to('/departments', array('message' => 'Osasto lisätty!'));
        }
    }
    
    public static function edit($id) {
        self::check_logged_in();
        $department = Department::find($id);
        
        self::render_view('department/edit.html', array('attributes' => $department));
    }
    
    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
        
        $attributes = array(
            'name' => $params['name'],
            'abbreviation' => $params['abbreviation']
        );
        
        Department::update($id, $attributes);
        
        $departments = Department::all();
        self::redirect_to('/departments', array('message' => 'Osastoa muokattiin onnistuneesti!', 'departments' => $departments));
    }
    
    public static function destroy($id) {
        self::check_logged_in();
        $department_name = Department::find($id)->name;
        Department::delete($id);
        
        self::redirect_to('/departments', array('message' => 'Osasto ' . $department_name . ' poistettu!'));
    }
}
