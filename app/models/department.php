<?php

/**
 * An abstraction of a shop department linked to a particular {@link Product} or {@link Purchase}.
 *
 * @author Petri Kallio <kallionpetri@gmail.com>
 */
class Department extends BaseModel {
    public $id, $name, $abbreviation;
    
    /**
     * A function that validates the Department's name.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_name() {
        $errors = array();
        
        if ($this->name == '' || $this->name == null) {
            $errors[] = 'Nimi ei saa olla tyhjä.';
        }
        
        if (strlen($this->name) < 2 || strlen($this->name) > 50) {
            $errors[] = 'Nimen pituuden tulee olla vähintään 2 ja enintaan 50 merkkiä.';
        }
        
        
        $previous_departments = self::all();

        foreach ($previous_departments as $d) {
            if (strcasecmp($d->name, $this->name) == 0) {
                $errors[] = 'Olet jo lisännyt samannimisen osaston.';
            }
        }
                
        return $errors;
    }
    
    /**
     * A function that validates the Department's abbreviation.
     * 
     * @return array an array of error messages as strings
     */
    public function validate_abbreviation() {
        $errors = array();
        
        if (strlen($this->abbreviation) > 12) {
            $errors[] = 'Lyhenteen pituuden tulee olla enintaan 12 merkkiä.';
        }
                
        return $errors;
    }
    
    /**
     * Constructor method inherited from {@link BaseModel}. Validates the object based on validation rules.
     * 
     * @param type $attributes the attributes to create a new object from
     */
    public function __construct($attributes = null) {
        parent::__construct($attributes);
        
        $this->validators = array('validate_name', 'validate_abbreviation');
    }
    
    /**
     * Fetch all rows from the table <em>Department</em>, construct Department objects from them and finally return them as an array.
     * 
     * @return array an array of Department objects
     */
    public static function all() {
        $departments = array();
        
        $rows = DB::query('SELECT * FROM Department');
        
        foreach ($rows as $row) {
            $departments[] = self::create_department($row);
        }
        
        return $departments;
    }
    
     /**
      * Finds a row from the table <em>Department</em> with the id given as parameter, constructs a Department object of it and finally returns it.
      * 
      * @param integer $id
      * @return Department
      */
    public static function find($id) {
        if ($id != null) {
            $rows = DB::query('SELECT * FROM Department WHERE id = :id LIMIT 1', array('id' => $id));

            if (count($rows) > 0) {
                $row = $rows[0];

                $department = self::create_department($row);
            }

            return $department;
        } else {
            return null;
        }
    }
    
    /**
     * Inserts a new row to the table <em>Department</em> based upon the params. Returns the id of the new row.
     * 
     * @param array $params
     * @return integer
     */
    public static function create($params) {
        $rows = DB::query('INSERT INTO Department (name, abbreviation) '
                . 'VALUES (:name, :abbreviation) RETURNING id',
                array('name' => StringUtil::trim(($params['name'])), 
                    'abbreviation' => $params['abbreviation']));
        
        $last_id = $rows[0];
        return $last_id['id'];
    }
    
    /**
     * Update the row of the table <em>Department</em> where id matches the one given as parameter.
     * 
     * @param type $id
     * @param type $params an array of parameters to update
     */
    public static function update($id, $params) {
        DB::query('UPDATE Department '
                . 'SET name = :name, abbreviation = :abbreviation '
                . 'WHERE id = :id', 
                array('name' => $params['name'],
                      'abbreviation' => $params['abbreviation'],
                      'id' => $id));
    }
    
    /**
     * Delete the row of the table <em>Department</em> where id matches the one given as parameter.
     * 
     * @param type $id
     */
    public static function delete($id) {
        DB::query('DELETE FROM Department WHERE id = :id', array('id' => $id));
    }
    
    /**
     * Creates a new Department object based on a <em>Department</em> database table row
     * 
     * @param type $row the row from database given as array
     * @return \Department
     */
    private static function create_department($row) {
        $department = new Department(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'abbreviation' => $row['abbreviation']
            ));
        
        return $department;
    }
}
