<?php

/**
 * A controller class to handle requests relating to {@link Purchase} model.
 * 
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class PurchaseController extends BaseController {
    
    /**
     * Asks for the {@link Purchase} model class to insert a new row to the database based on the parameters given with HTTP Post request and either
     * <ol>
     *  <li>redirects to the user's <em>active list view</em> or</li>
     *  <li>if the new object didn't pass the validations, redirects to the user's <em>active list view</em> displaying the errors that prohibited the object from being saved</li>
     * </ol>
     * 
     * @see Purchase::validate_amount()
     * @see Purchase::validate_department()
     * @see Purchase::validate_product()
     * @see Purchase::validate_shopping_list()
     * @see Purchase::validate_unit()
     */
    public static function store() {
        self::check_logged_in();
        
        $user = self::get_user_logged_in();
        $params = $_POST;
        $product = self::product_validation($user, $params);
        
        if (is_array($product)) {
            self::redirect_to('/list/' . $params['list'], array('errors' => $product));
        } else {
            $params['product'] = $product;
            Purchase::create($params);

            self::redirect_to('/list/' . $params['list'], array('message' => 'Ostos lisätty!'));
        }
    }
    
    /**
     * Passes the given {@link Purchase} object's id to the model class to set the current timestamp as the purchases purchase date
     * 
     * @param integer $id
     */
    public static function set_purchase_date($id) {
        self::check_logged_in();
        
        $params = $_POST;
        Purchase::set_purchase_date($params);
        
        self::redirect_to('/list/' . $id, array('message' => 'Lista päivitetty!'));
    }
    
    private static function product_validation($user, $params) {
        $product = Product::find_by_name($user->id, StringUtil::trim($params['name']));
        
        if (!$product) {
            return self::create_product($params);
        } else {
            return $product->id;
        }
    }

    private static function create_product($params) {
        self::check_logged_in();
        
        $attributes = ProductController::clean_attributes($params);
        $errors = ProductController::validation_errors($attributes);
        
        if (count($errors) == 0) {
            $attributes['owner'] = self::get_user_logged_in()->id;
            $product_id = Product::create($attributes);

            return $product_id;
        } else {
            return $errors;
        }
    }
    
    /**
     * Takes a {@link Purchase}'s id as a parameter, asks for the corresponding object from the model class and renders its <em>edit</em> view.
     * 
     * @param integer $id an id of a particular {@link Purchase} object
     */
    public static function edit($id) {
        self::check_logged_in();
        
        $purchase = Purchase::find($id);
        $product = Product::find($purchase->product);
        
        $attributes = array('id' => $purchase->id,
                            'name' => $product->name,
                            'amount' => $purchase->amount,
                            'unit' => $purchase->unit,
                            'department' => $purchase->department,
                            'list' => $purchase->shopping_list
                            );
        
        $units = Unit::all();
        $departments = Department::all();
        
        self::render_view('/purchase/edit.html', array('attributes' => $attributes, 
                                                        'units' => $units,
                                                        'departments' => $departments));
    }
    
    /**
     * Asks for the {@link Purchase} model class to update the row in the database with the corresponding id based on the parameters given with HTTP Post request and either
     * <ol>
     *  <li>redirects to the user's <em>active list view</em> or</li>
     *  <li>if the new object didn't pass the validations, redirects to the user's <em>active list view</em> displaying the errors that prohibited the object from being saved</li>
     * </ol>
     * 
     * @see Purchase::validate_amount()
     * @see Purchase::validate_department()
     * @see Purchase::validate_product()
     * @see Purchase::validate_shopping_list()
     * @see Purchase::validate_unit()
     * @param integer $id an id of a particular {@link Purchase} object
     */
    public static function update($id) {
        self::check_logged_in();
        
        $user = self::get_user_logged_in();
        $params = $_POST;
        
        $product = self::product_validation($user, $params);
        
        if (is_array($product)) {
            self::redirect_to('/list/' . $params['list'], array('errors' => $product));
        } else {
            $params = self::clean_attributes($params);
            $params['product'] = $product;
            $params['id'] = $id;
            $params['shopping_list'] = $params['list'];
            $errors = self::validation_errors($params);
            
            if (count($errors) == 0) {
                Purchase::update($params);
                self::redirect_to('/list/' . $params['list'], array('message' => 'Ostosta muokattu!'));
            } else {
                self::redirect_to('/list/' . $params['list'], array('errors' => $errors));
            }
        }
    }
    
    /**
     * A helper method to clean the attributes before they are passed to the {@link Purchase::create($params)} or {@link Purchase::update($params)} methods.
     * 
     * @param array $params "raw" params
     * @return array "cleaned" attributes
     */
    
    public static function clean_attributes($params) {
        if ($params['unit'] == 'null') {
            $params['unit'] = null;
        }
        
        if ($params['department'] == 'null') {
            $params['department'] = null;
        }
        
        return $params;
    }
    
    /**
     * A helper method that returns the possible validation errors that creating a new {@link Purchase} object would cause
     * 
     * @param array $attributes
     * @return array an array of error messages
     */
    public static function validation_errors($attributes) {
        $purchase = new Purchase($attributes);
        
        return $purchase->errors();
    }
    
    /**
     * Takes a {@link Purchase}'s id as a parameter, asks the model class to delete the corresponding row from the database and redirects to the user's <em>active list</em> view.
     * 
     * @param integer $id an id of a particular {@link Purchase} object
     */
    public static function destroy($id) {
        self::check_logged_in();
        
        Purchase::delete($id);
        
        self::redirect_to('/lists');
    }
}
    