<?php
// /person/list.php

require_once __DIR__ . '/../classes/Model/Person.php';

$persons = Person::get_all();

?>

<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        <link rel="stylesheet" href="../ressources/style/style.css" type="text/css">
        <meta charset="UTF-8">
        <title>Liste des Personnes</title>
    </head>
    <body>
        <div class="container">
            <h1>Liste des personnes yeah</h1>
            <br>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Prénom</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Mail</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($persons as $person) {
                        echo $person->output_as_table_line();
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
