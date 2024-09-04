<?php
session_start();
include 'cfg/dbconnect.php';
include "topmenu.php";
$email = $pwd = "";
$email_err = $pwd_err = "";
$error = false;
$err_msg = "";

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $pwd = trim($_POST['pwd']);

    if (isset($_POST['remember']))
        $remember = $_POST['remember'];

    // validate input fields
    if ($email == "") {
        $email_err = "Email is mandatory";
        $error = true;
    }  
    

    if ($pwd == "") {
        $pwd_err = "Passqword is mandatory";
        $error = true;
    }


    // All validations passed
if (!$error) {

    $sql = "SELECT * FROM users WHERE email = ?";

    // Prepare the statement
    if ($stmt = mysqli_prepare($conn, $sql)) {

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "s", $email);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {

            // Get the result
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $stored_pwd = $row['password'];

                // Verify password
                if (password_verify($pwd, $stored_pwd)) {
                    // login successful
                    if (isset($_POST['remember'])){
                      // set cookies for email and checkbox
                        setcookie("remember_email", $email, time()+365*24*3600);
                        setcookie("remember", $remember, time()+365*24*3600);
                    }
                    else{
                        // delete cookies for email and checkbox
                        setcookie("remember_email", $email, time() - 365*24*3600);
                        setcookie("remember", $remember, time() - 365*24*3600);
                    }   
                    $_SESSION['name'] = $row['name'];
                    header("location:index.php");
                } else {
                    $error_msg = "Incorrect Password";
                }
            } else {
                $error_msg = "Email id not registered";
            }
        } else {
            $error_msg = "Error executing the query.";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Error preparing the SQL statement.";
    }
}

}

?>
<h1>Login</h1>
<div class="container">
    <div class="err-msg">
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

        <?php if (!empty($error_msg)) { ?>
            <div class="alert alert-danger">
                <?= $error_msg ?>
            </div>
        <?php } ?>

    </div>
    <form action="" method="post">
    <?php
             $display_email = isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : $email;

             $checked = !empty($remember) ? "checked" : (isset($_COOKIE['remember']) ? "checked" : "");
            ?>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="text"
                class="form-control"
                name="email"
                id="email"
                placeholder="Enter email"
                value="<?= $display_email ?>" />
            <div class="input-err text-danger"><?= $email_err ?></div>
        </div>
        <div class="mb-3">
            <label for="pwd" class="form-label">Password</label>
            <input
                type="password"
                class="form-control"
                name="pwd"
                id="pwd"
                placeholder="Enter password" />
            <div class="input-err text-danger"><?= $pwd_err ?></div>
        </div>
        <div class="form-check">
            <input
                class="form-check-input"
                name="remember"
                id="remember"
                type="checkbox"
                value="checkedValue"
                aria-label="Remember Me"
                <?= $checked?>
                 />Remember Me
                 <a href="reset_password.php" class="ms-3">Forgot Password?</a>
        </div>
        <div class="reg-button mt-3">
            <button
                type="submit"
                name="submit"
                class="btn btn-primary col-md-3">
                Login
            </button>
        </div>
        <p>Not Registered? Register <a href="register.php">here</a></p>
    </form>
</div>
</body>

</html>