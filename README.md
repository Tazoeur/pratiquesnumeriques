# pratiquesnumeriques
Utilisation de PHP &amp; MySQL pour un projet simple


## Contexte

Dans ce projet je vais proposer une façon d'implémenter l'utilisation d'une bibliothèque simple.

C'est un sujet éculé pour les cours de programmation car beaucoup de concepts peuvent être abordés et chacun d'entre eux
possède une représentation tengible dans l'esprit des apprenants.  

Donc notre bibliothèque sera constituée de **livres**.
Des **personnes** pourront **emprunter** ces livres.

Le premier objectif, après avoir créé notre base de données sera d'avoir un CRUD fonctionnel 
pour chaque concept clef de notre application.

> Le CRUD (Create Read Update Delete) est un acronyme qui désigne les fonctionnalités de création, 
de mise à jour, de lecture et de suppression des objets que l'on va manipuler.

Les livres seront caractérisés par
* un titre
* un auteur
* une description

Les personnes seront caractérisées par 
* un nom
* un prénom
* une adresse mail

Les prêts seront caractérisés par 
* un livre
* une personne
* une date de début de prêt
* une date de fin de prêt
* la date à laquelle le livre a été rendu


Ensuite les objectifs seront les suivants :
* ajouter des _images_ à notre objet livre, ce qui implique la gestion en base de donnée et la modification du CRUD
* ajouter une notion de **categorie** de manière à pouvoir classer les livres par catégories 
* implémenter une vue de recherche
    * de personne (nom/prenom/categorie)
    * de livre (titre/auteur)
    * de prêt (personne/livre/date)
* implémenter une vue de statistiques
    * Quelle est la propention d'une personne à rendre ses livres dans les délais impartis ?
    * Quels catégories sont les plus lues ?
    * Quelle(s) est/sont la/les catégorie(s) la/les plus lue(s) par une personne ?
    * ...
    
## Configuration

Je pars du principe que l'application que vous allez développer au fur et à mesure de ce tutoriel ne sera jamais utilisée 
qu'en local.

Il vous faut 
* un serveur web qui va servir les pages HTML à votre navigateur
* un serveur de base de données SQL
* un interpréteur PHP

Installer le [XAMPP](https://www.apachefriends.org/fr/index.html) qui correspond à votre OS.

Voici ma configuration au moment de la rédaction de ce tutoriel :

| Outil   | Version |
| ------- | ------- |
| Apache  | 2.4.34  |
| PHP     | 7.2.15  |
| MySQL   | 5.7.25  |

mais en vrai osef

#### Vérifier que l'installation s'est bien passée

##### 1. Vérifier le bon fonctionnement d'Apache :

se connecter sur [localhost](http://localhost)

un message attestant du bon fonctionnement de Apache doit apparaître.

##### 2. Vérifier le bon fonctionnement de PHP :

* créer un fichier `info.php` avec le contenu suivant :
```php
<?php
phpinfo();
```

* déplacer ce fichier à la racine de votre `/var/www/`
* se connecter sur [localhost/info.php](http://localhost/info.php)

la page d'information de PHP doit apparaitre.

##### 3. Création d'une base de donnée et d'utilisateur SQL pour votre projet

* se rendre sur PhpMyAdmin
* créer un user
* créer une base de données

##### 4. Installation d'un IDE

Même s'il est possible de développer n'importe quoi avec n'importe quel éditeur de texte, ne vous infligez pas ça!
Si vous ne savez pas quoi utiliser, dirigez vous vers [vscode](https://code.visualstudio.com/).
Si vous êtes riche, achetez [PhpStorm](https://www.jetbrains.com/phpstorm/).

## En route vers le premier objectif

Donc commençons par créer les tables nécessaires à notre projet.

book
* id (int 10) (autoincrement) (unique) (primary key)
* title (nvarchar 150) (not null)
* writer (nvarchar 150) (not null)
* description (nvarchar 255)

person
* id (int 10) (autoincrement) (unique) (primary key)
* firstname (nvarchar 150)
* lastname (nvarchar 150)
* email (nvarchar 150) (not null)

lend
* id (int 10) (autoincrement) (unique) (primary key)
* book_id (int 10) (foreign key book)
* person_id (int 10) (foreign key person)
* start_date (int 10) (not null)
* end_date (int 10) (not null)
* return_date (int 10)

Maintenant que cela est fait, on peut remplir quelques lignes via PhpMyAdmin et on va essayer de les afficher via PHP.

Créons un fichier à la racine du projet, que l'on va appeller `index.php` dans lequel nous allons écrire les lignes suivantes


```php
<?php

$conn = new PDO('mysql:host=localhost;dbname=pratiquesnumeriques', 'patricnumeric', 'superpassword');
$statement = $conn->query('SELECT * FROM person');
$persons = $statement->fetchAll(PDO::FETCH_ASSOC);

print_r($persons);
```

Fantastique, on voit quelque chose.

Par contre, on n'est pas des bêtes, maintenant que l'on sait que l'on arrive à dialoguer avec notre base de données, 
on va encapsuler correctement nos personnes dans une belle classe.

Dans un répertoire `classes/Model/` ajoutons une classe `Person` avec les différents attributs que l'on a renseigné en 
DB ainsi qu'une méthode pour récupérer les informations en DB.

La classe `Person` ressemble alors à ceci

```php
<?php


class Person
{
    private $id;
    private $firstname;
    private $lastname;
    private $email;

    private function __construct(stdClass $object)
    {
        $this->id = $object->id;
        $this->firstname = $object->firstname;
        $this->lastname = $object->lastname;
        $this->email = $object->email;
    }

    public function __get($name)
    {
        if(property_exists(self::class, $name)) {
            return $this->$name;
        } else {
            throw new Exception("There is no property $name in the class " . self::class);
        }
    }

    public function __set($name, $value)
    {
        if(property_exists(self::class, $name)) {
            $this->$name = $value;
        } else {
            throw new Exception("There is no property $name in the class " . self::class);
        }
    }

    public static function get_all() {
        $conn = new PDO('mysql:host=localhost;dbname=pratiquesnumeriques', 'patricnumeric', 'superpassword');
        $statement = $conn->query('SELECT * FROM person');
        $persons = $statement->fetchAll(PDO::FETCH_ASSOC);

        $tab = [];
        foreach($persons as $person) {
            $p = (object) $person;
            $tab[$p->id] = new Person($person);
        }
        return $tab;
    }

}
```

Mais encore une fois ce n'est pas top car la connexion est répétée à chaque fois.

On va donc créer un utilitaire de requête SQL.

Cela peut être vu comme quelque chose de compliqué, mais en véritié il n'y a rien de sorcier et cette abstraction va 
nous permettre de simplifier énormément nos futures requêtes SQL.

Ainsi notre classe DB ressemble à ceci 

```php
<?php
// /classes/Helper/DB.php

class DB
{
    private $host;
    private $user;
    private $password;
    private $database;

    private $connexion;

    private static $instance;

    private function __construct()
    {
        $this->host = 'localhost';
        $this->user = 'patricnumeric';
        $this->password = 'superpassword';
        $this->database = 'pratiquesnumeriques';

        $this->connexion = new PDO("mysql:host={$this->host};dbname={$this->database}", $this->user, $this->password);
    }

    public static function get_instance() {
        if(!isset(self::$instance)) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public static function select(string $table, array $attributes = [], string $fields = '*') {
        $sql = "SELECT {$fields} FROM {$table} WHERE 1=1";
        foreach($attributes as $key => $value) {
            $sql .= " AND {$key} = :{$key}";
        }
        return self::select_sql($sql, $attributes);

    }

    public static function select_sql($sql, array $attributes = []) {
        $cursor_attributes = [];
        foreach($attributes as $key => $value) {
            $cursor_attributes[':' . $key] = $value;
        }

        $query = self::get_instance()->connexion->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $query->execute($cursor_attributes);
        $values = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();

        $tab = [];
        foreach($values as $key => $value) {
            $tab[$key] = (object)$value;
        }

        return $tab;
    }

}
```

Maintenant que nous avons écrit le backend-nécessaire pour récupérer en base de données les différentes personne, on va
se concentrer sur l'affichage.

Pour ce faire on va créer une nouvelle page qui ne va faire qu'afficher les personnes que l'on a enregistré en DB

```php
<?php
// /person/list.php

require_once __DIR__ . '/../classes/Model/Person.php';

$persons = Person::get_all();

?>

<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
```

Maintenant on veut avoir la logique pour ajouter une personne
Pour ça on va commencer par créer un formulaire

```html
<form action="./add.php" method="post">
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
```

Puis on va créer, sur la même page `person/add.php`, la gestion de ce formulaire

```php
<?php
// /person/add.php

require_once __DIR__ . '/../classes/Model/Person.php';
$params = $_POST;
$dialogue = '';

if(count($params) > 0) {
    $id = Person::create($params);

    if($id !== false) {
        $person = Person::get($id);
        $dialogue = '<div class="alert alert-success"><strong>Yeaah!</strong>La personne ' . $person->firstname . ' ' . $person->lastname . ' a été correctement enregistrée.</div>';
    } else {
        $dialogue = '<div class="alert alert-danger"><strong>Erreur :(</strong>Une erreur est survenue lors de l\'enregistrement</div>';
    }
}
```

Ce bout de code va demander la création de la méthode statique `create` dans la classe `Person`

```php
public static function create(array $parameters) {
    foreach(['firstname', 'lastname', 'email'] as $key) {
        if(!in_array($key, array_keys($parameters))) {
            throw new Exception("User cannot be created : $key missing");
        }
    }

    return DB::insert(self::$table, $parameters);
}
```

Qui va nous demander d'implémenter une méthode statique `insert` dans la classe `DB`.

```php
public static function insert(string $table, array $attributes) {
    $cursor_attributes = [];
    foreach($attributes as $key => $value) {
        $cursor_attributes[':' . $key] = $value;
    }

    $sql = "INSERT INTO {$table} (" . implode(', ', array_keys($attributes)) . ") VALUES (" . implode(', ', array_keys($cursor_attributes)) . ')';
    $query = self::get_instance()->connexion->prepare($sql);
    if($query->execute($attributes)) {
        return (int)self::get_instance()->connexion->lastInsertId();
    } else {
        return false;
    }
}
```

Et voila, la création de personne fonctionne maintenant.
On peut retourner sur la page qui les liste pour vérifier que les personnes se sont bien ajoutées.

Les deux prochaines actions qu'il reste à implémenter sont la modification et la suppression.
Nous allons ajouter un lien vers ces deux actions dans la table qui affiche les personnes.

Donc il nous faut rajouter une colonne 'Action' :
```html
<th scope="col">Action</th>
```

et dans la méthode `output_as_table_line` de la classe `Person`, on va ajouter un champ composé de deux liens 
(vers des pagers qui n'existent pas encore mais qui sont, je pense, assez explicite)

```php
$link_delete = '<a href="delete.php?id=' . $this->id . '" role="button" class="btn"><i class="far fa-trash-alt text-danger"></i></a>';
$link_update = '<a href="update.php?id=' . $this->id . '" role="button" class="btn"><i class="fas fa-pencil-alt"></i></a>';
$output .= "<td>{$link_update}{$link_delete}</td>";
```
> Attention ici les liens doivent être pensés comme s'ils étaient interprétés depuis le fichier dans lequel il vont être output.

> Ici nous avons utilisé un paramètre GET pour passer l'information (l'id) directement dans l'url. 

Donc pour le moment si on clique sur un de ces liens, le serveur apache va nous renvoyer une "belle" 404 car les fichiers 
n'ont tout simplement pas encore été créés.

Nous pouvons tout de suite gérer la mise à jour. Nous allons réutiliser le formulaire de création pour lequel nous allons 
préremplir les champs en fonction des valeurs de la personne qui correspond à l'id passsé en paramètre de l'url.

Une fonction `update` va être créée dans la classe `Person`
```php
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
```

et la fonction `update` de la classe `DB` va aussi être créée
```php
public static function update(string $table, array $attributes, array $where) {
    if(count($attributes) === 0) {
        throw new Exception("Nothing to update");
    }

    $update = [];
    foreach($attributes as $key => $value) {
        $update[] = "$key = :{$key}";
    }
    $update_sql = implode(', ', $update);

    $where_sql = '';
    foreach($where as $key => $value) {
        $where_sql .= " AND {$key} = :{$key}";
    }

    $params = $attributes + $where;
    $sql = "UPDATE {$table} SET {$update_sql} WHERE 1=1 $where_sql";

    $query = self::get_instance()->connexion->prepare($sql);
    return $query->execute($params);
}
```

Une fois que cela est fait, il suffit d'implémenter la suppression.

```php
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
```

qui crée une fonction `delete` dans la classe `Person`

```php
public function delete() {
    return DB::delete(self::$table, ['id' => $this->id]);
}
```

et la fonction `delete` de la classe `DB`

```php
public static function delete(string $table, $where)  {
    $where_sql = '';
    foreach($where as $key => $value) {
        $where_sql .= " AND {$key} = :{$key}";
    }
    $sql = "DELETE from {$table} WHERE 1=1 $where_sql";

    $query = self::get_instance()->connexion->prepare($sql);
    return $query->execute($where);
}
```

Et voila, le crud pour les personnes est maintenant terminé


Le vrai avantage d'avoir séparé les différentes actions sur différents fichiers (différentes classes) va se faire sentir
maintenant que l'on veut créer la classe et les vues se rapportant au concept de livre.

En effet toutes les fonctions qui gèrent les accès en base de données sont déjà écrites et bien que l'on ait seulement 1/3 
de nos concepts qui soient fonctionnels, on a déjà fait beaucoup plus que la moitié du travail :D

