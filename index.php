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
	<br />
	<div class="row">
	<?php
	if($_SESSION['type'] == 'master')
	{
	?>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total User</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_user($connect); ?></h1>
			</div>
		</div>
	</div>

	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Item in Stock</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_product($connect); ?></h1>
			</div>
		</div>
	</div>

	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Completed Order</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_order($connect); ?></h1>
			</div>
		</div>
	</div>
	
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Pending Order</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_pending_order($connect); ?></h1>
			</div>
		</div>
	</div>
	<!-- <div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Weight Purchase</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_weight_purchase($connect); ?> Kg</h1>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Weight Sold</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_weight_sold($connect); ?> Kg</h1>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Weight In Stock</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_weight_sold($connect); ?> Kg</h1>
			</div>
		</div>
	</div> -->
	<?php
	}
	?>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Order Value(Sale Price)</strong></div>
				<div class="panel-body" align="center">
					<h1><?php echo count_total_order_value($connect); ?></h1>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Order Value(Base Price)</strong></div>
				<div class="panel-body" align="center">
					<h1><?php echo count_total_actual_order_value($connect); ?></h1>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Cash Received</strong></div>
				<div class="panel-body" align="center">
					<h1><?php echo count_total_cash_order_value($connect); ?></h1>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Credit / Cash Receivable</strong></div>
				<div class="panel-body" align="center">
					<h1><?php echo count_total_credit_order_value($connect); ?></h1>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Profit Without Expense</strong></div>
				<div class="panel-body" align="center">
					<h1><?php echo count_total_profit_without_expense($connect); ?></h1>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Profit With Expense</strong></div>
				<div class="panel-body" align="center">
					<h1><?php echo count_total_profit_with_expense($connect); ?></h1>
				</div>
			</div>
		</div>
		<hr />
		<?php
		if($_SESSION['type'] == 'master')
		{
		?>
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Order Value User wise</strong></div>
				<div class="panel-body" align="center">
					<?php echo get_user_wise_total_order($connect); ?>
				</div>
			</div>
		</div>
		<?php
		}
		?>
	</div>

<?php
include("footer.php");
?>