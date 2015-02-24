<?php

/**
 * Description of string_util
 *
 * @author kallionpetri
 */
class StringUtil {
    
    public static function trim($object) {
        if (is_string($object)) {
            return strtolower(trim($object));
        } else {
            return $object;
        }
    }
    
    public static function trim_name($object) {
        if (is_string($object)) {
            return ucfirst(trim($object));
        } else {
            return $object;
        }
    }
}
