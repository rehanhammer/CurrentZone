<?php

//product_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{

	if($_POST['btn_action'] == 'Add')
	{
		$product_weight = $_POST['product_weight'];
		$product_rate = $_POST['product_base_price'];

		$total_cost = $product_weight * $product_rate;
		
		$cost_per_piece = $total_cost / $product_weight ;

		$query = "
		INSERT INTO product (product_name, product_weight_remaining, product_total_weight,
			product_unit, product_base_price,
			product_total_cost, product_cost_per_kg, product_enter_by,
			product_status, product_date) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		";
		$statement = $connect->prepare($query);

		$statement->execute(
			array(
					$_POST['product_name'],
				 	$_POST['product_weight'],
					$_POST['product_weight'],
					$_POST['product_unit'],
					$_POST['product_base_price'],
					$total_cost,
					$cost_per_piece,
					$_SESSION["user_id"],
					$_POST['product_status'],
					$_POST['product_date']
			)
		);
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$productid = $statement->fetchColumn();
		if($productid)
		{
			echo 'Product Added';
		}
		else{
			echo 'Product Not Added';
		}
	}
	if($_POST['btn_action'] == 'product_details')
	{
		$query = "
		SELECT * FROM product 
		INNER JOIN user_details ON user_details.user_id = product.product_enter_by 
		WHERE product.product_id = '".$_POST["product_id"]."'
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
			if($row['product_status'] == '1')
			{
				$status = '<span class="label label-success">Active</span>';
			}
			else
			{
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$output .= '
			<tr>
				<td>Product Name</td>
				<td>'.$row["product_name"].'</td>
			</tr>

			<tr>
				<td>Product Total Weight</td>
				<td>'.$row["product_total_weight"].' '.$row["product_unit"].'</td>
			</tr>
			
			<tr>
				<td>Available Quantity</td>
				<td>'.$row["product_weight_remaining"].' '.$row["product_unit"].'</td>
			</tr>

			<tr>
				<td>Base Price</td>
				<td>'.$row["product_base_price"].'</td>
			</tr>
				
			<tr>
				<td>Product Total Cost</td>
				<td>'.$row["product_total_cost"].'</td>
			</tr>
			
			<tr>
				<td>Product Cost Per Piece</td>
				<td>'.$row["product_cost_per_kg"].'</td>
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
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM product WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_id'	=>	$_POST["product_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['product_name'] = $row['product_name'];
			$output['product_weight_remaining'] = $row['product_weight_remaining'];
			$output['product_unit'] = $row['product_unit'];
			$output['product_base_price'] = $row['product_base_price'];
			$output['product_date'] = $row['product_date'];
			$output['product_status'] = $row['product_status'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$datetime = getDateTime();
		$product = fetch_product_details($_POST['product_id'], $connect);

		$product_name = $_POST['product_name'];
		$product_weight = $_POST['product_weight'] + $product['weight'];
		$product_remaining_weight = $product['weight_remaining'] + $_POST['product_weight'];
		
		$total_cost = $product_weight * $product['price'];

		$query = "
		UPDATE product 
		set product_name = :product_name,
		product_total_weight = product_total_weight + :product_total_weight,
		product_weight_remaining = product_weight_remaining + :product_weight_remaining,
		product_unit = :product_unit,
		product_total_cost = :product_total_cost,
		product_status = :product_status,
		product_date = :product_date,
		product_udt = :product_udt 
		WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_name'				=>	$_POST['product_name'],
				':product_total_weight'		=>	$_POST['product_weight'],
				':product_weight_remaining'	=>	$_POST['product_weight'],
				':product_unit'				=>	$_POST['product_unit'],
				':product_total_cost'		=>	$total_cost,
				':product_status'			=>	$_POST['product_status'],
				':product_date'				=>	$_POST['product_date'],
				':product_udt'          	=> 	$datetime,
				':product_id'				=>	$_POST['product_id']
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product Details Edited';
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = '1';
		if($_POST['status'] == '1')
		{
			$status = '2';
		}
		$query = "
		UPDATE product 
		SET product_status = :product_status 
		WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_status'	=>	$status,
				':product_id'		=>	$_POST["product_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product status changed';

			// echo 'Product status change to ' . $status;
		}
	}
}


?>