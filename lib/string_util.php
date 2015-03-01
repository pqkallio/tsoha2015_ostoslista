<?php

/**
 * A helper class to edit strings
 *
 * @author kallionpetri
 */
class StringUtil {
    
    /**
     * Takes a string and returns it in trimmed lower-case form
     * 
     * @param string $object
     * @return string
     */
    public static function trim($object) {
        if (is_string($object)) {
            return strtolower(trim($object));
        } else {
            return $object;
        }
    }
    
    /**
     * Takes a string and returns it in trimmed capitalized form
     * 
     * @param string $object
     * @return string
     */
    public static function trim_name($object) {
        if (is_string($object)) {
            return ucfirst(trim($object));
        } else {
            return $object;
        }
    }
}
