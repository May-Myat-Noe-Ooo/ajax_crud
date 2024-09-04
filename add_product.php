<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    // User is not logged in, redirect to login page with a message
    $_SESSION['error'] = "Please login to access this route.";
    header("Location: login.php");
    exit(); // Ensure no further code is executed
}

include 'cfg/dbconnect.php';

// Retrieve form data
$product = $_POST['p_name'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$color = $_POST['color']; // New color field
$packaging = isset($_POST['packaging']) ? implode(",", $_POST['packaging']) : ""; // New packaging field

$succMsg = $errMsg = "";

// Check if product already exists
$sql = "SELECT * FROM product WHERE product_name='$product'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $errMsg = "Product already exists.";
} else {
    // Insert the new product with color and packaging details
    $sql = "INSERT INTO product (product_name, price, stock, color, packaging) VALUES ('$product', '$price', '$stock', '$color', '$packaging')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $succMsg = "Product Added";
    } else {
        $errMsg = "Error: product not added.";
    }
}

// Fetch and display updated product list
$sql = "SELECT * FROM product ORDER BY product_id";
$result = mysqli_query($conn, $sql);
?>

<table class="table table-bordered table-striped">
    <tr>
        <thead>
            <th>Serial No.</th>
            <th>Product Id</th>
            <th>Product Name</th>
            <th>Price (<span class="fa fa-inr"></span>)</th>
            <th>Stock</th>
            <th>Color</th> <!-- New column for color -->
            <th>Packaging</th> <!-- New column for packaging -->
            <th>Action</th>
        </thead>
    </tr>
    <?php
    if (mysqli_num_rows($result) > 0) {
        $counter = 0;
        foreach ($result as $row) {
            $counter++;
            $product_id = $row['product_id'];
            $product_name = $row['product_name'];
            $color = $row['color'];
            $packaging = explode(",", $row['packaging']); // Assuming 'packaging' is stored as a comma-separated string
    ?>
            <tr>
                <td><?= $counter; ?></td>
                <td><?= $row['product_id']; ?></td>
                <td><?= $row['product_name']; ?></td>
                <td><?= $row['price']; ?></td>
                <td><?= $row['stock']; ?></td>
                <td><?= $row['color']; ?></td> <!-- Display color -->
                <td><?= $row['packaging']; ?></td> <!-- Display packaging -->
                <td>
                    <a href="index.php?id=<?= $product_id ?>&flag=edit" class="fa fa-edit" title="Edit"></a>
                    <a href="javascript:void(0)" title="Delete" class="fa fa-remove" onclick="delProduct('<?= $product_id ?>','<?= $product_name ?>')"></a>
                </td>
            </tr>
    <?php
        }
    } else { ?>
        <tr>
            <td colspan="8">No Products found.</td>
        </tr>
    <?php } ?>
</table>

<script>
    $(document).ready(function() {
        <?php if (!empty($succMsg)) { ?>
            $("#showMsg").html("<div class='alert alert-success alert-dismissible fade show' role='alert'><?= $succMsg; ?></div>");
            // Automatically hide alert after 5 seconds
            setTimeout(function() {
                $("#showMsg .alert").alert('close');
            }, 5000); // 5000 milliseconds = 5 seconds
        <?php } ?>
        <?php if (!empty($errMsg)) { ?>
            $("#showMsg").html("<div class='alert alert-danger alert-dismissible fade show' role='alert'><?= $errMsg; ?></div>");
            // Automatically hide alert after 5 seconds
            setTimeout(function() {
                $("#showMsg .alert").alert('close');
            }, 5000); // 5000 milliseconds = 5 seconds
        <?php } ?>
    });
</script>
