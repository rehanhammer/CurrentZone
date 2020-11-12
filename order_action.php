<?php

//order_action.php
error_reporting(0);
include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'fetch_productweight'){
		$query .= "
		SELECT * FROM product where product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_id'	=>	$_POST["product_id"]
			)
		);
		
		$result = $statement->fetchAll();
		$output = array();
		foreach($result as $row)
		{
			$output['product_id'] = $row['product_id'];
			// $output['product_weight'] = $row['product_weight'];
			// $output['product_weight_sold'] = $row['product_weight_sold'];
			$output['product_weight_remaining'] = $row['product_weight_remaining'] ;
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'fetch_producttotal'){
		
		$product_amount = $_POST['sale_price'] * $_POST['weight'];

		$total_amount = number_format($product_amount,2);

		echo json_encode($total_amount);
	}

	if($_POST['btn_action'] == 'order_details')
	{
		$query = "
		SELECT * FROM inventory_order
		LEFT JOIN inventory_order_product ON inventory_order_product.inventory_order_id = inventory_order.inventory_order_id 
		LEFT JOIN product ON product.product_id = inventory_order_product.product_id 
		LEFT JOIN user_details ON user_details.user_id = inventory_order.user_id 
		WHERE inventory_order.inventory_order_id = '".$_POST["order_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();

		$output = '
		<div class="table-responsive">
			<table class="table table-boredered">
		';
		foreach($result as $row)
		{
			$product_data = fetch_product_details($row['product_id'], $connect);

			$status = '';
			if($row['inventory_order_status'] == '1')
			{
				$status = '<span class="label label-success">Completed</span>';
			}
			elseif($row['inventory_order_status'] == '2')
			{
				$status = '<span class="label label-primary">InProgress</span>';
			}
			else{
				$status = '<span class="label label-danger">InActive</span>';
			}
			$output .= '
			<tr>
				<td>Product Name</td>
				<td>'.$row['product_name'].'</td>
			</tr>

			<tr>
				<td>Order Total</td>
				<td>'.$row["inventory_order_total"].'</td>
			</tr>

			<tr>
				<td>Cash Received</td>
				<td>'.$row["order_cash_received"].'</td>
			</tr>
			<tr>
				<td>Cash Receivable</td>
				<td>'.$row["inventory_order_cash_receivable"].'</td>
			</tr>
			<tr>
				<td>Order Date</td>
				<td>'.$row["inventory_order_date"].'</td>
			</tr>
			<tr>
				<td>Customer Name</td>
				<td>'.$row["customer_name"].'</td>
			</tr>
			<tr>
				<td>Customer Address</td>
				<td>'.$row["customer_mobile_no"].'</td>
			</tr>
			<tr>
				<td>Product Pieces</td>
				<td>'.$row["weight"].'</td>
			</tr>
			<tr>
				<td>Product Price</td>
				<td>'.$row["sale_price"].'</td>
			</tr>
			<tr>
				<td>Enter By</td>
				<td>'.$row["user_name"].'</td>
			</tr>
			<tr>
				<td>Product Profit</td>
				<td>'.$row["product_profit"].'</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>'.$status.'</td>
			</tr>
			<tr>
				<td><br></td>
				<td><br></td>
			</tr>
			';
		}
		$output .= '
			</table>
		</div>
		';
		echo $output;
	}

	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO inventory_order (user_id, inventory_order_total, order_cash_received, inventory_order_date, customer_name, customer_mobile_no, payment_status, inventory_order_status) 
		VALUES (:user_id, :inventory_order_total, :order_cash_received, :inventory_order_date, :customer_name, :customer_mobile_no, :payment_status, :inventory_order_status)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':user_id'						=>	$_SESSION["user_id"],
				':inventory_order_total'		=>	0,
				':order_cash_received' 			=> 	$_POST['cash_received'],
				':inventory_order_date'			=>	$_POST['inventory_order_date'],
				':customer_name'				=>	$_POST['inventory_order_name'],
				':customer_mobile_no'			=>	$_POST['customer_mobile_no'],
				':payment_status'				=>	$_POST['payment_mode'],
				':inventory_order_status'		=>	$_POST['order_status'],
			)
		);
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$inventory_order_id = $statement->fetchColumn();

		if(isset($inventory_order_id))
		{
			$total_amount = 0;
			$check_count = 0;
			$delete_count = 0;
			$updated_weight = 0;
			$total_cash_amount = 0;
			// $product_profit = 0;
			for($count = 0; $count<count($_POST["product_id"]); $count++)
			{
				$product_profit = 0;
				$total_amount_per_product = 0;
				$product_details = fetch_product_details($_POST["product_id"][$count], $connect);
				
				if($product_details['weight_remaining'] >= $_POST["quantity"][$count]){
					$check_count = 1;

					$product_sale_amount = $_POST["quantity"][$count] * $_POST["sale_price"][$count];
					$product_base_amount = $_POST["quantity"][$count] * $product_details['price'];
					//$cutting_amount = $_POST["cutting_per_kg"][$count];
					//$total_cutting = $cutting_amount * $_POST["quantity"][$count];
					//$total_mon =  $_POST["quantity"][$count] / 40;
					//$wage_per_mon	= $_POST["wage_per_mon"][$count];
					//$total_wage = $total_mon * $wage_per_mon;

					//$total_cash_amount = $product_amount + $total_cutting + $total_wage;
					
					$total_amount_per_product = $total_amount_per_product + ($product_sale_amount);

					$total_amount = $total_amount + ($product_sale_amount);
					$total_actual_amount = $total_actual_amount + ($product_base_amount);

					$product_profit = $total_amount_per_product - $product_base_amount;

					$sub_query = "
					INSERT INTO inventory_order_product
					(inventory_order_id, product_id, weight, base_price, sale_price
					,product_amount, product_profit) 
					VALUES (:inventory_order_id, :product_id, :weight,
					:base_price, :sale_price, :product_amount, :product_profit)
					";
					$statement = $connect->prepare($sub_query);
					$statement->execute(
						array(
							':inventory_order_id'	=>	$inventory_order_id,
							':product_id'			=>	$_POST["product_id"][$count],
							':weight'				=>	$_POST["quantity"][$count],
							':base_price'			=>	$product_details['price'],
							':sale_price'			=> 	$_POST["sale_price"][$count],
							':product_amount'		=> 	$total_amount_per_product,
							':product_profit'		=> 	$product_profit
						)
					);

					$updated_weight = $product_details['weight_remaining'] - $_POST["quantity"][$count];
					$sold_weight = $product_details['weight_sold'] + $_POST["quantity"][$count];
					update_product_weight($connect, $_POST["product_id"][$count], $updated_weight, $sold_weight);
				}
				else{

					$delete_query = " DELETE FROM inventory_order_product
					 WHERE inventory_order_id = '".$inventory_order_id."'
					";
					$statement = $connect->prepare($delete_query);
					$statement->execute();
					$result = $statement->rowCount();
					
					$delete_subquery = " DELETE FROM inventory_order
					 	WHERE inventory_order_id = '".$inventory_order_id."'
					";
					$subStatement = $connect->prepare($delete_subquery);
					$subStatement->execute();
					$subResult = $subStatement->rowCount();

					$delete_count = 1;
				}
			}
			if($delete_count != 1){

				if($_POST['cash_received'] != 0)
				{
					$insertSubQuery = "INSERT INTO tbl_cash_book
					(order_id, cash_amount_received, cash_received_from, cash_action, cash_received_date)
					VALUES(:order_id, :cash_amount_received, :cash_received_from, :cash_action, :cash_received_date)
					";
					$substatement = $connect->prepare($insertSubQuery);
					$substatement->execute(
						array(
							':order_id'				=>	$inventory_order_id,
							':cash_amount_received'	=>	$_POST['cash_received'],
							':cash_received_from' 	=>	$_POST['inventory_order_name'],
							':cash_action'			=>	1,
							':cash_received_date'	=>	$_POST['inventory_order_date'],
						)
					);
				}

				$insertQuery = "INSERT INTO tbl_ar_ap
						(order_id, ar_ap_customer_name, ar_ap_description, cash_received,
						remaining_balance, insert_description, ar_ap_date)
						VALUES(:order_id, :ar_ap_customer_name, :ar_ap_description,
						:cash_received, :remaining_balance, :insert_description, :ar_ap_date)
					";
				$arstatement = $connect->prepare($insertQuery);
				$arstatement->execute(
					array(
						':order_id'				=>	$inventory_order_id,
						':ar_ap_customer_name'	=>	$_POST['inventory_order_name'],
						':ar_ap_description'	=>	$_POST['cash_received_at'],
						':cash_received'		=>	$_POST['cash_received'],
						':remaining_balance'	=>	$total_amount - $_POST['cash_received'],
						':insert_description'	=> 	"Insert",
						':ar_ap_date'			=>	$_POST['inventory_order_date'],
					)
				);

				$cash_received = $_POST['cash_received'];
				$cash_receivable = $total_amount - $cash_received;

				$update_query = "
				UPDATE inventory_order 
				SET inventory_order_total = '".$total_amount."',
				inventory_order_actual_total = '".$total_actual_amount."',
				inventory_order_cash_receivable = '".$cash_receivable."'
				WHERE inventory_order_id = '".$inventory_order_id."'
				";
				$statement = $connect->prepare($update_query);
				$statement->execute();
				$result = $statement->fetchAll();
				if(isset($result))
				{
					echo 'Order Created...';
					echo '<br />';
					echo 'Total Amount = ' .$total_amount;
					echo '<br />';
					//echo $inventory_order_id;
				}
			}
			else{
				$response = delete_inventory($connect, $inventory_order_id);
				$subresponse = delete_sub_inventory($connect, $inventory_order_id);
				
				if($response || $subresponse)
				{
					echo 'Quantity Issue with Products Please Check quantity again...';
					echo '<br />';
				}
			}
		}
	}

	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT inventory_order.customer_name, inventory_order.inventory_order_date,
		inventory_order.customer_mobile_no, inventory_order.inventory_order_cash_receivable,
		inventory_order.inventory_order_total,
		inventory_order.payment_status, inventory_order.inventory_order_status,
		inventory_order_product.weight, inventory_order_product.sale_price,
		inventory_order_product.product_id, inventory_order_product.product_amount,
		product.product_weight_remaining
		FROM inventory_order
		LEFT JOIN inventory_order_product ON inventory_order_product.inventory_order_id = inventory_order.inventory_order_id 
		LEFT JOIN product ON product.product_id = inventory_order_product.product_id
		WHERE inventory_order.inventory_order_id = :inventory_order_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':inventory_order_id'	=>	$_POST["inventory_order_id"]
			)
		);
		$result = $statement->fetchAll();
		$output = array();
		$product_details = '';
		$count = '';
		$output = array();
		$cash_receivable = 0;
		foreach($result as $row)
		{
			$cash_receivable = $row['inventory_order_cash_receivable'];
			$output['inventory_order_name'] = $row['customer_name'];
			$output['inventory_order_date'] = $row['inventory_order_date'];
			$output['customer_mobile_no'] = $row['customer_mobile_no'];
			$output['cash_remaining'] = $cash_receivable;
			$output['payment_mode'] = $row['payment_status'];
			$output['inventory_order_status'] = $row['inventory_order_status'];

			$amount_total = $row['product_amount'];

			$product_details .= '
			<script>
			$(document).ready(function(){
				$("#product_id'.$count.'").selectpicker("val", '.$row["product_id"].');
				$(".selectpicker").selectpicker();
			});
			</script>
			<span id="row'.$count.'">
				<div class="row">
					<div class="col-md-2">Product Name
						<select name="product_id[]" id="product_id'.$count.'" class="form-control selectpicker" data-live-search="true" disabled="disabled">
							'.fill_product_list($connect).'
						</select>
						<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.$row["product_id"].'" />
					</div>
					<div class="col-md-1">Pieces
						<input type="text" readonly name="quantity[]" class="form-control" value="'.$row["weight"].'" />
					</div>
					<div class="col-md-2">Pieces Left
					<input type="text" name="hidden_quantity[]" class="form-control" value ="'.$row['product_weight_remaining'].'" id="hidden_quantity'.$count.'" readonly />
					</div>
					<div class="col-md-1">Price
					<input type="text" name="sale_price[]" class="form-control" value ="'.$row['sale_price'].'" id="sale_price'.$count.'" readonly/>
					</div>
					<div class="col-md-2">Total
					<input type="text" name="amount_total[]" class="form-control" value ="'.$amount_total.'" id="amount_total'.$count.'" readonly/>
					</div>
					<div class="col-md-1">
			';

			// if($count == '')
			// {
			// 	$product_details .= '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			// }
			// else
			// {
			// 	$product_details .= '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">-</button>';
			// }
			$product_details .= '
						</div>
					</div>
				</div><br />
			</span>
			';
			$count = $count + 1;

		}
		// $sub_query = "
		// SELECT * FROM inventory_order_product 
		// WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
		// ";
		// $statement = $connect->prepare($sub_query);
		// $statement->execute();
		// $sub_result = $statement->fetchAll();
		// $product_details = '';
		// $count = '';
		// foreach($sub_result as $sub_row)
		// {
		// 	$product_details .= '
		// 	<script>
		// 	$(document).ready(function(){
		// 		$("#product_id'.$count.'").selectpicker("val", '.$sub_row["product_id"].');
		// 		$(".selectpicker").selectpicker();
		// 	});
		// 	</script>
		// 	<span id="row'.$count.'">
		// 		<div class="row">
		// 			<div class="col-md-4">Product Name
		// 				<select name="product_id[]" id="product_id'.$count.'" class="form-control selectpicker" data-live-search="true" required>
		// 					'.fill_product_list($connect).'
		// 				</select>
		// 				<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.$sub_row["product_id"].'" />
		// 			</div>
		// 			<div class="col-md-3">Quantity
		// 				<input type="text" name="quantity[]" class="form-control" value="'.$sub_row["quantity"].'" required />
		// 			</div>
		// 			<div class="col-md-3">Price
		// 				<input type="text" name="sale_price[]" class="form-control" value="'.$sub_row["sale_price"].'" required />
		// 			</div>
		// 			<div class="col-md-1">
		// 	';

		// 	if($count == '')
		// 	{
		// 		$product_details .= '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
		// 	}
		// 	else
		// 	{
		// 		$product_details .= '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">-</button>';
		// 	}
		// 	$product_details .= '
		// 				</div>
		// 			</div>
		// 		</div><br />
		// 	</span>
		// 	';
		// 	$count = $count + 1;
		// }
		$output['product_details'] = $product_details;
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$datetime = getDateTime();

		$order_detail = fetch_order_detail($connect , $_POST["inventory_order_id"]);

		$cash_receivable = $order_detail['cash_receivable'];
		// $total_cash = $order_detail['inventory_order_total'];
		$cash_remaining = $cash_receivable - $_POST["cash_received"];

		// if($total_cash >= $cash_received )
		// {
		// 	$cQuery = "
		// 		INSERT INTO tbl_credit_payment_history (order_id,
		// 		credit_received) 
		// 		VALUES (:order_id, :credit_received)
		// 	";
		// 	$cStatement = $connect->prepare($cQuery);
		// 	$cStatement->execute(
		// 		array(
		// 			':order_id'			=>	$_POST["inventory_order_id"],
		// 			':credit_received'	=>	$_POST["cash_received"],
		// 		)
		// 	);
		// }

		$insertQuery = "INSERT INTO tbl_ar_ap
			(order_id, ar_ap_customer_name, ar_ap_description, cash_received,
			remaining_balance, insert_description, ar_ap_date)
			VALUES(:order_id, :ar_ap_customer_name, :ar_ap_description, :cash_received,
			:remaining_balance, :insert_description, :ar_ap_date)
		";
		$arstatement = $connect->prepare($insertQuery);
		$arstatement->execute(
			array(
				':order_id'				=>	$_POST["inventory_order_id"],
				':ar_ap_customer_name' 	=>	$_POST['inventory_order_name'],
				':ar_ap_description'	=>	$_POST['cash_received_at'],
				':cash_received'		=>	$_POST['cash_received'],
				':remaining_balance'	=>	$cash_remaining,
				':insert_description'	=> 	'Update',
				':ar_ap_date'			=>	$_POST['cash_received_date'],
			)
		);

		$insertSubQuery = "INSERT INTO tbl_cash_book
			(order_id, cash_amount_received, cash_received_from, cash_action, cash_received_date)
			VALUES(:order_id, :cash_amount_received, :cash_received_from, :cash_action, :cash_received_date)
		";
		$substatement = $connect->prepare($insertSubQuery);
		$substatement->execute(
			array(
				':order_id'				=>	$_POST["inventory_order_id"],
				':cash_amount_received'	=>	$_POST['cash_received'],
				':cash_received_from' 	=>	$_POST['inventory_order_name'],
				':cash_action'			=>	1,
				':cash_received_date'	=>	$_POST['cash_received_date'],
			)
		);

		$update_query = "
		UPDATE inventory_order 
		SET customer_name = :customer_name, 
		customer_mobile_no = :customer_mobile_no,
		order_cash_received = order_cash_received + :order_cash_received,
		inventory_order_cash_receivable = inventory_order_cash_receivable - :inventory_order_cash_receivable, 
		payment_status = :payment_status,
		inventory_order_status = :inventory_order_status,
		inventory_order_udt = :inventory_order_udt
		WHERE inventory_order_id = :inventory_order_id
		";
		$statement = $connect->prepare($update_query);
		$statement->execute(
			array(
				':customer_name'						=>	$_POST["inventory_order_name"],
				//':inventory_order_date'				=>	$_POST["inventory_order_date"],
				':customer_mobile_no'					=>	$_POST["customer_mobile_no"],
				':order_cash_received'					=>	$_POST["cash_received"],
				':inventory_order_cash_receivable'		=>	$_POST["cash_received"],
				':payment_status'						=>	$_POST["payment_mode"],
				':inventory_order_status'				=>	$_POST["order_status"],
				':inventory_order_udt'					=>	$date,
				':inventory_order_id'					=>	$_POST["inventory_order_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Order Edited...';
		}

		// $delete_query = "
		// DELETE FROM inventory_order_product 
		// WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
		// ";
		// $statement = $connect->prepare($delete_query);
		// $statement->execute();
		// $delete_result = $statement->fetchAll();
		// if(isset($delete_result))
		// {
		// 	$total_amount = 0;
		// 	for($count = 0; $count < count($_POST["product_id"]); $count++)
		// 	{
		// 		$product_details = fetch_product_details($_POST["product_id"][$count], $connect);
		// 		$sub_query = "
		// 		INSERT INTO inventory_order_product (inventory_order_id, product_id, quantity, price, sale_price, inventory_order_product_udt) VALUES (:inventory_order_id, :product_id, :quantity, :price, :sale_price, :inventory_order_product_udt)
		// 		";
		// 		$statement = $connect->prepare($sub_query);
		// 		$statement->execute(
		// 			array(
		// 				':inventory_order_id'	=>	$_POST["inventory_order_id"],
		// 				':product_id'			=>	$_POST["product_id"][$count],
		// 				':quantity'				=>	$_POST["quantity"][$count],
		// 				':price'				=>	$product_details['price'],
		// 				':sale_price'			=>	$_POST["sale_price"][$count],
		// 				':inventory_order_product_udt' => $datetime
		// 			)
		// 		);
		// 		$base_price = $_POST["sale_price"][$count] * $_POST["quantity"][$count];
		// 		$total_amount = $total_amount + ($base_price);
		// 	}
		// 	$update_query = "
		// 	UPDATE inventory_order 
		// 	SET inventory_order_name = :inventory_order_name, 
		// 	inventory_order_date = :inventory_order_date, 
		// 	customer_mobile_no = :customer_mobile_no, 
		// 	inventory_order_total = :inventory_order_total, 
		// 	payment_status = :payment_status,
		// 	inventory_order_status = :inventory_order_status,
		// 	inventory_order_udt = :inventory_order_udt
		// 	WHERE inventory_order_id = :inventory_order_id
		// 	";
		// 	$statement = $connect->prepare($update_query);
		// 	$statement->execute(
		// 		array(
		// 			':inventory_order_name'			=>	$_POST["inventory_order_name"],
		// 			':inventory_order_date'			=>	$_POST["inventory_order_date"],
		// 			':customer_mobile_no'		=>	$_POST["customer_mobile_no"],
		// 			':inventory_order_total'		=>	$total_amount,
		// 			':payment_status'				=>	$_POST["payment_mode"],
		// 			':inventory_order_status'		=>	$_POST["order_status"],
		// 			':inventory_order_udt'          => $datetime,
		// 			':inventory_order_id'			=>	$_POST["inventory_order_id"]
		// 		)
		// 	);
		// 	$result = $statement->fetchAll();
		// 	if(isset($result))
		// 	{
		// 		echo 'Order Edited...';
		// 	}
		// }
	}

	if($_POST['btn_action'] == 'delete')
	{
		$datetime = getDateTime();
		// $status = '1';
		// if($_POST['status'] == '1')
		// {
		// 	$status = '2';
		// }
		$status = '3';
		$query = "
		UPDATE inventory_order 
		SET inventory_order_status = :inventory_order_status,
		inventory_order_edt = :inventory_order_edt
		WHERE inventory_order_id = :inventory_order_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':inventory_order_status'	=>	$status,
				':inventory_order_edt'	=>	$datetime,
				':inventory_order_id'		=>	$_POST["inventory_order_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Order status changed';

			// echo 'Order status change to ' . $status;
		}
	}
}

?>