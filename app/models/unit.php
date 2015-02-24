<?php

/**
 * An abstraction of a {@link Product}'s unit
 *
 * @author kallionpetri
 */

class Unit extends BaseModel {
    public $id, $name_singular, $name_plural, $abbreviation; 
    
    /**
     * A function that validates the Unit's singular name.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_name_singular() {
        $errors = array();
        
        if ($this->name_singular == '' || $this->name_singular == null) {
            $errors[] = 'Nimi ei saa olla tyhjä.';
        }
        
        if (strlen($this->name_singular) < 2 || strlen($this->name_singular) > 24) {
            $errors[] = 'Nimen pituuden tulee olla vähintään 2 ja enintaan 24 merkkiä.';
        }
        
        $previous_units = self::all();

        foreach ($previous_units as $u) {
            if (strcasecmp($u->name_singular, $this->name_singular) == 0) {
                $errors[] = 'Samanniminen yksikkö löytyy jo tietokannasta.';
            }
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Unit's plural name.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_name_plural() {
        $errors = array();
        
        if ($this->name_plural == '' || $this->name_plural == null) {
            $errors[] = 'Monikko ei saa olla tyhjä.';
        }
        
        if (strlen($this->name_plural) < 2 || strlen($this->name_plural) > 28) {
            $errors[] = 'Monikon pituuden tulee olla vähintään 2 ja enintaan 24 merkkiä.';
        }
        
        $previous_units = self::all();

        foreach ($previous_units as $u) {
            if (strcasecmp($u->name_plural, $this->name_plural) == 0) {
                $errors[] = 'Sama monikko löytyy jo tietokannasta.';
            }
        }
        
        return $errors;
    }
    
    /**
     * A function that validates the Unit's abbreviation.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_abbreviation() {
        $errors = array();
        
        if ($this->name_abbreviation == '' || $this->name_abbreviation == null) {
            $errors[] = 'Lyhenne ei saa olla tyhjä.';
        }
        
        if (strlen($this->name_abbreviation) < 2 || strlen($this->name_abbreviation) > 9) {
            $errors[] = 'Lyhenteen pituuden tulee olla vähintään 2 ja enintaan 9 merkkiä.';
        }
        
        $previous_units = self::all();

        foreach ($previous_units as $u) {
            if (strcasecmp($u->name_abbreviation, $this->name_abbreviation) == 0) {
                $errors[] = 'Sama monikko löytyy jo tietokannasta.';
            }
        }
        
        return $errors;
    }
    
    /**
     * A constructor inherited from {@link BaseModel}. Validates the object based on validation rules.
     * 
     * @param array $attributes attributes used to construct an object (default null)
     */
    public function __construct($attributes = null) {
        parent::__construct($attributes);
        
        $this->validators = array('validate_name_singular', 'validate_name_plural',
            'validate_abbreviation');
    }

    /**
     * Retrieves all rows from the table <em>Unit</em> and constructs Unit objects from them finally returning them as an array.
     * 
     * @return array an array of Unit objects
     */
    public static function all() {
        $units = array();
        $rows = DB::query('SELECT * FROM Unit ORDER BY name_singular ASC');
        
        foreach ($rows as $row) {
            $units[] = self::create_unit($row);
        }
        
        return $units;
    }
    
    /**
     * Finds a row from the table <em>Unit</em> with the id given as parameter, constructs a Unit object of it and finally returns it.
     * 
     * @param integer $id
     * @return \Unit the Unit object corresponding to the id
     */
    public static function find($id) {
        if ($id != null) {
            $rows = DB::query('SELECT * FROM Unit WHERE id = :id LIMIT 1', array('id' => $id));
        
            if (count($rows) > 0) {
                $row = $rows[0];

                $unit = self::create_unit($row);
            }

            return $unit;
        } else {
            return null;
        }
    }
    
    /**
     * Updates the <em>Unit</em> table's row corresponding to the id given as parameter with the values given in the $params array.
     * 
     * @param integer $id
     * @param array $params
     */
    public static function update($id, $params) {
        DB::query('UPDATE Unit '
                . 'SET name_singular = :name_singular, '
                . '    name_plural = :name_plural, '
                . '    abbreviation = :abbreviation '
                . 'WHERE id = :id',
                array('name_singular' => $params['name_singular'],
                      'name_plural'   => $params['name_plural'],
                      'abbreviation'  => $params['abbreviation'],
                      'id'            => $id));
    }
    
    /**
     * Inserts a new row to the table <em>Unit</em> based upon the params. Returns the id of the new row.
     * 
     * @param array $params
     * @return integer the new unit's id
     */
    public static function create($params) {
        $rows = DB::query('INSERT INTO Unit (name_singular, name_plural, abbreviation) '
                        . 'VALUES (:name_singular, :name_plural, :abbreviation) '
                        . 'RETURNING ID',
                        array('name_singular' => $params['name_singular'],
                              'name_plural'   => $params['name_plural'],
                              'abbreviation'  => $params['abbreviation']
                        ));
        
        if (count($rows) > 0) {
            $row = $rows[0];
            
            $last_id = $row['id'];
        }
        
        return $last_id;
    }
    
    /**
     * Creates a <em>Unit</em> object based on a row from the table <em>Unit</em>.
     * 
     * @param type $row
     * @return \Unit
     */
    private static function create_unit($row) {
        return new Unit(array(
                    'id' => $row['id'],
                    'name_singular' => $row['name_singular'],
                    'name_plural' => $row['name_plural'],
                    'abbreviation' => $row['abbreviation']));
    }
}
