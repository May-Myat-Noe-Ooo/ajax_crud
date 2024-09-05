<?php
// change_password.php
session_start();
include 'cfg/dbconnect.php';
$new_pwd = $confirm_pwd = "";
$pwd_err = $conf_pwd_err = "";
$error = false;
if (isset($_POST['reset'])) {
    $token = $_POST['token'];
    $new_pwd = $_POST['new_pwd'];
    $confirm_pwd = $_POST['confirm_pwd'];

    if ($new_pwd == "") {
        $pwd_err = "Password is mandatory";
        $error = true;
    }

    if ($confirm_pwd == "") {
        $conf_pwd_err = "Confirm Password is mandatory";
        $error = true;
    }

    if ($new_pwd != "" && $confirm_pwd != "") {
        echo $new_pwd != $confirm_pwd;
        if ($new_pwd != $confirm_pwd) {
            $conf_pwd_err = "Passwords do not match";
            $error = true;
        }
    }

    if (!$error) {


        $sql = "SELECT * FROM password_resets WHERE token = ? AND expires >= ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            $current_time = date("U");
            mysqli_stmt_bind_param($stmt, "ss", $token, $current_time);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $email = $row['email'];

                // Update the user's password
                $new_pwd_hashed = password_hash($new_pwd, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ? WHERE email = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ss", $new_pwd_hashed, $email);
                    mysqli_stmt_execute($stmt);

                    // Delete the token
                    $sql = "DELETE FROM password_resets WHERE email = ?";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $email);
                        mysqli_stmt_execute($stmt);
                    }

                    $_SESSION['success'] = "Password successfully reset. Please log in.";
                    header("Location: login.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Invalid or expired token.";
            }
        }


        header("Location: change_password.php?token=$token");
        exit();
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: reset_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h3 class="mb-0">Reset Password</h3>
                    </div>
                    <div class="card-body">
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

                        <form action="" method="post">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            <div class="mb-3 position-relative">
                                <label for="new_pwd" class="form-label">New Password</label>
                                <input type="password" name="new_pwd" id="new_pwd" class="form-control" placeholder="Enter new password"
                                    value="<?= $new_pwd ?>">
                                <i class="far fa-eye" id="togglePassword" style="position: absolute; right: 10px; top: 65%; transform: translateY(-50%); cursor: pointer;"></i>
                                <div class="input-err text-danger" style="height:10px;"><?= $pwd_err ?></div>
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="confirm_pwd" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_pwd" id="confirm_pwd" class="form-control" placeholder="Confirm new password"
                                    value="<?= $confirm_pwd ?>">
                                <i class="far fa-eye" id="c_togglePassword" style="position: absolute; right: 10px; top: 73%; transform: translateY(-50%); cursor: pointer;"></i>
                                <div class="input-err text-danger" style="height:10px;"><?= $conf_pwd_err ?></div>
                            </div>
                            <button type="submit" name="reset" class="btn btn-primary">Confirm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const c_togglePassword = document.querySelector('#c_togglePassword');
        const password = document.querySelector('#new_pwd');
        const c_password = document.querySelector('#confirm_pwd');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
        c_togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = c_password.getAttribute('type') === 'password' ? 'text' : 'password';
            c_password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>