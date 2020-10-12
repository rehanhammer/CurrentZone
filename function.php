<?php
//function.php

function fill_category_list($connect)
{
	$query = "
	SELECT * FROM category 
	WHERE category_status = 'active' 
	ORDER BY category_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["category_id"].'">'.$row["category_name"].'</option>';
	}
	return $output;
}

function fill_brand_list($connect, $category_id)
{
	$query = "SELECT * FROM brand 
	WHERE brand_status = 'active' 
	AND category_id = '".$category_id."'
	ORDER BY brand_name ASC";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<option value="">Select Brand</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["brand_id"].'">'.$row["brand_name"].'</option>';
	}
	return $output;
}

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
	WHERE product_status = 'active' 
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
		$output['quantity'] = $row["product_quantity"];
		$output['price'] = $row['product_base_price'];
		$output['quantity_remaining'] = $row['product_quantity_remaining'];
		$output['quantity_sold'] = $row['product_quantity_sold'];
	}
	return $output;
}

function available_product_quantity($connect, $product_id)
{
	$product_data = fetch_product_details($product_id, $connect);
	$query = "
	SELECT 	inventory_order_product.quantity FROM inventory_order_product 
	INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
	WHERE inventory_order_product.product_id = '".$product_id."' AND
	inventory_order.inventory_order_status = 'active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$total = 0;
	foreach($result as $row)
	{
		$total = $total + $row['quantity'];
	}
	$available_quantity = intval($product_data['quantity']) - intval($total);
	if($available_quantity == 0)
	{
		$update_query = "
		UPDATE product SET 
		product_status = 'inactive' 
		WHERE product_id = '".$product_id."'
		";
		$statement = $connect->prepare($update_query);
		$statement->execute();
	}
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

function count_total_category($connect)
{
	$query = "
	SELECT * FROM category WHERE category_status='active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_brand($connect)
{
	$query = "
	SELECT * FROM brand WHERE brand_status='active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_product($connect)
{
	$query = "
	SELECT * FROM product WHERE product_status='active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}
function count_total_order_value_base($connect)
{
	$query = "
	SELECT sum(inventory_order_base_price_total) as total_order_value FROM inventory_order 
	WHERE inventory_order_status = '1' OR inventory_order_status = '2'
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

function count_total_order_value($connect)
{
	$query = "
	SELECT sum(inventory_order_sale_price_total) as total_order_value FROM inventory_order 
	WHERE inventory_order_status = '1' OR inventory_order_status = '2'
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
	SELECT sum(order_cash_receivable) as total_order_value FROM inventory_order WHERE payment_mode = 'credit' AND inventory_order_status='1' OR inventory_order_status = '2'
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

function count_total_profit_order_value($connect){
	$query = "
	SELECT sum(inventory_order_sale_price_total) as total_order_value,
	sum(inventory_order_base_price_total) as total_actual_value
	FROM inventory_order WHERE inventory_order_status = '1' OR inventory_order_status = '2'
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
		$total = $row['total_order_value'] - $row['total_actual_value'];

		return number_format($total, 2);
	}
}

function get_user_wise_total_order($connect)
{
	$query = '
	SELECT sum(inventory_order.inventory_order_sale_price_total) as order_total, 
	SUM(CASE WHEN inventory_order.order_cash_receivable > 0 OR inventory_order.payment_mode = "cash" THEN inventory_order.order_cash_received ELSE 0 END) AS cash_order_total, 
	SUM(CASE WHEN inventory_order.order_cash_receivable > 0 OR inventory_order.payment_mode = "credit" THEN inventory_order.order_cash_receivable ELSE 0 END) AS credit_order_total, 
	user_details.user_name 
	FROM inventory_order 
	INNER JOIN user_details ON user_details.user_id = inventory_order.user_id 
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
				<th>Total Order Value</th>
				<th>Total Cash Received</th>
				<th>Total Cash Receivable</th>
			</tr>
	';

	$total_order = 0;
	$total_cash_order = 0;
	$total_credit_order = 0;
	foreach($result as $row)
	{
		$output .= '
		<tr>
			<td>'.$row['user_name'].'</td>
			<td align="right"> '.$row["order_total"].'</td>
			<td align="right"> '.$row["cash_order_total"].'</td>
			<td align="right"> '.$row["credit_order_total"].'</td>
		</tr>
		';

		$total_order = $total_order + $row["order_total"];
		$total_cash_order = $total_cash_order + $row["cash_order_total"];
		$total_credit_order = $total_credit_order + $row["credit_order_total"];
	}
	$output .= '
	<tr>
		<td align="right"><b>Total</b></td>
		<td align="right"><b> '.$total_order.'</b></td>
		<td align="right"><b> '.$total_cash_order.'</b></td>
		<td align="right"><b> '.$total_credit_order.'</b></td>
	</tr></table></div>
	';
	return $output;
}

function getDateTime(){

	date_default_timezone_set('Asia/Karachi');
	$date = date('Y-m-d H:i:s');

	return $date;
}

function update_product_weight($connect, $product_id, $quantity, $sold_quantity)
{
	$datetime = getDateTime();

	$update_query = "
	UPDATE product SET
	product_quantity_remaining = '".$quantity."',
	product_quantity_sold = '".$sold_quantity."',
	product_udt = '".$datetime."'
	WHERE product_id = '".$product_id."'
	";
	$statement = $connect->prepare($update_query);
	$statement->execute();
}

function fetch_order_detail($connect, $order_id)
{
	$query = "Select * from inventory_order where inventory_order_id = '".$order_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['inventory_order_sale_price_total'] = $row['inventory_order_sale_price_total'];
		$output['cash_receivable'] = $row['order_cash_receivable'];
	}
	return $output;
}
?>