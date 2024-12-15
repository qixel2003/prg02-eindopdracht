<?php
/** @var mysqli $db */
session_start();
$errors = [];

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Connect to the database
require_once 'includes/database.php';

// Retrieve the ID from the URL
if (!isset($_GET['character_id']) || $_GET['character_id'] === '') {
    header('Location: index.php');
    exit;
}
$id = mysqli_real_escape_string($db, $_GET['character_id']);

// Fetch the character data from the database
$query = "
    SELECT 
        characters.id,
        characters.name,
        characters.class_id,
        characters.primary_weapon_id,
        characters.secondary_weapon_id
    FROM characters
    WHERE characters.id = '$id'";
$result = mysqli_query($db, $query) or die('Error: ' . mysqli_error($db));

// Redirect if no character is found
if (mysqli_num_rows($result) != 1) {
    header('Location: index.php');
    exit;
}

// Convert the result to a PHP array
$character = mysqli_fetch_assoc($result);

// Fetch dropdown options
$classQuery = "SELECT id, name FROM classes";
$classes = mysqli_query($db, $classQuery);

$weaponQuery = "SELECT id, name FROM weapons";
$weapons = mysqli_query($db, $weaponQuery);

$secondaryWeaponQuery = "SELECT id, name FROM secondary_weapons";
$secondaryWeapons = mysqli_query($db, $secondaryWeaponQuery);

// Check if the form has been submitted
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $classId = mysqli_real_escape_string($db, $_POST['class_id']);
    $primaryWeaponId = mysqli_real_escape_string($db, $_POST['primary_weapon_id']);
    $secondaryWeaponId = mysqli_real_escape_string($db, $_POST['secondary_weapon_id']);

    // Server-side validation
    if ($name === "") {
        $errors['name'] = "Please enter the character's name.";
    }
    if ($classId === "") {
        $errors['class_id'] = "Please choose a class.";
    }
    if ($primaryWeaponId === "") {
        $errors['primary_weapon_id'] = "Please choose a primary weapon.";
    }
    if ($secondaryWeaponId === "") {
        $errors['secondary_weapon_id'] = "Please choose a secondary weapon.";
    }

    // If no errors, update the database
    if (empty($errors)) {
        // Update query
        $updateQuery = "
            UPDATE `characters` 
            SET 
                `name` = '$name', 
                `class_id` = '$classId', 
                `primary_weapon_id` = '$primaryWeaponId', 
                `secondary_weapon_id` = '$secondaryWeaponId'
            WHERE `id` = '$id'";

        if (mysqli_query($db, $updateQuery)) {
            header('Location: index.php');
            exit;
        } else {
            echo "Error: " . mysqli_error($db);
        }
    }
}
?>
<!doctype html>
<html lang="en" class="has-background-black-ter">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <title>Edit Character</title>
</head>
<body>
<div class="container px-4 ">
    <h1 class="title mt-4 has-text-primary">Edit Character</h1>
    <section class="columns">
        <form class="column is-6" action="" method="post">
            <div class="field">
                <label class="label has-text-primary" for="name">Name</label>
                <input class="input" id="name" type="text" name="name" value="<?= htmlentities($character['name']) ?>" />
                <p class="help is-danger"><?= $errors['name'] ?? '' ?></p>
            </div>

            <!-- Class Dropdown -->
            <div class="field">
                <label class="label has-text-primary" for="class_id">Class</label>
                <div class="select is-fullwidth">
                    <select id="class_id" name="class_id">
                        <option value="">-- Select a Class --</option>
                        <?php while ($class = mysqli_fetch_assoc($classes)): ?>
                            <option value="<?= $class['id'] ?>" <?= $character['class_id'] == $class['id'] ? 'selected' : '' ?>>
                                <?= htmlentities($class['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <p class="help is-danger"><?= $errors['class_id'] ?? '' ?></p>
            </div>

            <!-- Primary Weapon Dropdown -->
            <div class="field">
                <label class="label has-text-primary" for="primary_weapon_id">Primary Weapon</label>
                <div class="select is-fullwidth">
                    <select id="primary_weapon_id" name="primary_weapon_id">
                        <option value="">-- Select a Primary Weapon --</option>
                        <?php while ($weapon = mysqli_fetch_assoc($weapons)): ?>
                            <option value="<?= $weapon['id'] ?>" <?= $character['primary_weapon_id'] == $weapon['id'] ? 'selected' : '' ?>>
                                <?= htmlentities($weapon['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <p class="help is-danger"><?= $errors['primary_weapon_id'] ?? '' ?></p>
            </div>

            <!-- Secondary Weapon Dropdown -->
            <div class="field">
                <label class="label has-text-primary" for="secondary_weapon_id">Secondary Weapon</label>
                <div class="select is-fullwidth">
                    <select id="secondary_weapon_id" name="secondary_weapon_id">
                        <option value="">-- Select a Secondary Weapon --</option>
                        <?php while ($secondaryWeapon = mysqli_fetch_assoc($secondaryWeapons)): ?>
                            <option value="<?= $secondaryWeapon['id'] ?>" <?= $character['secondary_weapon_id'] == $secondaryWeapon['id'] ? 'selected' : '' ?>>
                                <?= htmlentities($secondaryWeapon['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <p class="help is-danger"><?= $errors['secondary_weapon_id'] ?? '' ?></p>
            </div>

            <button class="button is-link" type="submit" name="submit">Save</button>
        </form>
    </section>
    <a class="button mt-4 has-text-primary has-background-dark" href="index.php">Go back to the list</a>
</div>
</body>
</html>
