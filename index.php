<?php
/** @var mysqli $db */
session_start();
// Setup connection with database
require_once 'includes/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Get user data from the SESSION
$user = $_SESSION['user'];
$firstName = $user['first_name'];
$lastName = $user['last_name'];

// Select all characters with related data
$query = "
    SELECT 
        characters.id,
        characters.name,
        classes.name AS class_name,
        weapons.name AS primary_weapon_name,
        secondary_weapons.name AS secondary_weapon_name
    FROM characters
    JOIN classes ON characters.class_id = classes.id
    JOIN weapons ON characters.primary_weapon_id = weapons.id
    JOIN secondary_weapons ON characters.secondary_weapon_id = secondary_weapons.id
";

$result = mysqli_query($db, $query) or die('Error: ' . mysqli_error($db) . ' with query ' . $query);

// Store the characters in an array
$characters = [];
while ($row = mysqli_fetch_assoc($result)) {
    $characters[] = $row;
}

// Close the connection
mysqli_close($db);
?>
<!doctype html>
<html lang="en" class="has-background-black-ter">
<head>
    <meta charset="UTF-8">
    <title>Character List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body>
<div class="container px-4 has-background-black-ter">
    <h1 class="title mt-4 has-text-primary">Character List</h1>
    <h2 class="title mt-4 has-text-primary">Welcome <?= "$firstName $lastName" ?></h2>
    <a href="logout.php" class="button has-text-primary has-background-dark m-1 ">Log out</a>

    <hr>
    <div class="columns is-centered">
        <div class="column is-narrow">

            <table class="table is-striped">
                <thead>
                <a href="create.php" class="button has-text-primary has-background-dark m-1">Create</a>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Primary Weapon</th>
                    <th>Secondary Weapon</th>
                    <th>Details</th>
                    <th>Edit</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="7" class="has-text-centered has-background-black-ter has-text-primary">Character Database</td>
                </tr>
                </tfoot>
                <tbody>
                <?php foreach ($characters as $index => $character) { ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlentities($character['name']) ?></td>
                        <td><?= htmlentities($character['class_name']) ?></td>
                        <td><?= htmlentities($character['primary_weapon_name']) ?></td>
                        <td><?= htmlentities($character['secondary_weapon_name']) ?></td>
                        <td><a href="details.php?character_id=<?= $character['id'] ?>">Details</a></td>
                        <td><a href="edit.php?character_id=<?= $character['id'] ?>">Edit</a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
