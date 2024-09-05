<?php 
if (!isset($_SESSION) || session_id() == "" || session_status() === PHP_SESSION_NONE)
session_start() ;
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <link rel="stylesheet" href="/css/style.css" type="text/css">
   
</head>

<body>
    <div class="topmenu">
    <div class="menubar">
                <a href="index.php" class="<?= ($current_page=='index.php')?'active':'' ?>">Home</a>
                <?php if (isset($_SESSION['name'])){ ?>
                    <div class="user">
                        <span>Welcome <?= $_SESSION['name']?> </span>
                        <a href="logout.php">Logout</a>
                    </div>
                  
               <?php } else { ?>
                <a href="register.php" class="<?= ($current_page == 'register.php') ? 'active' : '' ?>">Register</a>
                <a href="login.php" class="<?= ($current_page=='login.php')? 'active': '' ?>">Login</a>
                <?php } ?>
            </div>
    </div>
</body>

</html>