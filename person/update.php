<?php
// /person/update.php

require_once '../classes/Model/Person.php';

// prevent access without id parameter
$params = $_POST;
$dialogue = '';

if(count($params) == 0 && !isset($_GET['id'])) {
    $dialogue = '<div class="alert alert-danger"><strong>Erreur :(</strong> Problème d\'accès à la page !</div>';
}

$person = null;
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $person = Person::get($id);
} elseif(isset($_POST['id'])) {
    $id = $_POST['id'];
    $person = Person::get($id);
}

if(count($params) > 0) {
    try {
        $person->update($params);
        $dialogue = '<div class="alert alert-success"><strong>Yeaah!</strong>La personne ' . $person->firstname . ' ' . $person->lastname . ' a été correctement mise à jour.</div>';
    } catch (Exception $e) {
        $dialogue = '<div class="alert alert-danger"><strong>Erreur :(</strong>Une erreur est survenue lors de la mise à jour<br>' . $e->getMessage() . '</div>';
    }
}

?>

<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../ressources/style/style.css" type="text/css">
    <meta charset="UTF-8">
    <title>Modifier une personne</title>
</head>
<body>
<div class="container">
    <h1>Modifier une personne</h1>
    <br>
    <?php echo $dialogue;
    if($person !== null) {
        ?>
        <form action="update.php" method="post">
            <input type="hidden" name="id" value="<?php echo $person->id; ?>">
            <div class="form-group">
                <label for="firstname">Prénom</label>
                <input type="text" class="form-control" id="firstname" name="firstname" aria-describedby="firstnameHelp"
                       value="<?php echo $person->firstname; ?>">
                <small id="firstnameHelp" class="form-text text-muted">Ceci est une aide pour le champ du prénom.
                </small>
            </div>

            <div class="form-group">
                <label for="lastname">Nom</label>
                <input type="text" class="form-control" id="lastname" name="lastname" aria-describedby="lastnameHelp"
                       value="<?php echo $person->lastname; ?>">
                <small id="lastnameHelp" class="form-text text-muted">Ceci est une aide pour le champ du Nom.</small>
            </div>

            <div class="form-group">
                <label for="email">Adresse mail</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp"
                       value="<?php echo $person->email; ?>">
                <small id="emailHelp" class="form-text text-muted">Ceci est une aide pour le champ du mail.</small>
            </div>

            <input type="submit" role="button" class="btn btn-primary" value="Mettre à jour">
        </form>
        <?php
    }
    ?>
</div>
</body>
</html>

