<?php

//credit_fetch.php

include('database_connection.php');
include('function.php');

$query = '';

$output = array();


$query .= "
	SELECT * FROM tbl_ar_ap 
LEFT JOIN inventory_order ON inventory_order.inventory_order_id = tbl_ar_ap.order_id
LEFT JOIN user_details ON user_details.user_id = inventory_order.user_id
WHERE
";

if(isset($_POST["customerName"]))
{
	$query .= 'tbl_ar_ap.ar_ap_customer_name = "'.$_POST["customerName"].'" AND';
}

if(isset($_POST["search"]["value"]))
{
	$query .= '(tbl_ar_ap.ar_ap_id LIKE "%'.$_POST["search"]["value"].'%" ';
	// $query .= 'OR product.product_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_details.user_name LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY ar_ap_id ASC ';
}

if($_POST['length'] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

//print_r($query);exit();

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
$total_balance = 0;
foreach($result as $row)
{
	if($row['insert_description'] == "Insert")
		$total_balance = $total_balance + $row['remaining_balance'];
	
	if($row['insert_description'] == "Update")
		$total_balance = $total_balance - $row['cash_received'];


	$timestamp = strtotime($row['ar_ap_date']);
	$day = date('l', $timestamp);


	$sub_array = array();
	$sub_array[] = $row['ar_ap_id'];
	$sub_array[] = $row['order_id'];
	$sub_array[] = $row['ar_ap_customer_name'];
	// $sub_array[] = $row['product_name'];
	$sub_array[] = $row['ar_ap_description'];
	$sub_array[] = number_format($row['inventory_order_total'],2);

	$sub_array[] = number_format($row['cash_received'],2);
	// $sub_array[] = number_format($row['remaining_balance'],2);
	$sub_array[] = number_format($total_balance,2);//available_balance_total_receivable($connect);//$row['remaining_balance'];
	$sub_array[] = $day.", ".$row['ar_ap_date'];
	$sub_array[] = $row['user_name'];
	$sub_array[] = $row['ar_ap_sdt'];
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare('SELECT * FROM tbl_ar_ap');
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