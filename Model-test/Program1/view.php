<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$rating = intval($_POST["rating"] ?? 0);
$message = trim($_POST["message"] ?? "");

if ($name === "") {
    die("Name is required.");
}

if ($email === "") {
    die("Email is required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

if ($rating < 1 || $rating > 5) {
    die("Please select a rating.");
}

if ($message === "") {
    die("Feedback message is required.");
}

$sql = "INSERT INTO feedback
        (name, email, rating, message)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Database query failed.");
}

$stmt->bind_param(
    "ssis",
    $name,
    $email,
    $rating,
    $message
);

if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    header("Location: index.php");
    exit();

} else {

    $stmt->close();
    $conn->close();

    die("Feedback submission failed.");
}

?>