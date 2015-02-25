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
            $return_value = self::create_product($params);
        } else {
            $return_value = $product->id;
        }
        
        if (is_array($return_value)) {
            self::redirect_to('/list/' . $params['list'], array('errors' => $return_value));
        } else {
            Purchase::create(array(
                'list' => $params['list'],
                'product' => $return_value,
                'department' => $params['department'],
                'unit' => $params['unit'],
                'amount' => $params['amount']
            ));

            self::redirect_to('/list/' . $params['list'], array('message' => 'Ostos lisätty!'));
        }
    }
    
    public static function set_purchase_date($id) {
        $params = $_POST;
        Purchase::set_purchase_date($params);
        
        self::redirect_to('/list/' . $id, array('message' => 'Lista päivitetty!'));
    }
    
    private static function create_product($attributes) {
        $params = array();
        
        if ($attributes['unit'] == 'null') {
            $attributes['unit'] = null;
        }
        
        if ($attributes['department'] == 'null') {
            $attributes['department'] = null;
        }
        
        $params['name'] = $attributes['product_name'];
            $params['department'] = $attributes['department'];
            $params['unit'] = $attributes['unit'];
            $params['owner'] = self::get_user_logged_in()->id;
            
        $product = new Product($params);
        
        if (count($product->errors()) == 0) {
            $product_id = Product::create($params);

            return $product_id;
        } else {
            return $product->errors();
        }
    }
    
    public static function destroy($id) {
        Purchase::delete($id);
        
        self::redirect_to('/lists');
    }
}
    