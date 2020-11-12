<?php
//product.php

include('database_connection.php');
include('function.php');

if(!isset($_SESSION["type"]))
{
    header('location:login.php');
}

if($_SESSION['type'] != 'master')
{
    header('location:index.php');
}

include('header.php');


?>

    <link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>

        <span id='alert_action'></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
                    <div class="panel-heading">
                    	<div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="panel-title">Inventory Detail</h3>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body hide">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="product_data" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Total Weight</th>
                                    <th>Available Weight</th>
                                    <th>Sold Weight</th>
                                    <th>Enter By</th>
                                    <th>Status</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr></thead>
                            </table>
                        </div></div>
                    </div>
                </div>
			</div>
		</div>
        <div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Pieces Purchase</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_weight_purchase($connect); ?></h1>
			</div>
		</div>
	</div>
    <div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Pieces InStock</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_weight_instock($connect); ?></h1>
			</div>
		</div>
	</div>
    <div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Pieces Sales</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_weight_sold($connect); ?></h1>
			</div>
		</div>
	</div>

<script>
$(document).ready(function(){
    var productdataTable = $('#product_data').DataTable({
        "processing":true,
        "serverSide":true,
        "order":[],
        "ajax":{
            url:"product_fetch.php",
            type:"POST"
        },
        "columnDefs":[
            {
                "targets":[5, 6, 7],
                "orderable":false,
            },
        ],
        "pageLength": 10
    });
});
</script>
<?php
include('footer.php');
?>