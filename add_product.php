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
$is_logged_in = isset($_SESSION['name']);
// Start transaction
mysqli_begin_transaction($conn);
try {
// Check if product already exists
$sql = "SELECT * FROM product WHERE product_name='$product'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    throw new Exception("Product already exists.");
} 
    // Insert the new product with color and packaging details
    $sql = "INSERT INTO product (product_name, price, stock, color, packaging) VALUES ('$product', '$price', '$stock', '$color', '$packaging')";
    // Simulate error: trying to insert into a non-existent column 'invalid_column'
    // $sql = "INSERT INTO product (product_name, price, stock, color, packaging, invalid_column) 
    //         VALUES ('$product', '$price', '$stock', '$color', '$packaging', 'value')";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception("Error: product not added.");
    } 
    // Commit the transaction
    mysqli_commit($conn);
    $succMsg = "Product Added";
} catch(Exception $e) {
    // Rollback the transaction if any error occurs
    mysqli_rollback($conn);
    $errMsg = $e->getMessage();
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
            <?php if ($is_logged_in): // Show "Action" column only if logged in 
                                ?>
                                    <th>Action</th>
                                <?php endif; ?>
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
                <td><?php echo $color; ?></td>
                                    <td><?php echo implode(", ", $packaging); ?></td>
                                    <?php if ($is_logged_in): // Show "Action" buttons only if logged in 
                                    ?>
                                        <td>
                                            <a href="update_product.php?id=<?= $product_id ?>" class="fa fa-edit" title="Edit"></a>
                                            <a href="javascript:void(0)" title="Delete" class="fa fa-remove" onclick="delProduct('<?= $product_id ?>','<?= $product_name ?>')"></a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php
                            }
    } else { ?>
        <tr>
            <td colspan="6">No Products found.</td>
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