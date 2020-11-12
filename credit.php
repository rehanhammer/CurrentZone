<?php
//credit.php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');


?>

	<span id="alert_action"></span>
	<div class="row">
		<div class="col-lg-12">
			
			<div class="panel panel-default">
                <div class="panel-heading">
                	<div class="row">
                    	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title">Account Receivable / Account Payable</h3>
                        </div>
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
						<select id = "cus_name" name = "cus_name" class="form-control">
							<option value = ''>Select Customer</option>
							<?php echo fill_customer_list($connect);?>
						</select>
                        </div>
                    </div>
                </div>
                <div class="panel-body hide" id = "showtable">
                	<table id="credit_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>AR/AP ID</th>
                                <th>Order ID</th>
								<th>Customer Name</th>
								<!-- <th>Product Name</th> -->
                                <th>Description</th>
								<th>Total Balance Of Order</th>
								<th>Cash Received</th>
								<th>Total Balance Receivable</th>
								<th>AR/AP Date</th>
								<?php
								if($_SESSION['type'] == 'master')
								{
									echo '<th>Enter By</th>';
								}
								?>
								<th>AR/AP Enter Date</th>
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>

	<div id="creditdetailsModal" class="modal fade">
        <div class="modal-dialog">
        	<form method="post" id="credit_detail_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i> Credit Details</h4>
                    </div>
                    <div class="modal-body">
                        <Div id="credit_details"></Div>
                    </div>
                    <div class="modal-footer">     
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
		
<script type="text/javascript">
    $(document).ready(function(){

    	// var orderdataTable = $('#credit_data').DataTable({
		// 	"processing":true,
		// 	"serverSide":true,
		// 	"order":[],
		// 	"ajax":{
		// 		url:"credit_fetch.php",
		// 		type:"POST"
		// 	},
		// 	"pageLength": 10
		// });
    });

	$('#cus_name').change(function(){

		var value = $(this).val();
		$('#showtable').removeClass('hide');
		$('#showtable').show();
		
		var orderdataTable = $('#credit_data').DataTable({
			dom: 'Blfrtip',
        	buttons: [
            	'excelHtml5',
            	'pdfHtml5'
        	],
			"processing":true,
			"serverSide":true,
			"bDestroy": true,
			"order":[],
			"ajax":{
				url:"credit_fetch.php",
				type:"POST",
				"data" : {
            	"customerName" : value,
				}
			},
			"pageLength": 10
		});

	});
</script>

<?php
include('footer.php');
?>