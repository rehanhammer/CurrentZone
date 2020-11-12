<?php

//expense_fetch.php

include('database_connection.php');
include('function.php');

$query = '';

$output = array();
$query .= "
	SELECT * FROM tbl_expense
	LEFT JOIN user_details ON user_details.user_id = tbl_expense.expense_created_by
";
if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE tbl_expense.expense_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR tbl_expense.expense_description LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR tbl_expense.expense_price LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_details.user_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR tbl_expense.expense_id LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY expense_id DESC ';
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
	if($row['expense_status'] == '1')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['expense_id'];
	$sub_array[] = $row['expense_name'];
	$sub_array[] = $row['expense_price'];
	$sub_array[] = $row['expense_date'];
	$sub_array[] = $row['user_name'];
	// $sub_array[] = $status;
	$sub_array[] = $row['expense_sdt'];
	$sub_array[] = '<button type="button" name="view" id="'.$row["expense_id"].'" class="btn btn-info btn-xs view">View</button>';
	// $sub_array[] = '<button type="button" name="update" id="'.$row["expense_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	// $sub_array[] = '<button type="button" name="delete" id="'.$row["expense_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["expense_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare('SELECT * FROM tbl_expense');
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