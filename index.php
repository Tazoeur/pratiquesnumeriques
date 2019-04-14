<?php

require_once __DIR__ . '/classes/Model/Person.php';

$persons = Person::get_all();

print_r($persons);