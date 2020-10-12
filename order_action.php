<?php

//order_action.php
error_reporting(0);
include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'fetch_productquantity'){
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
			$output['product_quantity_remaining'] = $row['product_quantity_remaining'] ;
		}
		echo json_encode($output);
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
				<td>'.$row["inventory_order_sale_price_total"].'</td>
			</tr>
			
			<tr>
				<td>Cash Received</td>
				<td>'.$row["order_cash_received"].'</td>
			</tr>
			<tr>
				<td>Cash Receivable</td>
				<td>'.$row["order_cash_receivable"].'</td>
			</tr>
			<tr>
				<td>Order Date</td>
				<td>'.$row["inventory_order_date"].'</td>
			</tr>
			<tr>
				<td>Customer Name</td>
				<td>'.$row["inventory_order_name"].'</td>
			</tr>
			<tr>
				<td>Customer Address</td>
				<td>'.$row["inventory_order_address"].'</td>
			</tr>
			<tr>
				<td>Product Quantity</td>
				<td>'.$row["quantity"].'</td>
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
				<td>Status</td>
				<td>'.$status.'</td>
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
		INSERT INTO inventory_order (user_id, inventory_order_sale_price_total,
		order_cash_received, inventory_order_date, inventory_order_name, inventory_order_address, payment_mode, inventory_order_status) 
		VALUES (:user_id, :inventory_order_sale_price_total, :order_cash_received, :inventory_order_date, :inventory_order_name, :inventory_order_address, :payment_mode, :inventory_order_status)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':user_id'						=>	$_SESSION["user_id"],
				':inventory_order_sale_price_total'		=>	0,
				':order_cash_received'			=> $_POST['cash_received'],
				':inventory_order_date'			=>	$_POST['inventory_order_date'],
				':inventory_order_name'			=>	$_POST['inventory_order_name'],
				':inventory_order_address'		=>	$_POST['inventory_order_address'],
				':payment_mode'					=>	$_POST['payment_mode'],
				':inventory_order_status'		=>	$_POST['order_status'],
			)
		);
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$inventory_order_id = $statement->fetchColumn();

		if(isset($inventory_order_id))
		{
			$total_amount = 0;
			for($count = 0; $count<count($_POST["product_id"]); $count++)
			{
				$product_details = fetch_product_details($_POST["product_id"][$count], $connect);
				$sub_query = "
				INSERT INTO inventory_order_product (inventory_order_id, product_id, quantity, base_price, sale_price) VALUES (:inventory_order_id, :product_id, :quantity, :base_price, :sale_price)
				";
				$statement = $connect->prepare($sub_query);
				$statement->execute(
					array(
						':inventory_order_id'	=>	$inventory_order_id,
						':product_id'			=>	$_POST["product_id"][$count],
						':quantity'				=>	$_POST["quantity"][$count],
						':base_price'			=>	$product_details['price'],
						':sale_price'			=> 	$_POST["sale_price"][$count],
					)
				);

				$base_price = $product_details['price'] * $_POST["quantity"][$count];
				$sale_price = $_POST["sale_price"][$count] * $_POST["quantity"][$count];
				
				$sale_price_total = $total_amount + ($sale_price);
				$base_price_total = $total_amount + ($base_price);

				$updated_weight = $product_details['quantity_remaining'] - $_POST["quantity"][$count];
				$sold_weight = $product_details['quantity_sold'] + $_POST["quantity"][$count];
				update_product_weight($connect, $_POST["product_id"][$count], $updated_weight, $sold_weight);
			}

			$cash_receivable = $sale_price_total - $_POST['cash_received'];

			// if($cash_receivable > 0){
			// 	$cQuery = "
			// 	INSERT INTO tbl_credit_payment_history (order_id,
			// 	credit_received) 
			// 	VALUES (:order_id, :credit_received)
			// 	";
			// 	$cStatement = $connect->prepare($cQuery);
			// 	$cStatement->execute(
			// 		array(
			// 			':order_id'			=>	$inventory_order_id,
			// 			':credit_received'	=>	$cash_receivable,
			// 		)
			// 	);
			// }

			$update_query = "
			UPDATE inventory_order 
			SET inventory_order_sale_price_total = '".$sale_price_total."',
			inventory_order_base_price_total ='".$base_price_total."',
			order_cash_receivable = '".$cash_receivable."'
			WHERE inventory_order_id = '".$inventory_order_id."'
			";
			$statement = $connect->prepare($update_query);
			$statement->execute();
			$result = $statement->fetchAll();
			if(isset($result))
			{
				echo 'Order Created...';
				echo '<br />';
				echo 'Total Amount = ' .$sale_price_total;
				echo '<br />';
				//echo $inventory_order_id;
			}
		}
	}

	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM inventory_order
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
		$product_details = '';
		$count = '';
		$output = array();
		foreach($result as $row)
		{
			$output['inventory_order_name'] = $row['inventory_order_name'];
			$output['inventory_order_date'] = $row['inventory_order_date'];
			$output['inventory_order_address'] = $row['inventory_order_address'];
			$output['cash_remaining'] = $row['order_cash_receivable'];
			$output['payment_mode'] = $row['payment_mode'];
			$output['order_status'] = $row['inventory_order_status'];

			$amount_total = $row['quantity'] * $row['sale_price'];

			$product_details .= '
			<script>
			$(document).ready(function(){
				$("#product_id'.$count.'").selectpicker("val", '.$row["product_id"].');
				$(".selectpicker").selectpicker();
			});
			</script>
			<span id="row'.$count.'">
				<div class="row">
					<div class="col-md-3">Product Name
						<select name="product_id[]" id="product_id'.$count.'" class="form-control selectpicker" data-live-search="true" disabled="disabled">
							'.fill_product_list($connect).'
						</select>
						<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.$row["product_id"].'" />
					</div>
					<div class="col-md-2">Quantity
						<input type="text" readonly name="quantity[]" class="form-control" value="'.$row["quantity"].'" />
					</div>
					<div class="col-md-2">Remaining
					<input type="text" name="hidden_quantity[]" class="form-control" value ="'.$row['product_quantity_remaining'].'" id="hidden_quantity'.$count.'" readonly />
					</div>
					<div class="col-md-2">Price Per Quantity
					<input type="text" name="sale_price[]" class="form-control" value ="'.$row['sale_price'].'" id="sale_price'.$count.'" readonly/>
					</div>
					<div class="col-md-2">Total
					<input type="text" name="amount_total[]" class="form-control" value ="'.$amount_total.'" id="amount_total'.$count.'" readonly/>
					</div>
					<div class="col-md-1">
			';

			if($count == '')
			{
				$product_details .= '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			}
			else
			{
				$product_details .= '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">-</button>';
			}
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
		//foreach($sub_result as $sub_row)
		//{
			// $product_details .= '
			// <script>
			// $(document).ready(function(){
			// 	$("#product_id'.$count.'").selectpicker("val", '.$sub_row["product_id"].');
			// 	$(".selectpicker").selectpicker();
			// });
			// </script>
			// <span id="row'.$count.'">
			// 	<div class="row">
			// 		<div class="col-md-3">Product Name
			// 			<select name="product_id[]" id="product_id'.$count.'" class="form-control selectpicker" data-live-search="true" required>
			// 				'.fill_product_list($connect).'
			// 			</select>
			// 			<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.$sub_row["product_id"].'" />
			// 		</div>
			// 		<div class="col-md-2">Quantity
			// 			<input type="text" name="quantity[]" class="form-control" value="'.$sub_row["quantity"].'" required />
			// 		</div>
			// 		<div class="col-md-2">Remaining
			// 		<input type="text" name="hidden_quantity[]" class="form-control" value ="'..'" id="hidden_quantity'.$count.'" readonly /> />
			// 		</div>
			// 		<div class="col-md-1">
			// ';

			// if($count == '')
			// {
			// 	$product_details .= '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			// }
			// else
			// {
			// 	$product_details .= '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">-</button>';
			// }
			// $product_details .= '
			// 			</div>
			// 		</div>
			// 	</div><br />
			// </span>
			// ';
			// $count = $count + 1;
		//}
		$output['product_details'] = $product_details;
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$date = getDateTime();

		$order_detail = fetch_order_detail($connect , $_POST["inventory_order_id"]);

		$cash_receivable = $order_detail['cash_receivable'];
		$total_cash = $order_detail['inventory_order_sale_price_total'];
		$cash_received = $cash_receivable + $_POST["cash_received"];

		if($total_cash >= $cash_received )
		{
			$cQuery = "
				INSERT INTO tbl_credit_payment_history (order_id,
				credit_received) 
				VALUES (:order_id, :credit_received)
			";
			$cStatement = $connect->prepare($cQuery);
			$cStatement->execute(
				array(
					':order_id'			=>	$_POST["inventory_order_id"],
					':credit_received'	=>	intval($_POST["cash_received"]),
				)
			);
		}


		$update_query = "
		UPDATE inventory_order 
		SET inventory_order_name = :inventory_order_name, 
		inventory_order_date = :inventory_order_date, 
		inventory_order_address = :inventory_order_address,
		order_cash_received = order_cash_received + :order_cash_received,
		order_cash_receivable = order_cash_receivable - :order_cash_receivable, 
		payment_mode = :payment_mode,
		inventory_order_status = :inventory_order_status,
		inventory_order_udt = :inventory_order_udt
		WHERE inventory_order_id = :inventory_order_id
		";
		$statement = $connect->prepare($update_query);
		$statement->execute(
			array(
				':inventory_order_name'			=>	$_POST["inventory_order_name"],
				':inventory_order_date'			=>	$_POST["inventory_order_date"],
				':inventory_order_address'		=>	$_POST["inventory_order_address"],
				':order_cash_received'			=>	intval($_POST["cash_received"]),
				':order_cash_receivable'		=>	intval($_POST["cash_received"]),
				':payment_mode'					=>	$_POST["payment_mode"],
				':inventory_order_status'		=>	$_POST["order_status"],
				':inventory_order_udt'			=>	$date,
				':inventory_order_id'			=>	$_POST["inventory_order_id"]
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
		// 		INSERT INTO inventory_order_product (inventory_order_id, product_id, quantity, price, tax) VALUES (:inventory_order_id, :product_id, :quantity, :price, :tax)
		// 		";
		// 		$statement = $connect->prepare($sub_query);
		// 		$statement->execute(
		// 			array(
		// 				':inventory_order_id'	=>	$_POST["inventory_order_id"],
		// 				':product_id'			=>	$_POST["product_id"][$count],
		// 				':quantity'				=>	$_POST["quantity"][$count],
		// 				':price'				=>	$product_details['price'],					)
		// 		);
		// 		$base_price = $product_details['price'] * $_POST["quantity"][$count];
		// 		$tax = ($base_price/100)*$product_details['tax'];
		// 		$total_amount = $total_amount + ($base_price + $tax);
		// 	}
		// 	$update_query = "
		// 	UPDATE inventory_order 
		// 	SET inventory_order_name = :inventory_order_name, 
		// 	inventory_order_date = :inventory_order_date, 
		// 	inventory_order_address = :inventory_order_address, 
		// 	inventory_order_total = :inventory_order_total, 
		// 	payment_mode = :payment_mode,
		// 	inventory_order_status = :inventory_order_status
		// 	WHERE inventory_order_id = :inventory_order_id
		// 	";
		// 	$statement = $connect->prepare($update_query);
		// 	$statement->execute(
		// 		array(
		// 			':inventory_order_name'			=>	$_POST["inventory_order_name"],
		// 			':inventory_order_date'			=>	$_POST["inventory_order_date"],
		// 			':inventory_order_address'		=>	$_POST["inventory_order_address"],
		// 			':inventory_order_total'		=>	$total_amount,
		// 			':payment_status'				=>	$_POST["payment_status"],
		// 			':inventory_order_status'		=>	$_POST['order_status'],
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
		$status = 'active';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';
		}
		$query = "
		UPDATE inventory_order 
		SET inventory_order_status = :inventory_order_status 
		WHERE inventory_order_id = :inventory_order_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':inventory_order_status'	=>	$status,
				':inventory_order_id'		=>	$_POST["inventory_order_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Order status change to ' . $status;
		}
	}
}

?>