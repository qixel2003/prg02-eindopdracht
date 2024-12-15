<?php
/** @var mysqli $db */
session_start();
require_once 'includes/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$name = $classId = $primaryWeaponId = $secondaryWeaponId = '';

// Handle form submission
if (isset($_POST['submit'])) {
    // Sanitize inputs
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $classId = mysqli_real_escape_string($db, $_POST['class_id']);
    $primaryWeaponId = mysqli_real_escape_string($db, $_POST['primary_weapon_id']);
    $secondaryWeaponId = mysqli_real_escape_string($db, $_POST['secondary_weapon_id']);
    $userId = $_SESSION['user']['id']; // Retrieve logged-in user's ID

    // Validation
    if ($name === "") {
        $errors['name'] = "Choose your character's name.";
    }
    if ($classId === "") {
        $errors['class_id'] = "Choose your character's class.";
    }
    if ($primaryWeaponId === "") {
        $errors['primary_weapon_id'] = "Choose your primary weapon.";
    }
    if ($secondaryWeaponId === "") {
        $errors['secondary_weapon_id'] = "Choose your secondary weapon.";
    }

    // If no errors, insert data
    if (empty($errors)) {
        $insertQuery = "
        INSERT INTO characters (name, class_id, primary_weapon_id, secondary_weapon_id, user_id) 
        VALUES ('$name', '$classId', '$primaryWeaponId', '$secondaryWeaponId', '$userId')";

        if (mysqli_query($db, $insertQuery)) {
            header('Location: index.php');
            exit;
        } else {
            echo "Error: " . mysqli_error($db);
        }
    }
}

// Fetch dropdown options
$classQuery = "SELECT id, name FROM classes";
$classes = mysqli_query($db, $classQuery);

$weaponQuery = "SELECT id, name FROM weapons";
$weapons = mysqli_query($db, $weaponQuery);

$secondaryWeaponQuery = "SELECT id, name FROM secondary_weapons";
$secondaryWeapons = mysqli_query($db, $secondaryWeaponQuery);

mysqli_close($db);
?>
<!doctype html>
<html lang="en" class="has-background-black-ter">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <title>Characters - Create</title>
</head>
<body>
<div class="container px-4">
    <section class="columns is-centered">
        <div class="column is-10">
            <h2 class="title mt-4 has-text-primary">Add Character</h2>

            <form class="column is-6" action="" method="post">
                <!-- Name -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label has-text-primary" for="name">Name</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input class="input" id="name" type="text" name="name" value="<?= htmlentities($name) ?>"/>
                            </div>
                            <p class="help is-danger"><?= $errors['name'] ?? '' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Class -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label has-text-primary" for="class_id">Class</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="class_id" name="class_id">
                                        <option value="">-- Select a Class --</option>
                                        <?php while ($class = mysqli_fetch_assoc($classes)): ?>
                                            <option value="<?= $class['id'] ?>" <?= $classId == $class['id'] ? 'selected' : '' ?>>
                                                <?= htmlentities($class['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <p class="help is-danger"><?= $errors['class_id'] ?? '' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Primary Weapon -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label has-text-primary" for="primary_weapon_id">Primary Weapon</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="primary_weapon_id" name="primary_weapon_id">
                                        <option value="">-- Select a Primary Weapon --</option>
                                        <?php while ($weapon = mysqli_fetch_assoc($weapons)): ?>
                                            <option value="<?= $weapon['id'] ?>" <?= $primaryWeaponId == $weapon['id'] ? 'selected' : '' ?>>
                                                <?= htmlentities($weapon['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <p class="help is-danger"><?= $errors['primary_weapon_id'] ?? '' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Secondary Weapon -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label has-text-primary" for="secondary_weapon_id">Secondary Weapon</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="secondary_weapon_id" name="secondary_weapon_id">
                                        <option value="">-- Select a Secondary Weapon --</option>
                                        <?php while ($secondaryWeapon = mysqli_fetch_assoc($secondaryWeapons)): ?>
                                            <option value="<?= $secondaryWeapon['id'] ?>" <?= $secondaryWeaponId == $secondaryWeapon['id'] ? 'selected' : '' ?>>
                                                <?= htmlentities($secondaryWeapon['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <p class="help is-danger"><?= $errors['secondary_weapon_id'] ?? '' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal"></div>
                    <div class="field-body">
                        <button class="button is-link is-fullwidth" type="submit" name="submit">Save</button>
                    </div>
                </div>
            </form>

            <a class="button mt-4 has-text-primary has-background-dark" href="index.php">&laquo; Go back to the list</a>
        </div>
    </section>
</div>
</body>
</html>
