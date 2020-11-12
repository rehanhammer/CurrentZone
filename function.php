<?php
//function.php
error_reporting(0);

function get_user_name($connect, $user_id)
{
	$query = "
	SELECT user_name FROM user_details WHERE user_id = '".$user_id."'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['user_name'];
	}
}

function fill_product_list($connect)
{
	$query = "
	SELECT * FROM product 
	WHERE product_status = '1'
	ORDER BY product_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["product_id"].'">'.$row["product_name"].'</option>';
	}
	return $output;
}

function fill_customer_list($connect){
	$query = "
		SELECT * FROM `tbl_ar_ap` GROUP BY ar_ap_customer_name";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["ar_ap_customer_name"].'">'.$row["ar_ap_customer_name"].'</option>';
	}
	return $output;
}

function fetch_product_details($product_id, $connect)
{
	$query = "
	SELECT * FROM product 
	WHERE product_id = '".$product_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['product_name'] = $row["product_name"];
		$output['weight'] = $row["product_total_weight"];
		$output['price'] = $row['product_cost_per_kg'];
		$output['weight_remaining'] = $row['product_weight_remaining'];
		$output['weight_sold'] = $row['product_weight_sold'];
	}
	return $output;
}

function available_product_quantity($connect, $product_id)
{
	$product_data = fetch_product_details($product_id, $connect);
	$datetime = getDateTime();
	
	$query = "
	SELECT 	inventory_order_product.quantity FROM inventory_order_product 
	INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
	WHERE inventory_order_product.product_id = '".$product_id."' AND
	inventory_order.inventory_order_status = '1' OR inventory_order.inventory_order_status = '2'

	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$total = 0;
	foreach($result as $row)
	{
		$total = $total + $row['quantity'];
	}
	$available_quantity = intval($product_data['weight']) - intval($total);
	if($available_quantity == 0 || $available_quantity < 0)
	{
		$update_query = "
		UPDATE product SET 
		product_status = '2',
		product_udt = '".$datetime."'
		WHERE product_id = '".$product_id."'
		";
		$statement = $connect->prepare($update_query);
		$statement->execute();
	}
	if($total == 0)
		$available_quantity = 0;
	return $available_quantity;
}

function count_total_user($connect)
{
	$query = "
	SELECT * FROM user_details WHERE user_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_order($connect){
	$query = "
	SELECT * FROM inventory_order WHERE inventory_order_status='1'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_pending_order($connect){
	$query = "
	SELECT * FROM inventory_order WHERE inventory_order_status='2'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_weight_purchase($connect){
	
	$total_weight_purchase = 0;

	$query = "
	SELECT product_name, SUM(product_total_weight) as weight_purchase FROM product GROUP BY product_name";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '
	<div class="table-responsive">
		<table class="table table-bordered table-striped" style = "font-size: 14px;">
			<tr>
				<th>Product Name</th>
				<th>Pieces</th>
			</tr>
	';
	foreach($result as $row)
	{
		$output .= '
		<tr>
			<td>'.$row['product_name'].'</td>
			<td>'.$row["weight_purchase"].'</td>
		</tr>
		';

		$total_weight_purchase = $total_weight_purchase + $row["weight_purchase"];
	}
	$output .= '
	<tr>
		<td><b>Grand Total</b></td>
		<td><b> '.$total_weight_purchase.'</b></td>

	</tr></table></div>
	';

	return $output;
}

function count_total_weight_instock($connect){
	
	$total_weight_instock = 0;

	$query = "
	SELECT product_name, SUM(product_weight_remaining) as weight_remaining FROM product GROUP BY product_name";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '
	<div class="table-responsive">
		<table class="table table-bordered table-striped" style = "font-size: 14px;">
			<tr>
				<th>Product Name</th>
				<th>Pieces</th>
			</tr>
	';
	foreach($result as $row)
	{
		$output .= '
		<tr>
			<td>'.$row['product_name'].'</td>
			<td>'.number_format($row["weight_remaining"],2).'</td>
		</tr>
		';

		$total_weight_instock = $total_weight_instock + $row["weight_remaining"];
	}
	$output .= '
	<tr>
		<td><b>Grand Total</b></td>
		<td><b> '.number_format($total_weight_instock,2).'</b></td>

	</tr></table></div>
	';

	return $output;
}

function count_total_weight_sold($connect){

	$total_weight_sold = 0;

	$query = "
	SELECT product_name, SUM(product_weight_sold) as weight_sold FROM product GROUP BY product_name";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '
	<div class="table-responsive">
		<table class="table table-bordered table-striped" style = "font-size: 14px;">
			<tr>
				<th>Product Name</th>
				<th>Pieces</th>
			</tr>
	';
	foreach($result as $row)
	{
		$output .= '
		<tr>
			<td>'.$row['product_name'].'</td>
			<td>'.number_format($row["weight_sold"],2).'</td>
		</tr>
		';

		$total_weight_sold = $total_weight_sold + $row["weight_sold"];
	}
	$output .= '
	<tr>
		<td><b>Grand Total</b></td>
		<td><b> '.number_format($total_weight_sold,2).'</b></td>

	</tr></table></div>
	';

	return $output;

}

function count_total_product($connect)
{
	$query = "
	SELECT * FROM product WHERE product_status='1'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_order_value($connect)
{
	$query = "
	SELECT sum(inventory_order_total) as total_order_value FROM inventory_order 
	WHERE inventory_order_status='1' OR inventory_order_status = '2'
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function count_total_actual_order_value($connect)
{
	$query = "
	SELECT sum(inventory_order_actual_total) as total_actual_order_value FROM inventory_order 
	WHERE payment_status = 'cash' 
	AND inventory_order_status='1' OR inventory_order_status = '2'
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_actual_order_value'], 2);
	}
}

function count_total_cash_order_value($connect)
{
	$query = "
	SELECT sum(order_cash_received) as total_order_value FROM inventory_order 
	WHERE inventory_order_status ='1' OR inventory_order_status = '2'
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function count_total_credit_order_value($connect)
{
	$query = "
	SELECT sum(inventory_order_cash_receivable) as total_order_value FROM inventory_order WHERE payment_status = 'credit' AND inventory_order_status = '2'
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function count_total_profit_without_expense($connect)
{
	$query = "
	SELECT sum(inventory_order_total) as total_order_value,
	sum(inventory_order_actual_total) as total_actual_value,
	sum(inventory_order_product.product_profit) as totalprofit
	FROM inventory_order
	LEFT JOIN inventory_order_product ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
	Where inventory_order.inventory_order_status='1' OR inventory_order.inventory_order_status='2'
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$total = $row['totalprofit'];

		return number_format($total, 2);
	}
}

function count_total_profit_with_expense($connect){

	$sale_count = count_total_profit_without_expense($connect);
	$sale_count = preg_replace("/[^0-9\.]/", "", $sale_count);
	
	$query = "
	SELECT sum(expense_price) as total_expense
	FROM tbl_expense
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' Where user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();

	foreach($result as $row)
	{

		//if($sale_count > $row['total_expense'])
			$total = $sale_count - $row['total_expense'];
		//else
			//$total = $row['total_expense'] - $sale_count;

		return number_format($total, 2);
	}
}

function get_user_wise_total_order($connect)
{
	$query = '
	SELECT sum(inventory_order.inventory_order_total) as order_total,
	sum(inventory_order.inventory_order_actual_total) as order_actual_total,
	sum(inventory_order_product.product_profit) as total_profit,
	SUM(CASE WHEN inventory_order.payment_status = "cash" THEN inventory_order.inventory_order_total ELSE 0 END) AS cash_order_total, 
	user_details.user_name 
	FROM inventory_order 
	LEFT JOIN user_details ON user_details.user_id = inventory_order.user_id 
	LEFT JOIN inventory_order_product ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id 
	WHERE inventory_order.inventory_order_status = "1" OR inventory_order_status = "2" GROUP BY inventory_order.user_id
	';
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<tr>
				<th>User Name</th>
				<th>Sale Price Total</th>
				<th>Base Price Total</th>
				<th>Total Profit</th>

			</tr>
	';

	$total_order = 0;
	$total_profit = 0;
	$total_actual_order = 0;
	foreach($result as $row)
	{
		$profit = $row["order_total"] - $row["order_actual_total"];
		$output .= '
		<tr>
			<td>'.$row['user_name'].'</td>
			<td align="right"> '.$row["order_total"].'</td>
			<td align="right"> '.$row["order_actual_total"].'</td>
			<td align="right"> '.$row['total_profit'].'</td>

		</tr>
		';

		$total_order = $total_order + $row["order_total"];
		$total_actual_order = $total_actual_order + $row["order_actual_total"];
		$total_profit = $total_profit + $row['total_profit'];

	}
	$output .= '
	<tr>
		<td align="right"><b>Total</b></td>
		<td align="right"><b> '.$total_order.'</b></td>
		<td align="right"><b> '.$total_actual_order.'</b></td>
		<td align="right"><b> '.$total_profit.'</b></td>

	</tr></table></div>
	';
	return $output;
}

function getDateTime(){

	date_default_timezone_set('Asia/Karachi');
	$date = date('Y-m-d H:i:s');

	return $date;
}

function delete_inventory($connect, $inventory_id){
	$delete_query = " DELETE FROM inventory_order_product
		WHERE inventory_order_id = '".$inventory_id."'
	";
	$statement = $connect->prepare($delete_query);
	$statement->execute();
	$result = $statement->rowCount();
	return $result; 
}

function delete_sub_inventory($connect, $inventory_id){
	$delete_subquery = " DELETE FROM inventory_order
		WHERE inventory_order_id = '".$inventory_id."'
	";
	$subStatement = $connect->prepare($delete_subquery);
	$subStatement->execute();
	$subResult = $subStatement->rowCount();
	return $subResult; 
}

function update_product_weight($connect, $product_id, $weight, $sold_weight)
{
	$datetime = getDateTime();

	$update_query = "
	UPDATE product SET
	product_weight_remaining = '".$weight."',
	product_weight_sold = '".$sold_weight."',
	product_udt = '".$datetime."'
	WHERE product_id = '".$product_id."'
	";
	$statement = $connect->prepare($update_query);
	$statement->execute();
}

function update_product_status($connect, $product_id){
	$update_query = "
	UPDATE product SET
	product_status = '2',
	product_udt = '".$datetime."'
	WHERE product_id = '".$product_id."'
	";
	$statement = $connect->prepare($update_query);
	$statement->execute();
	$result = $statement->fetchAll();
	if(isset($result))
		return 2;				
}

function fetch_order_detail($connect, $order_id)
{
	$query = "Select * from inventory_order where inventory_order_id = '".$order_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['inventory_order_total'] = $row['inventory_order_total'];
		$output['cash_receivable'] = $row['inventory_order_cash_receivable'];
	}
	return $output;
}

function available_balance_total_receivable($connect){
	$query = "
		SELECT SUM(remaining_balance) as balance FROM tbl_ar_ap
		GROUP BY tbl_ar_ap.ar_ap_customer_name	
	";

	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['balance'], 2);
	}
}

function fill_month_list($connect){
	$query = "
		SELECT * FROM `lutbl_month`";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["month_id"].'">'.$row["month_name"].'</option>';
	}
	return $output;
}

function getMonth($monthId){
	$monthValue = "";
	switch ($monthId) {
		case 1:
			$monthValue = "01";
		break;
		case 2:
			$monthValue = "02";
		break;
		case 3:
			$monthValue = "03";
		break;
		case 4:
			$monthValue = "04";
		break;
		case 5:
			$monthValue = "05";
		break;
		case 6:
			$monthValue = "06";
		break;
		case 7:
			$monthValue = "07";
		break;
		case 8:
			$monthValue = "08";
		break;
		case 9:
			$monthValue = "09";
		break;
		case 10:
			$monthValue = "10";
		break;
		case 11:
			$monthValue = "11";
		break;
		case 12:
			$monthValue = "12";
		break;
	}
	return $monthValue;
}

function countProfit($connect, $order_id){

	$query = "Select Sum(product_profit) as productProfit from inventory_order_product 
	WHERE inventory_order_id = '".$order_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['productProfit'];
	}
}

?>