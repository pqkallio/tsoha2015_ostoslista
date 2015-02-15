<?php

/**
 * A controller class to handle requests relating to {@link Product} model.
 * 
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class ProductController extends BaseController {
    
    /**
     * Prepare variables needed to render the user's <em>all products</em> view and render it.
     */
    public static function index() {
        self::check_logged_in();
        
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
    
    /**
     * Prepare variables needed to render the user's <em>favorite products</em> view and render it.
     */
    public static function favorites() {
        self::check_logged_in();
        
        $products = Product::find_by_owner(parent::get_user_logged_in()->id);
        $product_units = array();
        $product_departments = array();
        
        foreach ($products as $product) {
            array_push($product_units, Unit::find($product->unit));
            array_push($product_departments, Department::find($product->department));
        }
        
        self::render_view('product/favorites.html', array('products' => $products, 
            'product_units' => $product_units, 'product_departments' => $product_departments));
    }

    /**
     * Takes a {@link Product}'s id as a parameter, asks for the corresponding object from the model class and renders its <em>show</em> view.
     * 
     * @param integer $id an id of a particular {@link Product} object
     */
    public static function show($id) {
        self::check_logged_in();
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
    
    /**
     * Takes a {@link Product}'s id as a parameter, asks for the corresponding object from the model class and renders its <em>edit</em> view.
     * 
     * @param integer $id an id of a particular {@link Product} object
     */
    public static function edit($id) {
        self::check_logged_in();
        $product = self::check_ownership_and_existence($id);
        
        if (!$product) {
            self::redirect_to('/products', array('message' => 'Tuotetta ei löytynyt!'));
        } else {
            $units = Unit::all();
            $departments = Department::all();

            self::render_view('product/edit.html', array('attributes' => $product, 'units' => $units, 'departments' => $departments));
        }
    }
    
    /**
     * Takes a {@link Product}'s id as a parameter, asks the model class to add the corresponding product to the signed-in user's favorite products and redirects to the user's <em>all products</em> view.
     * 
     * @param integer $id an id of a particular {@link Product} object
     */
    public static function fave($id) {
        self::check_logged_in();
        $product = self::check_ownership_and_existence($id);
        
        if ($product) {
            Product::fave($id, parent::get_user_logged_in()->id);
        }
        
        self::redirect_to('/products');
    }
    
    /**
     * Takes a {@link Product}'s id as a parameter, asks the model class to delete the corresponding row from the database and redirects to the user's <em>all products</em> view.
     * 
     * @param integer $id an id of a particular {@link Product} object
     */
    public static function destroy($id) {
        self::check_logged_in();
        $product = self::check_ownership_and_existence($id);
        
        if ($product) {
            Product::delete($id);

            self::redirect_to('/products', array('message' => 'Tuote poistettu!'));
        } else {
            self::redirect_to('/products', array('message' => 'Tuotetta ei löytynyt!'));
        }
    }
    
    /**
     * Prepares the variables needed to render the <em>create new product</em> view and renders it.
     */
    public static function create() {
        self::check_logged_in();
        $units = Unit::all();
        $departments = Department::all();
        
        self::render_view('product/new.html', array('units' => $units, 'departments' => $departments));
    }
    
    /**
     * Asks for the {@link Product} model class to insert a new row to the database based on the parameters given with HTTP Post request and either
     * <ol>
     *  <li>redirects to the newly added {@link Product}'s <em>show</em> view or</li>
     *  <li>if the new object didn't pass the validations, renders the <em>create new product</em> view displaying the errors that prohibited the object from being saved</li>
     * </ol>
     * 
     * @see Product::validate_name()
     * @see Product::validate_department()
     * @see Product::validate_unit()
     * @see Product::validate_owner()
     */
    public static function store() {
        self::check_logged_in();
        $params = $_POST;
        $units = Unit::all();
        $departments = Department::all();
        
        $attributes = array(
            'name' => strtolower(trim($params['name'])),
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
    
    /**
     * Asks for the {@link Product} model class to update the row in the database with the corresponding id based on the parameters given with HTTP Post request and either
     * <ol>
     *  <li>redirects to the updated {@link Product}'s <em>show</em> view or</li>
     *  <li>if the new object didn't pass the validations, renders the {@link Product}'s <em>edit product</em> view displaying the errors that prohibited the update from being made</li>
     * </ol>
     * 
     * @see Product::validate_name()
     * @see Product::validate_department()
     * @see Product::validate_unit()
     * @see Product::validate_owner()
     * @param integer $id
     */
    public static function update($id) {
        self::check_logged_in();
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
    
    /**
     * A helper function to check the signed-in user's ownership to the {@link Product} which id's given as parameter as well as the existence of the product
     * 
     * @param integer $product_id the id of a {@link Product} object
     * @return null or {@link Product}
     */
    private static function check_ownership_and_existence($product_id) {
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
