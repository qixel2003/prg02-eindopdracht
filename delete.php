<?php
/** @var mysqli $db */

session_start();

// Include the database connection file
require_once 'includes/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Get the logged-in user's ID
$userId = $_SESSION['user']['id'];

// Check if the character ID is passed in the GET request
if (isset($_GET['character_id']) && $_GET['character_id'] !== '') {
    // Sanitize the character ID
    $characterId = mysqli_real_escape_string($db, $_GET['character_id']);

    // Verify that the character belongs to the logged-in user
    $verifyQuery = "SELECT id FROM characters WHERE id = '$characterId' AND user_id = '$userId'";
    $result = mysqli_query($db, $verifyQuery);

    if (mysqli_num_rows($result) === 1) {
        // If the character belongs to the user, proceed with deletion
        $deleteQuery = "DELETE FROM characters WHERE id = '$characterId'";

        if (mysqli_query($db, $deleteQuery)) {
            // Redirect to the index page after successful deletion
            header('Location: index.php');
            exit;
        } else {
            // Display an error message if the deletion fails
            echo "Error: " . mysqli_error($db);
        }
    } else {
        // If the character does not belong to the user, deny deletion
        echo "You are not authorized to delete this character.";
    }
} else {
    // Redirect to the index page if character ID is not provided
    header('Location: index.php');
    exit;
}
