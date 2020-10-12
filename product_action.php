<?php

//product_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'load_brand')
	{
		echo fill_brand_list($connect, $_POST['category_id']);
	}

	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO product (category_id, brand_id, product_name, product_description, product_quantity, product_quantity_remaining, product_quantity_sold, product_unit, product_base_price, product_total_amount, product_supplier_name, product_supplier_contact_no, product_enter_by, product_status, product_date) 
		VALUES (:category_id, :brand_id, :product_name, :product_description, :product_quantity, :product_quantity_remaining, :product_quantity_sold, :product_unit, :product_base_price, :product_total_amount, :product_supplier_name, :product_supplier_contact_no, :product_enter_by, :product_status, :product_date)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':category_id'					=>	$_POST['category_id'],
				':brand_id'						=>	$_POST['brand_id'],
				':product_name'					=>	$_POST['product_name'],
				':product_description'			=>	$_POST['product_description'],
				':product_quantity'				=>	$_POST['product_quantity'],
				':product_quantity_remaining'	=>	$_POST['product_quantity'],
				':product_quantity_sold'		=>	0,
				':product_unit'					=>	$_POST['product_unit'],
				':product_base_price'			=>	$_POST['product_base_price'],
				':product_total_amount'			=>	$_POST['product_base_price'] * $_POST['product_quantity'],
				':product_supplier_name'		=>	$_POST['supplier_name'],
				':product_supplier_contact_no'	=>	$_POST['supplier_contact'],
				':product_enter_by'				=>	$_SESSION["user_id"],
				':product_status'				=>	'active',
				':product_date'					=>	$_POST['product_date'],
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product Added';
		}
	}
	if($_POST['btn_action'] == 'product_details')
	{
		$query = "
		SELECT * FROM product 
		INNER JOIN category ON category.category_id = product.category_id 
		INNER JOIN brand ON brand.brand_id = product.brand_id 
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
			if($row['product_status'] == 'active')
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
				<td>Product Description</td>
				<td>'.$row["product_description"].'</td>
			</tr>
			<tr>
				<td>Category</td>
				<td>'.$row["category_name"].'</td>
			</tr>
			<tr>
				<td>Brand</td>
				<td>'.$row["brand_name"].'</td>
			</tr>
			<tr>
				<td>Total Quantity</td>
				<td>'.$row["product_quantity"].' '.$row["product_unit"].'</td>
			</tr>
			<tr>
				<td>Available Quantity</td>
				<td>'.$row["product_quantity_remaining"].' '.$row["product_unit"].'</td>
			</tr>
			<tr>
				<td>Sold Quantity</td>
				<td>'.$row["product_quantity_sold"].' '.$row["product_unit"].'</td>
			</tr>
			<tr>
				<td>Base Price</td>
				<td>'.$row["product_base_price"].'</td>
			</tr>
			<tr>
				<td>Total Price</td>
				<td>'.$row["product_total_amount"].'</td>
			</tr>
			<tr>
				<td>Supplier Name</td>
				<td>'.$row["product_supplier_name"].'</td>
			</tr>
			<tr>
				<td>Supplier Contact No</td>
				<td>'.$row["product_supplier_contact_no"].'</td>
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
			$output['category_id'] = $row['category_id'];
			$output['brand_id'] = $row['brand_id'];
			$output["brand_select_box"] = fill_brand_list($connect, $row["category_id"]);
			$output['product_name'] = $row['product_name'];
			$output['product_description'] = $row['product_description'];
			$output['product_quantity_remaining'] = $row['product_quantity_remaining'];
			$output['product_unit'] = $row['product_unit'];
			$output['product_base_price'] = $row['product_base_price'];
			$output['supplier_name'] = $row['product_supplier_name'];
			$output['supplier_contact'] = $row['product_supplier_contact_no'];
			$output['product_date'] = $row['product_date'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$date = getDateTime();

		$query = "
		UPDATE product 
		set category_id = :category_id, 
		brand_id = :brand_id,
		product_name = :product_name,
		product_description = :product_description, 
		product_quantity =  product_quantity + :product_quantity, 
		product_quantity_remaining =  product_quantity_remaining + :product_quantity_remaining, 
		product_unit = :product_unit, 
		product_base_price = :product_base_price,
		product_udt = :product_udt
		WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':category_id'					=>	$_POST['category_id'],
				':brand_id'						=>	$_POST['brand_id'],
				':product_name'					=>	$_POST['product_name'],
				':product_description'			=>	$_POST['product_description'],
				':product_quantity'				=>	$_POST['product_quantity'],
				':product_quantity_remaining'	=>	$_POST['product_quantity'],
				':product_unit'					=>	$_POST['product_unit'],
				':product_base_price'			=>	$_POST['product_base_price'],
				':product_udt'					=>	$date,
				':product_id'					=>	$_POST['product_id']
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
		$status = 'active';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';
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
			echo 'Product status change to ' . $status;
		}
	}
}


?>