<?php

require_once "db.php";


// ======================================
// INSERT FEEDBACK
// ======================================

if (
    $db_connected &&
    $_SERVER["REQUEST_METHOD"] === "POST"
) {

    $name = trim($_POST["name"] ?? "");

    $email = trim($_POST["email"] ?? "");

    $rating = intval($_POST["rating"] ?? 0);

    $message = trim($_POST["message"] ?? "");


    // Validation

    if ($name === "") {

        die("Name is required.");

    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        die("Invalid email address.");

    }


    if ($rating < 1 || $rating > 5) {

        die("Please select a valid rating.");

    }


    if ($message === "") {

        die("Feedback message is required.");

    }


    // Insert data

    $sql = "INSERT INTO feedback
            (name, email, rating, message)
            VALUES (?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);


    if ($stmt === false) {

        die("Unable to process feedback.");

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

        /*
         * Redirect to index.php.
         * This prevents duplicate submission
         * when the page is refreshed.
         */

        header("Location: index.php?submitted=1");

        exit();

    } else {

        die("Feedback could not be submitted.");

    }

}


// ======================================
// GET FEEDBACK DETAILS
// ======================================

$result = null;

if ($db_connected) {

    $sql = "SELECT
                id,
                name,
                email,
                rating,
                message,
                created_at
            FROM feedback
            ORDER BY id DESC";

    $result = $conn->query($sql);

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Feedback System</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>


<div class="container">


    <!-- ======================================
         HEADER
    ======================================= -->

    <div class="header">

        <h1>Feedback System</h1>

        <p>
            We value your feedback.
            Share your experience with us.
        </p>


        <?php if ($db_connected): ?>

            <div class="database-status connected">
                Database Connected
            </div>

        <?php else: ?>

            <div class="database-status not-connected">
                Database Not Connected
            </div>

        <?php endif; ?>


    </div>



    <!-- ======================================
         SUCCESS MESSAGE
    ======================================= -->

    <?php if (isset($_GET["submitted"])): ?>

        <div class="success-message">

            Feedback submitted successfully.

        </div>

    <?php endif; ?>



    <!-- ======================================
         SUBMIT FEEDBACK
    ======================================= -->

    <div class="card">

        <h2>Submit Your Feedback</h2>


        <?php if ($db_connected): ?>

            <form
                action="index.php"
                method="POST"
            >


                <div class="form-group">

                    <label for="name">
                        Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your name"
                        required
                    >

                </div>



                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>



                <div class="form-group">

                    <label for="rating">
                        Rating
                    </label>

                    <select
                        id="rating"
                        name="rating"
                        required
                    >

                        <option value="">
                            Select Rating
                        </option>

                        <option value="5">
                            5 - Excellent
                        </option>

                        <option value="4">
                            4 - Very Good
                        </option>

                        <option value="3">
                            3 - Good
                        </option>

                        <option value="2">
                            2 - Average
                        </option>

                        <option value="1">
                            1 - Poor
                        </option>

                    </select>

                </div>



                <div class="form-group">

                    <label for="message">
                        Your Feedback
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        rows="5"
                        placeholder="Write your feedback..."
                        required
                    ></textarea>

                </div>



                <button type="submit">
                    Submit Feedback
                </button>


            </form>


        <?php else: ?>

            <p class="error-message">
                Database is not connected.
            </p>

        <?php endif; ?>


    </div>



    <!-- ======================================
         FEEDBACK DETAILS
    ======================================= -->

    <div class="card">

        <h2>Customer Feedback</h2>


        <?php if ($result && $result->num_rows > 0): ?>


            <?php while ($row = $result->fetch_assoc()): ?>


                <div class="feedback">


                    <div class="feedback-header">


                        <div>

                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $row["name"]
                                );

                                ?>

                            </h3>


                            <p class="email">

                                <?php

                                echo htmlspecialchars(
                                    $row["email"]
                                );

                                ?>

                            </p>

                        </div>



                        <div class="rating">

                            Rating:

                            <?php

                            echo htmlspecialchars(
                                $row["rating"]
                            );

                            ?>/5

                        </div>


                    </div>



                    <div class="feedback-message">

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $row["message"]
                            )
                        );

                        ?>

                    </div>



                    <div class="submitted-date">

                        Submitted:

                        <?php

                        echo htmlspecialchars(
                            $row["created_at"]
                        );

                        ?>

                    </div>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <div class="empty">

                No feedback available yet.

            </div>


        <?php endif; ?>


    </div>


</div>


<?php

if ($db_connected) {

    $conn->close();

}

?>


</body>

</html>