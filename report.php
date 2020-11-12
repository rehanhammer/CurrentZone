<?php
//index.php
include('database_connection.php');
include('function.php');

if(!isset($_SESSION["type"]))
{
	header("location:login.php");
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
                            	<h3 class="panel-title">Monthly Reports</h3>
                        	</div>
                        
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
								<select id = "month_name" name = "month_name" class="form-control">
									<option value = ''>Select Month</option>
									<?php echo fill_month_list($connect);?>
								</select>
                        	</div>
                    	</div>
               	 	</div>
				</div>
			</div>
		</div>

		<div class = "row hide" id = "reports_info">

			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Completed Order</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "total_completed_order"></h1>
					</div>
				</div>
			</div>
		
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Pending Order</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "total_pending_order"></h1>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Stock In</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "total_weight_in"></h1>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Stock Out / Sale</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "total_weight_out"></h1>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Order Value Sale(Price)</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "sale_price_total"></h1>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Order Value (Base Price)</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "base_price_total"></h1>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Order Cash Received</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "order_cash_received"></h1>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Credit / Cash Receivable</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "order_cash_receivable"></h1>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Profit Without Expense</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "without_expense"></h1>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Total Profit With Expense</strong></div>
					<div class="panel-body" align="center">
						<h1 id = "with_expense"></h1>
					</div>
				</div>
			</div>

		</div>


	<script> type="text/javascript"
		$('#month_name').change(function(){

			$('#total_completed_order').html('');
			$('#total_pending_order').html('');
			$('#total_weight_in').html('');
			$('#total_weight_out').html('');

			$('#sale_price_total').html('');
			$('#base_price_total').html('');
			$('#order_cash_received').html('');

			$('#order_cash_receivable').html('');
			$('#without_expense').html('');
			$('#with_expense').html('');

			var value = $(this).val();
			$('#reports_info').removeClass('hide');
			$('#reports_info').show();

			//completed orders
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalcompletedorder"
				},
				success:function(data){
					$('#total_completed_order').append(data);
				}
			});


			//pending orders
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalpendingorder"
				},
				success:function(data){
					$('#total_pending_order').append(data);
				}
			});

			//weight in
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalweightin"
				},
				success:function(data){
					$('#total_weight_in').append(data);
				}
			});

			//weight out
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalweightout"
				},
				success:function(data){
					$('#total_weight_out').append(data);
				}
			});

			//sale price
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalsaleprice"
				},
				success:function(data){
					$('#sale_price_total').append(data);
				}
			});

			//base price
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalbaseprice"
				},
				success:function(data){
					$('#base_price_total').append(data);
				}
			});

			//order cash received
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalcashreceived"
				},
				success:function(data){
					$('#order_cash_received').append(data);
				}
			});

			//order cash receivable
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalcashreceivable"
				},
				success:function(data){
					$('#order_cash_receivable').append(data);
				}
			});

			//total profit without expense
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalprofit"
				},
				success:function(data){
					$('#without_expense').append(data);
				}
			});

			//total profit with expense
			$.ajax({
				url:"report_action.php",
				method:"POST",
				data:{
					"monthName":value, btn_action: "fetchtotalprofitwithexpense"
				},
				success:function(data){
					$('#with_expense').append(data);
				}
			});

		});

	</script>

<?php
include('footer.php');
?>
