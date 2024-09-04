<?php
include 'cfg/dbconnect.php';
include "topmenu.php";
$name = $email = $pwd = $conf_pwd = "";
$name_err = $email_err = $pwd_err = $conf_pwd_err = "";
$error = false;
$succ_msg=$err_msg = "";

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pwd = trim($_POST['pwd']);
    $conf_pwd = trim($_POST['conf_pwd']);
    // validate input fields
    if ($name == "") {
        $name_err = "Name is mandatory";
        $error = true;
    }

    if ($email == "") {
        $email_err = "Email is mandatory";
        $error = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid Email format";
        $error = true;
    } else {   // check if email already registered
        // Prepare the SQL statement
        $sql = "SELECT * FROM users WHERE email = ?";

        // Initialize a statement and prepare the SQL query
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // Bind parameters (s = string)
            mysqli_stmt_bind_param($stmt, "s", $email);

            // Execute the statement
            mysqli_stmt_execute($stmt);

            // Get the result
            $result = mysqli_stmt_get_result($stmt);

            // Check if any rows returned
            if (mysqli_num_rows($result) > 0) {
                $email_err = "Email already registered";
                $error = true;
            }
            // Close the statement
            // mysqli_stmt_close($stmt);
        }
    }

    if ($pwd == "") {
        $pwd_err = "Passqword is mandatory";
        $error = true;
    }

    if ($conf_pwd == "") {
        $conf_pwd_err = "Confirm Password is mandatory";
        $error = true;
    }

    if ($pwd !="" && $conf_pwd !=""){
        if ($pwd != $conf_pwd){
            $conf_pwd_err = "Passwords do not match";
            $error = true;
        }
    }

    // All validations passed
if (!$error) {
    $pwd = password_hash($pwd, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";

    // Initialize the statement
    if ($stmt = mysqli_prepare($conn, $sql)) {

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $pwd);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $succ_msg = "Registration successful. Please <a href='login.php'>login</a>";
            $name = $email = "";
        } else {
            $error_msg = "Error: Could not execute the query.";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Error: Could not prepare the SQL statement.";
    }
}

}

?>
<h1>Registration</h1>
<div class="container">
<div class="err-msg">
          <?php if (!empty($succ_msg)){ ?>
              <div class="alert alert-success">
                  <?= $succ_msg?>
              </div>
          <?php } ?>
  
          <?php if (!empty($error_msg)){ ?>
              <div class="alert alert-danger">
                  <?= $error_msg?>
              </div>
          <?php } ?>
  
      </div>
    <form action="" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input
                type="text"
                class="form-control"
                name="name"
                id="name"
                placeholder="Enter Name" 
                value="<?=$name?>"
                />
            <div class="input-err text-danger"><?= $name_err ?></div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="text"
                class="form-control"
                name="email"
                id="email"
                placeholder="Enter email" 
                value="<?=$email?>"
                />
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
        <div class="mb-3">
            <label for="conf_pwd" class="form-label">Confirm Password</label>
            <input
                type="password"
                class="form-control"
                name="conf_pwd"
                id="conf_pwd"
                placeholder="Enter Confirm password" />
            <div class="input-err text-danger"><?= $conf_pwd_err ?></div>
        </div>
        <div class="form-check">
            <input
                class="form-check-input"
                name=""
                id=""
                type="checkbox"
                value="checkedValue"
                aria-label="Show Password"
                onclick="showPwd()" />Show Password
        </div>
        <div class="reg-button text-center mt-3">
            <button
                type="submit"
                name="submit"
                class="btn btn-primary">
                Register
            </button>
        </div>
        <p>Already Registered? Login <a href="login.php">here</a></p>
    </form>
</div>
<script src="js/common.js"></script>
</body>

</html>