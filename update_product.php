<!DOCTYPE html>
<html lang="en">

<head>
    <title>Update Product</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['name'])) {
        $_SESSION['error'] = "Please login to access this route.";
        header("Location: login.php");
        exit();
    }
    include 'cfg/dbconnect.php';

    $p_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $p_name = $price = $stock = "";
    $errMsg = $succMsg = "";

    if ($p_id) {
        $sql = "SELECT * FROM product WHERE product_id='$p_id'";
        $result = mysqli_query($conn, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            $p_name = $row['product_name'];
            $price = $row['price'];
            $stock = $row['stock'];
            $color = $row['color']; // Add this line
            $packaging = explode(',', $row['packaging']); // Add this line
        } else {
            $errMsg = "Product not found.";
        }
    }
    ?>

    <div class="container">
        <h2>Update Product</h2>
        <div id="showMsg">
            <?php if (!empty($succMsg)) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $succMsg; ?>
                </div>
            <?php } ?>
            <?php if (!empty($errMsg)) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $errMsg; ?>
                </div>
            <?php } ?>
        </div>
        <form id="updateForm">
            <input type="hidden" name="p_id" value="<?= $p_id ?>">

            <!-- Product Name -->
            <div class="form-group col-md-12 mt-2">
                <label for="p_name">Product Name</label>
                <input type="text" name="p_name" id="p_name" class="form-control" value="<?= $p_name ?>">
                <small><div id="productErr" class="input-err text-danger"></div></small>
            </div>

            <!-- Price -->
            <div class="form-group col-md-12 mt-2">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" min="1" max="99999" class="form-control" value="<?= $price ?>">
                <small><div id="priceErr" class="input-err text-danger"></div></small>
            </div>

            <!-- Stock -->
            <div class="form-group col-md-12 mt-2">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" min="0" max="99999" class="form-control" value="<?= $stock ?>">
                <small><div id="stockErr" class="input-err text-danger"></div></small>
            </div>

            <!-- Color Selection -->
            <div class="form-group col-md-12 mb-2">
                <label>Choose Color</label><br>
                <input type="radio" name="color" id="color_red" value="Red" <?= $color == 'Red' ? 'checked' : '' ?>> Red<br>
                <input type="radio" name="color" id="color_blue" value="Blue" <?= $color == 'Blue' ? 'checked' : '' ?>> Blue<br>
                <input type="radio" name="color" id="color_green" value="Green" <?= $color == 'Green' ? 'checked' : '' ?>> Green<br>
                <small><div class="input-err text-danger" id="colorErr"></div></small>
            </div>

            <!-- Packaging Options -->
            <div class="form-group col-md-12 mb-2">
                <label>Choose Packaging Options</label><br>
                <input type="checkbox" name="packaging[]" id="hard_packaging" value="Hard Packaging" <?= in_array('Hard Packaging', $packaging) ? 'checked' : '' ?>> Hard Packaging<br>
                <input type="checkbox" name="packaging[]" id="soft_packaging" value="Soft Packaging" <?= in_array('Soft Packaging', $packaging) ? 'checked' : '' ?>> Soft Packaging<br>
                <input type="checkbox" name="packaging[]" id="release_brochure" value="Release Brochure" <?= in_array('Release Brochure', $packaging) ? 'checked' : '' ?>> Release Brochure<br>
                <input type="checkbox" name="packaging[]" id="sender_name" value="Sender Name" <?= in_array('Sender Name', $packaging) ? 'checked' : '' ?>> Sender Name<br>
                <small><div class="input-err text-danger" id="packagingErr"></div></small>
            </div>

            <input type="button" class="btn btn-primary mt-2" value="Update" onclick="updateProduct()">
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="js/crud.js"></script>
    <script>
        function updateProduct() {
            var p_name = $("#p_name").val();
            var price = $("#price").val();
            var stock = $("#stock").val();
            var color = $("input[name='color']:checked").val();
            var packaging = [];
            $("input[name='packaging[]']:checked").each(function() {
                packaging.push($(this).val());
            });

            if (!validateForm(p_name, price, stock)) {
                return false;
            }

            $.ajax({
                url: "update_product_action.php",
                method: "post",
                data: {
                    p_id: <?= $p_id ?>,
                    p_name: p_name,
                    price: price,
                    stock: stock,
                    color: color,
                    packaging: packaging
                },
                dataType: "text",
                success: function(response) {
                    $("#showMsg").html(response);
                    setTimeout(function() {
        $('#updateForm')[0].reset();  // Clear form after UI update
        console.log("Form cleared after product update.");
    }, 100);  // Adding a slight delay to ensure the UI updates first
                }
            });
        }
    </script>
</body>

</html>
