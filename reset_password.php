<?php
// reset_password.php
session_start();
include 'cfg/dbconnect.php';
require 'vendor/autoload.php'; // Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = false;
$email_err ="";
$email="";
if (isset($_POST['send'])) {
    $email = trim($_POST['email']);

    //checking validation
    if ($email == "") {
        $email_err = "Email is mandatory";
        $error = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid Email format";
        $error = true;
    }

    if(!$error){
        // Check if email exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $token = bin2hex(random_bytes(32)); // Generate a secure token
            $expires = date("U") + 1800; // Token expires in 30 minutes

            
            // Store the token in the database
            $sql = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sss", $email, $token, $expires);
                mysqli_stmt_execute($stmt);

                // Prepare the email template
                $template = file_get_contents('email_template.html');
                $reset_link = "http://localhost/ajax_crud/change_password.php?token=$token";
                $body = str_replace(
                    ['{{name}}', '{{reset_link}}'],
                    [$row['name'], $reset_link],
                    $template
                );

                // Send the reset email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // SMTP server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'oomaymyatnoe21@gmail.com';
                    $mail->Password = 'xuxjzhbzihpnpaly';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('oomaymyatnoe21@gmail.com', 'Product Industry');
                    $mail->addAddress($email);
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';
                    // HTML content with button
                    $mail->Body = $body;
                    $mail->send();
                    $_SESSION['success'] = 'A reset link has been sent to your email.';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Failed to send the email. Please try again later.';
                }
            }
        } else {
            $_SESSION['error'] = 'No account found with that email address.';
        }
    }

    header("Location: reset_password.php");
    exit();
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
    <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        ?>
    <div class="row justify-content-center">
    <div class="col-md-6">
    <div class="card shadow">
    <div class="card-header">
    <h3 class="mb-0">Reset Password</h3>
    </div>
    <div class="card-body">

        <form action="" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" name="email" id="email" class="form-control" placeholder="Enter your email"
                value="<?=$email?>"
                />
                <div class="input-err text-danger"><?= $email_err ?></div>
            </div>
            <button type="submit" name="send" class="btn btn-primary">Send</button>
        </form>
    </div>
    </div>
    </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>