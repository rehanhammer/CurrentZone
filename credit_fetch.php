<?php

//credit_fetch.php

include('database_connection.php');
include('function.php');

$query = '';

$output = array();
$query .= "
	SELECT * FROM tbl_credit_payment_history 
LEFT JOIN inventory_order ON inventory_order.inventory_order_id = tbl_credit_payment_history.order_id
LEFT JOIN inventory_order_product ON inventory_order_product.inventory_order_id = inventory_order.inventory_order_id 
LEFT JOIN user_details ON user_details.user_id = inventory_order.user_id
LEFT JOIN product ON product.product_id = inventory_order_product.product_id
";

if(isset($_POST["search"]["value"]))
{
	$query .= 'OR product.product_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_details.user_name LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY history_id DESC ';
}

if($_POST['length'] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
	$status = '';
	if($row['history_status'] == '1')
	{
		$status = '<span class="label label-success">Paid</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Not Paid</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['history_id'];
	$sub_array[] = $row['order_id'];
	$sub_array[] = $row['product_name'];
	$sub_array[] = $row['inventory_order_name'];
	$sub_array[] = $row['credit_received'];
	$sub_array[] = $row['history_sdt'];
	$sub_array[] = $row['user_name'];
	$sub_array[] = $status;
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare('SELECT * FROM tbl_credit_payment_history');
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