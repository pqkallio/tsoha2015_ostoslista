<?php

/**
 * Description of purchases_controller
 *
 * @author kallionpetri
 */
class PurchaseController extends BaseController {
    
    public static function store() {
        self::check_logged_in();
        
        $user = self::get_user_logged_in();
        $params = $_POST;
        $product = Product::find_by_name($user->id, strtolower(trim($params['product_name'])));
        
        if (!$product) {
            $product_id = self::create_product($params);
        } else {
            $product_id = $product->id;
        }
        
        Purchase::create(array(
            'list' => $params['list'],
            'product' => $product_id,
            'department' => $params['department'],
            'unit' => $params['unit'],
            'amount' => $params['amount']
        ));
        
        self::redirect_to('/list/' . $params['list'], array('message' => 'kaikki muka ok...'));
    }
    
    public static function set_purchase_date($id) {
        $params = $_POST;
        Purchase::set_purchase_date($params);
        
        self::redirect_to('/list/' . $id, array('message' => 'Lista päivitetty!'));
    }
    
    private static function create_product($attributes) {
        $params = array();
        
        $params['name'] = $attributes['product_name'];
        $params['department'] = $attributes['department'];
        $params['unit'] = $attributes['unit'];
        $params['owner'] = self::get_user_logged_in()->id;
        
        $product_id = Product::create($params);
        
        return $product_id;
    }
}
