<?php
include 'cfg/dbconnect.php';
$p_id=$_POST['p_id'];
$succMsg=$errMsg="";

//delete appropriate row in products
$sql="delete from product where product_id='$p_id'";
$result=mysqli_query($conn,$sql);
if($result)
$succMsg="Product Deleted";
else
$errMsg="Error:product not deleted.";

$sql="select * from product order by product_id";
$result=mysqli_query($conn,$sql);
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
                if(mysqli_num_rows($result)>0){
                    $counter=0;
                    foreach($result as $row){
                        $counter++;
                        $product_id=$row['product_id'];
                        $product_name=$row['product_name'];
                        ?>
                        <tr>
                            <td><?= $counter;?></td>
                            <td><?= $row['product_id'];?></td>
                            <td><?= $row['product_name'];?></td>
                            <td><?= $row['price'];?></td>
                            <td><?= $row['stock'];?></td>
                            <td>
                            <a href="index.php?id=<?=$product_id?>&flag=edit"  class="fa fa-edit" title="Edit"></a>
                            <a href="javascript:void(0)" title="Delete" class="fa fa-remove" onclick="delProduct('<?=$product_id?>','<?=$product_name?>')"></a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                else { ?>
                        <tr>
                            <td>No Products found.</td>
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