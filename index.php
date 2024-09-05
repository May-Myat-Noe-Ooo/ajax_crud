<!DOCTYPE html>
<html lang="en">

<head>
    <title>Products</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    session_start(); // Start the session

    include "topmenu.php"; // Include the top menu

    // Check if the user is logged in
    $is_logged_in = isset($_SESSION['name']); // This will be true if the user is logged in

    $p_name = $price = $stock = $flag = $p_id = "";
    include 'cfg/dbconnect.php'; // Include your database connection

    // Display success or error messages if available
    $msg = isset($_GET['msg']) ? $_GET['msg'] : '';

    $sql = "select * from product order by product_id";
    $result = mysqli_query($conn, $sql);
    ?>
    <div class="container">
        <h2>Add, Update and Delete Product using Ajax (Ajax CRUD testing stashing)</h2>
        <div id="showMsg">
            <?php if (isset($_SESSION['success'])) {
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            {$_SESSION['success']}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
                unset($_SESSION['success']); // Clear the success message after displaying
            }

            if (isset($_SESSION['error'])) {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            {$_SESSION['error']}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
                unset($_SESSION['error']); // Clear the error message after displaying
            } ?>

        </div>
        <!-- Search Form -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="searchName" class="form-control" placeholder="Search by Product Name">
            </div>
            <div class="col-md-4">
                <input type="number" id="searchPrice" class="form-control" placeholder="Search by Price">
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary" onclick="searchProducts()">Search</button>
            </div>
        </div>
        <!-- Wrap the table and form in a single row -->
        <div class="row">
            <!-- Table Section -->
            <div class="col-md-7">
                <h4>Product List</h4>
                <div class="table-responsive" id="products">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <thead>
                                <th>Serial No.</th>
                                <th>Product Id</th>
                                <th>Product Name</th>
                                <th>Price (<span class="fa fa-inr"></span>)</th>
                                <th>Stock</th>
                                <th>Color</th>
                                <th>Packaging Options</th>
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
                </div>
            </div>
            <!-- Form Section -->
            <?php if ($is_logged_in): // Show the "Add Product" form only if logged in 
            ?>
                <div class="col-md-5">
                    <h4 class="d-flex justify-content-center">Add Product</h4>
                    <form id="frm">
                        <div class="form-group col-md-12 mb-2">
                            <label for="">Product Name</label>
                            <input type="text" name="p_name" id="p_name" class="form-control">
                            <small>
                                <div id="productErr" class="input-err text-danger"></div>
                            </small>
                        </div>
                        <div class="form-group col-md-12 mb-2">
                            <label for="">Price</label>
                            <input type="number" name="price" id="price" min="1" max="99999" class="form-control">
                            <small>
                                <div id="priceErr" class="input-err text-danger"></div>
                            </small>
                        </div>
                        <div class="form-group col-md-12 mb-2">
                            <label for="">Stock</label>
                            <input type="number" name="stock" id="stock" min="0" max="99999" class="form-control">
                            <small>
                                <div id="stockErr" class="input-err text-danger"></div>
                            </small>
                        </div>
                        <!-- New Color Selection Field -->
                        <div class="form-group col-md-12 mb-2">
                            <label>Choose Color</label><br>
                            <input type="radio" name="color" id="color_red" value="Red"> Red<br>
                            <input type="radio" name="color" id="color_blue" value="Blue"> Blue<br>
                            <input type="radio" name="color" id="color_green" value="Green"> Green<br>
                            <small>
                                <div class="input-err text-danger" id="colorErr"></div>
                            </small>
                        </div>

                        <!-- New Packaging Options Field -->
                        <div class="form-group col-md-12 mb-2">
                            <label>Choose Packaging Options</label><br>
                            <input type="checkbox" name="packaging[]" id="hard_packaging" value="Hard Packaging"> Hard Packaging<br>
                            <input type="checkbox" name="packaging[]" id="soft_packaging" value="Soft Packaging"> Soft Packaging<br>
                            <input type="checkbox" name="packaging[]" id="release_brochure" value="Release Brochure"> Release Brochure<br>
                            <input type="checkbox" name="packaging[]" id="sender_name" value="Sender Name"> Sender Name<br>
                            <small>
                                <div class="input-err text-danger" id="packagingErr"></div>
                            </small>
                        </div>
                        <input type="button" class="btn btn-primary mt-2" value="Add"
                            onclick="addProduct()">
                    </form>

                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="js/crud.js"></script>
    <script>
        // $(document).ready(function() {
        //     // Automatically hide alert after 5 seconds if there is a message
        //     setTimeout(function() {
        //         $("#showMsg .alert").alert('close');
        //     }, 5000); // 5000 milliseconds = 5 seconds
        // });
    </script>
</body>

</html>