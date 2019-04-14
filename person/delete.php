<?php
// /person/delete.php

require_once '../classes/Model/Person.php';

$dialogue = '';
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $person = Person::get($id);
        $person->delete();
        $dialogue = '<div class="alert alert-success"><strong>Yeaah!</strong>La suppression s\'est déroulée correctement.</div>';
    } catch (Exception $e) {
        $dialogue = '<div class="alert alert-danger"><strong>Erreur :(</strong>Une erreur est survenue lors de la suppression<br>' . $e->getMessage() . '</div>';
    }
} else {
    $dialogue = '<div class="alert alert-danger"><strong>Erreur :(</strong> Problème d\'accès à la page : il manque l\'id de la personne !</div>';
}
?>

<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../ressources/style/style.css" type="text/css">
    <meta charset="UTF-8">
    <title>Supprimer une personne</title>
</head>
<body>
<div class="container">
    <h1>Supprimer une personne</h1>
    <br>
    <?php echo $dialogue; ?>
</div>
</body>
</html>
