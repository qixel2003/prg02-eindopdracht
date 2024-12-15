<?php
/** @var mysqli $db */
session_start();
// Database connection
require_once 'includes/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Get the character ID from the URL
if (!isset($_GET['character_id']) || $_GET['character_id'] === '') {
    header('Location: index.php');
    exit;
}
$characterId = mysqli_real_escape_string($db, $_GET['character_id']);

// Fetch character details with related data from the database
$query = "
    SELECT 
        characters.name AS character_name,
        classes.name AS class_name,
        weapons.name AS primary_weapon,
        secondary_weapons.name AS secondary_weapon
    FROM characters
    JOIN classes ON characters.class_id = classes.id
    JOIN weapons ON characters.primary_weapon_id = weapons.id
    JOIN secondary_weapons ON characters.secondary_weapon_id = secondary_weapons.id
    WHERE characters.id = '$characterId'";

$result = mysqli_query($db, $query) or die('Error: ' . mysqli_error($db));

// Redirect if no results are found
if (mysqli_num_rows($result) != 1) {
    header('Location: index.php');
    exit;
}

// Convert the result to a PHP array
$character = mysqli_fetch_assoc($result);

// Close the database connection
mysqli_close($db);
?>
<!doctype html>
<html lang="en" class="has-background-black-ter">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <title class="has-text-primary">Details <?= htmlentities($character['character_name']) ?></title>
</head>
<body>
<div class="container px-4">
    <div class="columns is-centered">
        <div class="column is-narrow">
            <h2 class="title mt-4 has-text-primary"><?= htmlentities($character['character_name']) ?> details</h2>
            <section class="content">
                <ul>
                    <li class="has-text-primary">Class: <?= htmlentities($character['class_name']) ?></li>
                    <li class="has-text-primary">Primary weapon: <?= htmlentities($character['primary_weapon']) ?></li>
                    <li class="has-text-primary">Secondary weapon: <?= htmlentities($character['secondary_weapon']) ?></li>
                </ul>
            </section>
            <div>
                <a class="button has-text-primary has-background-dark" href="delete.php?character_id=<?= $characterId ?>">Delete</a>
            </div>
            <div>
                <a class="button has-text-primary has-background-dark" href="index.php">Go back to the list</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
