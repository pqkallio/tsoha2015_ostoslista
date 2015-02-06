<?php

class ProductController extends BaseController {
    
    public static function index() {
        if (!parent::get_user_logged_in()) {
            self::redirect_to('/login');
        }
        
        $products = Product::find_by_owner(parent::get_user_logged_in()->id);
        $product_units = array();
        $product_departments = array();
        
        foreach ($products as $product) {
            array_push($product_units, Unit::find($product->unit));
            array_push($product_departments, Department::find($product->department));
        }
        
        self::render_view('product/index.html', array('products' => $products, 
            'product_units' => $product_units, 'product_departments' => $product_departments));
    }
    
    public static function show($id) {
        $product = self::check_ownership_and_existence($id);
        
        if (!$product) {
            self::redirect_to('/products', array('message' => 'Tuotetta ei löytynyt!'));
        } else {
            $product_unit = Unit::find($product->unit);
            $product_department = Department::find($product->department);

            self::render_view('product/show.html', array('product' => $product, 
                'product_unit' => $product_unit, 'product_department' => $product_department));
        }
    }
    
    public static function edit($id) {
        $product = self::check_ownership_and_existence($id);
        
        if (!$product) {
            self::redirect_to('/products', array('message' => 'Tuotetta ei löytynyt!'));
        } else {
            $units = Unit::all();
            $departments = Department::all();

            self::render_view('product/edit.html', array('attributes' => $product, 'units' => $units, 'departments' => $departments));
        }
    }
    
    public static function fave($id) {
        $product = self::check_ownership_and_existence($id);
        
        if ($product) {
            Product::fave($id, parent::get_user_logged_in()->id);
        }
        
        self::redirect_to('/products');
    }

    public static function delete($id) {
        $product = self::check_ownership_and_existence($id);
        
        if ($product) {
            Product::delete($id);

            self::redirect_to('/products', array('message' => 'Tuote poistettu!'));
        } else {
            self::redirect_to('/products', array('message' => 'Tuotetta ei löytynyt!'));
        }
    }
    
    public static function create() {
        $units = Unit::all();
        $departments = Department::all();
        
        self::render_view('product/new.html', array('units' => $units, 'departments' => $departments));
    }
    
    public static function store() {
        $params = $_POST;
        $units = Unit::all();
        $departments = Department::all();
        
        $attributes = array(
            'name' => $params['name'],
            'department' => $params['department'],
            'unit' => $params['unit'],
            'owner' => parent::get_user_logged_in()->id
        );
        
        $product = new Product($attributes);
        $errors = $product->errors();
        
        if (count($errors) == 0) {
            $id = Product::create($attributes);
            self::redirect_to('/product/' . $id, array('message' => 'Tuote lisätty!'));
        } else {
            self::render_view('product/new.html', array('errors' => $errors, 'attributes' => $attributes, 'units' => $units, 'departments' => $departments));
        }
        
    }
    
    public static function update($id) {
        $params = $_POST;
        $units = Unit::all();
        $departments = Department::all();
        $name_original = Product::find($id)->name;
        
        $attributes = array(
            'name' => $params['name'],
            'department' => $params['department'],
            'unit' => $params['unit'],
            'owner' => parent::get_user_logged_in()->id
        );
        
        $product = new Product($attributes);
        $errors = $product->errors();
        
        if (count($errors) == 0) {
            Product::update($id, $attributes);
            self::redirect_to('/product/' . $id, array('message' => 'Tuotetta on muokattu onnistuneesti!'));
        } else {
            $attributes['id'] = $id;
            self::render_view('product/edit.html', array('errors' => $errors, 'attributes' => $attributes, 'units' => $units, 'departments' => $departments, 'name_original' => $name_original));
        }
    }
    
    public static function check_ownership_and_existence($product_id) {
        $product = Product::find($product_id);
        
        if (!parent::get_user_logged_in()) {
            return null;
        }
        
        if ($product == null
                || $product->owner != parent::get_user_logged_in()->id) {
            return null;
        } else {
            return $product;
        }
    }
}
