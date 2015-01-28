<?php

class ProductController extends BaseController {
    
    public static function index() {
        $products = Product::all();
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
        $product = Product::find($id);
        $product_unit = Unit::find($product->unit);
        $product_department = Department::find($product->department);
        
        self::render_view('product/show.html', array('product' => $product, 
            'product_unit' => $product_unit, 'product_department' => $product_department));
    }
    
    public static function delete($id) {
        Product::delete($id);
        
        self::redirect_to('/products', array('message' => 'Tuote poistettu!'));
    }
    
    public static function create() {
        $units = Unit::all();
        $departments = Department::all();
        
        self::render_view('product/new.html', array('units' => $units, 'departments' => $departments));
    }
    
    public static function store() {
        $params = $_POST;
        
        $id = Product::create(array(
            'name' => $params['name'],
            'department' => $params['department'],
            'unit' => $params['unit'],
            'owner' => 1
        ));
        
        self::redirect_to('/product/' . $id, array('message' => 'Tuote lisätty!'));
    }
}
