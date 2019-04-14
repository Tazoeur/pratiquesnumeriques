<?php
// /person/add.php

require_once __DIR__ . '/../classes/Model/Person.php';
$params = $_POST;
$dialogue = '';

if(count($params) > 0) {
    try {
        $id = Person::create($params);
    } catch (Exception $e) {
        $dialogue = '<div class="alert alert-danger"><strong>Erreur :(</strong>Une erreur est survenue lors de l\'enregistrement<br>' . $e->getMessage() . '</div>';
    }

    if($id !== false) {
        $person = Person::get($id);
        $dialogue = '<div class="alert alert-success"><strong>Yeaah!</strong>La personne ' . $person->firstname . ' ' . $person->lastname . ' a été correctement enregistrée.</div>';
    } else {
        $dialogue = '<div class="alert alert-danger"><strong>Erreur :(</strong>Une erreur est survenue lors de l\'enregistrement</div>';
    }
}

?>

<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="../ressources/style/style.css" type="text/css">
        <meta charset="UTF-8">
        <title>Ajouter une personne</title>
    </head>
    <body>
        <div class="container">
            <h1>Ajouter une personne</h1>
            <br>
            <?php echo $dialogue; ?>
            <form action="add.php" method="post">
                <div class="form-group">
                    <label for="firstname">Prénom</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" aria-describedby="firstnameHelp" placeholder="Renseigner votre prénom ici">
                    <small id="firstnameHelp" class="form-text text-muted">Ceci est une aide pour le champ du prénom.</small>
                </div>

                <div class="form-group">
                    <label for="lastname">Nom</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" aria-describedby="lastnameHelp" placeholder="Renseigner votre Nom ici">
                    <small id="lastnameHelp" class="form-text text-muted">Ceci est une aide pour le champ du Nom.</small>
                </div>

                <div class="form-group">
                    <label for="email">Adresse mail</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="email@example.com">
                    <small id="emailHelp" class="form-text text-muted">Ceci est une aide pour le champ du mail.</small>
                </div>

                <input type="submit" role="button" class="btn btn-primary" value="Ajouter">
            </form>
        </div>
    </body>
</html>

