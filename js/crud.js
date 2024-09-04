$("#frm :input").change(function(){
    $("#frm").data("changed",true);
})



function validateForm(p_name,price,stock){
    $("#productErr").html("");
    $("#priceErr").html("");
    $("#stockErr").html("");
    $("#colorErr").html("");
    $("#packagingErr").html("");
    var valid=true;
    if(p_name==""){
        $("#productErr").html("Enter Product Name")
        valid=false;
    }
    if(price=="")
        price=0;
    if (price<=0){
        $("#priceErr").html("Price should be positive.")
        valid=false;
    }
    if (stock=="" || stock<0){
        $("#stockErr").html("Stock should be zero or positive.")
        valid=false;
    }
    // Validate color selection (radio button)
    if (!$("input[name='color']:checked").val()) {
        $("#colorErr").html("Please choose a color.");
        valid = false;
    }

    // Validate packaging options (checkboxes)
    if ($("input[name='packaging[]']:checked").length == 0) {
        $("#packagingErr").html("Please choose one option.");
        valid = false;
    }
    return valid;
}


function addProduct() {
    var p_name = $("#p_name").val();
    var price = $("#price").val();
    var stock = $("#stock").val();

    // Get selected color
    var color = $("input[name='color']:checked").val();

    // Get selected packaging options (checkboxes)
    var packaging = [];
    $("input[name='packaging[]']:checked").each(function() {
        packaging.push($(this).val());
    });

    // Validate form including the new fields
    if (!validateForm(p_name, price, stock)) {
        return false;
    }
    // console.log(packaging);
    // return;
    
    // Write Ajax to add product
    $.ajax({
        url: "add_product.php",
        method: "post",
        data: {
            p_name: p_name,
            price: price,
            stock: stock,
            color: color,
            packaging: packaging
        },
        dataType: "text",
        success: function(response) {
            $("#products").html(response);
        }
    });
}





function delProduct(p_id,p_name){
 if(confirm("Are you sure you want to delete product"+p_name+"?")){
    $.ajax({
        url:"delete_product.php",
        method:"post",
        data:{p_id:p_id},
        dataType:"text",
        success:function(response){
            $("#products").html(response);
        }
    });
 }
}

function searchProducts() {
    var searchName = $("#searchName").val().trim();
    var searchPrice = $("#searchPrice").val().trim();

    $.ajax({
        url: "search_products.php",
        method: "GET",
        data: {
            name: searchName,
            price: searchPrice
        },
        dataType: "text",
        success: function(response) {
            $("#products").html(response);
        },
        error: function() {
            alert("An error occurred while searching.");
        }
    });
}



