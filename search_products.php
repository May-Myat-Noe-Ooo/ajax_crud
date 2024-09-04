<?php
include 'cfg/dbconnect.php';

$name = isset($_GET['name']) ? $_GET['name'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';

// Sanitize inputs
$name = mysqli_real_escape_string($conn, $name);
$price = mysqli_real_escape_string($conn, $price);

// Build query
$sql = "SELECT * FROM product WHERE 1=1";

if (!empty($name)) {
    $sql .= " AND product_name LIKE '%$name%'";
}

if (!empty($price)) {
    $sql .= " AND price = '$price'";
}

$sql .= " ORDER BY product_id";

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
    ?>
            <tr>
                <td><?= $counter; ?></td>
                <td><?= $row['product_id']; ?></td>
                <td><?= $row['product_name']; ?></td>
                <td><?= $row['price']; ?></td>
                <td><?= $row['stock']; ?></td>
                <td>
                    <a href="index.php?id=<?= $product_id ?>&flag=edit" class="fa fa-edit" title="Edit"></a>
                    <a href="javascript:void(0)" title="Delete" class="fa fa-remove" onclick="delProduct('<?= $product_id ?>','<?= $product_name ?>')"></a>
                </td>
            </tr>
        <?php
        }
    } else { ?>
        <tr>
            <td colspan="6">No Products found.</td>
        </tr>
    <?php } ?>



</table>