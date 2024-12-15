<?php
/** @var mysqli $db */

// required when working with sessions
session_start();
require_once 'includes/database.php';

$login = false;
// Is user logged in?

if (isset($_POST['submit'])) {
    $errors = array();

    // Get form data
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // Server-side validation
    if ($email === ""){
        $errors['email'] = "Enter an email";
    }
    if ($password === ""){
        $errors['loginFailed'] = "Enter a password";
    }

    // If data valid
// If data valid
    if (empty($errors)) {
        // SELECT the user from the database, based on the email address.
        $loginQuery = "SELECT * FROM users where email = '$email'";
        $result = mysqli_query($db, $loginQuery) or die('error: ' . mysqli_error($db));

        // check if the user exists
        if (mysqli_num_rows($result) != 1){
            header('Location: register.php');
            exit;
        }

        // Get user data from result
        $user = mysqli_fetch_assoc($result);

        // Check if the provided password matches the stored password in the database
        if (password_verify($password, $user['password'])){
            // Password is correct

            // Store the user in the session
            $_SESSION['user'] = $user; // Assuming user details are stored in session

            // Redirect to secure page
            header('Location: index.php');
            exit;
        } else {
            // Password is incorrect

            //error incorrect log in
            $errors['loginFailed'] = "Incorrect login credentials";
        }
    }

// User doesn't exist

//error incorrect log in
    $errors['loginFailed'] = "User does not exist or incorrect login credentials";
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
    <title>Log in</title>
</head>
<body>
<section class="section">
    <div class="container content">
        <h2 class="title has-text-warning">Log in</h2>

        <?php if ($login) { ?>
            <p><a href="logout.php">Uitloggen</a> / <a href="index.php">Naar home</a></p>
        <?php } else { ?>

            <section class="columns">
                <form class="column is-6" action="" method="post">

                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label has-text-warning" for="email">Email</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input" id="email" type="text" name="email" value="<?= $email ?? '' ?>" />
                                    <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
                                </div>
                                <p class="help is-danger">
                                    <?= $errors['email'] ?? '' ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label has-text-warning" for="password">Password</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input" id="password" type="password" name="password"/>
                                    <span class="icon is-small is-left"><i class="fas fa-lock"></i></span>

                                    <?php if(isset($errors['loginFailed'])) { ?>
                                        <div class="notification is-danger">
                                            <button class="delete"></button>
                                            <?=$errors['loginFailed']?>
                                        </div>
                                    <?php } ?>

                                </div>
                                <p class="help is-danger">
                                    <?= $errors['password'] ?? '' ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="field is-horizontal">
                        <div class="field-label is-normal"></div>
                        <div class="field-body">
                            <button class="button is-link is-fullwidth" type="submit" name="submit">Log in With Email</button>
                        </div>
                    </div>
                    <div>
                        <a class="button has-text-warning has-background-dark" href="register.php">Register</a>
                    </div>
                </form>

            </section>

        <?php } ?>

    </div>
</section>
</body>
</html>


