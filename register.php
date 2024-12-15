<?php
if (isset($_POST['submit'])) {
    /** @var mysqli $db */
    require_once "includes/database.php";
    $errors = array();
    // Get form data
    $email = mysqli_escape_string($db, $_POST['email']);
    $firstName = mysqli_escape_string($db, $_POST['first_name']);
    $lastName = mysqli_escape_string($db, $_POST['last_name']);
    $password = mysqli_escape_string($db, $_POST['password']);

    // Server-side validation
    if ($email == "") {
        $errors['email'] = "Please enter an email.";
    }
    if ($firstName == "") {
        $errors['first_name'] = "Please enter a firstname.";
    }
    if ($lastName == "") {
        $errors['last_name'] = "Please enter a lastname.";
    }
    if ($password == "") {
        $errors['password'] = "Please enter a password.";
    }
    // If data valid
    // Check if the email is already in use
    $checkQuery = "SELECT * FROM users WHERE email = '$email'";
    $checkResult = mysqli_query($db, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Email is already in use
        $errors['email'] = "This email is already registered.";
    } elseif (empty($errors)) {
        // create a secure password, with the PHP function password_hash()
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // store the new user in the database.

        $insertQuery = "INSERT INTO `users`(`id`, `email`, `password`, `first_name`, `last_name`) 
          VALUES ('','$email','$hashedPassword','$firstName','$lastName')";

        if (mysqli_query($db, $insertQuery)) {
            // Redirect to login page
            header('location: login.php');
            // Exit the code
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en" class="has-background-grey-dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <title>Registreren</title>
</head>
<body>

<section class="section">
    <div class="container content">
        <h2 class="title has-text-warning">Register With Email</h2>

        <section class="columns">
            <form class="column is-6" action="" method="post">

                <!-- First name -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label has-text-warning" for="firstName">First name</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input" id="first_name" type="text" name="first_name"
                                       value="<?= $firstName ?? '' ?>"/>
                                <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
                            </div>
                            <p class="help is-danger">
                                <?= $errors['first_name'] ?? '' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Last name -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label has-text-warning" for="last_name">Last name</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input" id="last_name" type="text" name="last_name"
                                       value="<?= $lastName ?? '' ?>"/>
                                <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
                            </div>
                            <p class="help is-danger">
                                <?= $errors['last_name'] ?? '' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label has-text-warning" for="email">Email</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input" id="email" type="text" name="email" value="<?= $email ?? '' ?>"/>
                                <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
                            </div>
                            <p class="help is-danger">
                                <?= $errors['email'] ?? '' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label has-text-warning" for="password">Password</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input" id="password" type="password" name="password"/>
                                <span class="icon is-small is-left"><i class="fas fa-lock"></i></span>
                            </div>
                            <p class="help is-danger">
                                <?= $errors['password'] ?? '' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="field is-horizontal">
                    <div class="field-label is-normal"></div>
                    <div class="field-body">
                        <button class="button is-link is-fullwidth" type="submit" name="submit">Register</button>
                    </div>
                </div>
                <div>
                    <a class="button has-text-warning has-background-dark" href="login.php">Login</a>
                </div>

            </form>
        </section>

    </div>
</section>
</body>
</html>

