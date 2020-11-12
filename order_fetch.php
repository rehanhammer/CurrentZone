<?php

//order_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
	SELECT * FROM inventory_order
	WHERE 
";

if($_SESSION['type'] == 'user')
{
	$query .= 'user_id = "'.$_SESSION["user_id"].'" AND ';
}

if(isset($_POST["search"]["value"]))
{
	$query .= '(inventory_order.inventory_order_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR customer_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_total LIKE "%'.$_POST["search"]["value"].'%" ';
	// $query .= 'OR inventory_order_status LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_date LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY inventory_order.inventory_order_id DESC ';
}

if($_POST["length"] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

//print_r($query);exit();

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
$total_profit = 0;
foreach($result as $row)
{
	$profit_count = countProfit($connect, $row['inventory_order_id']);
	$payment_status = '';

	if($row['payment_status'] == 'cash')
	{
		$payment_status = '<span class="label label-primary">Cash</span>';
	}
	else
	{
		$payment_status = '<span class="label label-warning">Credit</span>';
	}

	$status = '';
	if($row['inventory_order_status'] == '1')
	{
		$status = '<span class="label label-success">Completed</span>';
	}
	else if($row['inventory_order_status'] == '2')
	{
		$status = '<span class="label label-primary">InProgress</span>';
	}
	else{
		$status = '<span class="label label-danger">InActive</span>';
	}
	
	$sub_array = array();
	$sub_array[] = $row['inventory_order_id'];
	$sub_array[] = $row['customer_name'];
	$sub_array[] = number_format($row['inventory_order_total'],2);
	$sub_array[] = number_format($row['inventory_order_cash_receivable'],2);
	$sub_array[] = "Rs. ". number_format($profit_count, 2);
	$sub_array[] = $payment_status;
	$sub_array[] = $status;
	$sub_array[] = $row['inventory_order_date'];
	if($_SESSION['type'] == 'master')
	{
		$sub_array[] = get_user_name($connect, $row['user_id']);
	}
	$sub_array[] = '<button type="button" name="view" id="'.$row["inventory_order_id"].'" class="btn btn-info btn-xs view">View</button>';
	$sub_array[] = '<a href="view_order.php?pdf=1&order_id='.$row["inventory_order_id"].'" class="btn btn-info btn-xs">View PDF</a>';
	//if($row['inventory_order_status'] == '3' || $row['inventory_order_status'] == '2')
	//{
		$sub_array[] = '<button type="button" name="update" id="'.$row["inventory_order_id"].'" class="btn btn-warning btn-xs update">Update</button>';
		$sub_array[] = '<button type="button" name="delete" id="'.$row["inventory_order_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["inventory_order_status"].'">Delete</button>';
	//}
	// else{
	// 	$sub_array[] = $status;
	// 	$sub_array[] = $status;
	// }
		$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM inventory_order");
	$statement->execute();
	return $statement->rowCount();
}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"    			=> 	$data
);	

echo json_encode($output);

?>