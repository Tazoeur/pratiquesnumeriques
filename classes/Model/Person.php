<?php
// classes/Model/Person.php

require_once __DIR__ . '/../Helper/DB.php';

class Person
{
    public static $table = 'person';

    private $id;
    private $firstname;
    private $lastname;
    private $email;

    /**
     * Person constructor.
     *
     * The constructor is private because the only way to
     *
     * @param stdClass $object
     */
    private function __construct(stdClass $object)
    {
        $this->id = $object->id;
        $this->firstname = $object->firstname;
        $this->lastname = $object->lastname;
        $this->email = $object->email;
    }

    public static function get_all() {
        $persons = DB::select(self::$table);
        $tab = [];
        foreach($persons as $person) {
            $p = (object) $person;
            $tab[$p->id] = new Person($person);
        }
        return $tab;
    }

    public function output_as_table_line() {
        $output = '<tr>';
        $output .= "<th scope='row'>{$this->id}</th>";
        $output .= "<td>{$this->firstname}</td>";
        $output .= "<td>{$this->lastname}</td>";
        $output .= "<td>{$this->email}</td>";
        $link_delete = '<a href="delete.php?id=' . $this->id . '" role="button" class="btn"><i class="far fa-trash-alt text-danger"></i></a>';
        $link_update = '<a href="update.php?id=' . $this->id . '" role="button" class="btn"><i class="fas fa-pencil-alt"></i></a>';
        $output .= "<td>{$link_update}{$link_delete}</td>";
        $output .= '</tr>';
        return $output;
    }

    public static function create(array $parameters) {
        foreach(['firstname', 'lastname', 'email'] as $key) {
            if(!in_array($key, array_keys($parameters))) {
                throw new Exception("User cannot be created : $key missing");
            }
        }

        return DB::insert(self::$table, $parameters);
    }

    public static function get(int $id) {
        $tab = DB::select(self::$table, ['id' => $id]);
        if(count($tab) == 0) {
            throw new Exception("The Person with the id {$id} does not exist");
        }
        $o = $tab[array_keys($tab)[0]];
        return new Person($o);
    }


    public function __get($name)
    {
        if (property_exists(self::class, $name)) {
            return $this->$name;
        } else {
            throw new Exception("There is no property $name in the class " . self::class);
        }
    }

    public function __set($name, $value)
    {
        if (property_exists(self::class, $name)) {
            $this->$name = $value;
        } else {
            throw new Exception("There is no property $name in the class " . self::class);
        }
    }

    public function update(array $parameters) {
        unset($parameters['id']);
        foreach($parameters as $key => $value) {
            if(!property_exists(self::class, $key)) {
                unset($parameters[$key]);
            }
        }
        if(count($parameters) == 0) {
            return true;
        }

        $result = DB::update(self::$table, $parameters, ['id' => $this->id]);

        foreach($parameters as $key => $value) {
            $this->$key = $value;
        }

        return $result;
    }

    public function delete() {
        return DB::delete(self::$table, ['id' => $this->id]);
    }
}