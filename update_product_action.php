<?php
session_start();
if (!isset($_SESSION['name'])) {
    echo "<div class='alert alert-danger'>Please login to continue.</div>";
    exit();
}

include 'cfg/dbconnect.php';

$p_id = $_POST['p_id'];
$p_name = $_POST['p_name'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$color = $_POST['color'];
$packaging = implode(',', $_POST['packaging']);

$sql = "SELECT * FROM product WHERE product_name='$p_name' AND product_id <> '$p_id'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // $_SESSION['error'] = "Product already exists.";
    // header("Location: update_product.php?id=$p_id");
    // exit();
    echo "<div class='alert alert-danger'>Product already exists.</div>";
} else {
    $sql = "UPDATE product SET 
            product_name='$p_name', 
            price='$price', 
            stock='$stock',
            color='$color', 
            packaging='$packaging' 
            WHERE product_id='$p_id'";

    if (mysqli_query($conn, $sql)) {
        echo "<div id='successMessage' class='alert alert-success'>Product Updated Successfully!</div>";
    } else {
        echo "<div id='errorMessage' class='alert alert-danger'>Error updating product.</div>";
    }

}

mysqli_close($conn);
?>
<script>
    // Hide the error message after 5 seconds (5000 ms)
    setTimeout(function () {
        var errorMessage = document.getElementById('errorMessage');
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
    }, 5000); // 5000 ms = 5 seconds

    // Hide the success message after 5 seconds (5000 ms)
    setTimeout(function () {
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 5000);
</script>

