<?php

//credit_fetch.php

include('database_connection.php');
include('function.php');

$query = '';

$output = array();


$query .= "
	SELECT * FROM tbl_cash_book 
WHERE 
";

if(isset($_POST["monthName"]))
{
    $month = getMonth($_POST["monthName"]);
    $year = date("Y");

	$query .= 'MONTH(cash_received_date) = "'.$month.'" AND YEAR(cash_received_date) ="'.$year.'" AND ';
}

if(isset($_POST["search"]["value"]))
{
	$query .= '(tbl_cash_book.cash_book_id LIKE "%'.$_POST["search"]["value"].'%") ';

}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY cash_received_date ASC ';
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
    $action = "";

    if($row['cash_action'] == '1')
	{
		$action = '<span class="label label-primary">Debit</span>';
	}
	else
	{
		$action = '<span class="label label-warning">Credit</span>';
	}
	// if($row['insert_description'] == "Insert")
	// 	$total_balance = $total_balance + $row['remaining_balance'];
	
	// if($row['insert_description'] == "Update")
	// 	$total_balance = $total_balance - $row['cash_received'];


	$timestamp = strtotime($row['cash_received_date']);
	$day = date('l', $timestamp);


    $sub_array = array();
    if($row['cash_action'] == "1")
    {
        $sub_array[] = $action;
	    $sub_array[] = $day.', '.$row['cash_received_date'];
	    $sub_array[] = $row['cash_received_from'];
	    $sub_array[] = $row['cash_amount_received'];
	    $sub_array[] = "";
	    $sub_array[] = "";
        $sub_array[] = "";
        $sub_array[] = "";
    }
    if($row['cash_action'] == "2")
    {
        $sub_array[] = "";
	    $sub_array[] = "";
        $sub_array[] = "";
        $sub_array[] = "";
        $sub_array[] = $action;
	    $sub_array[] = $day.', '.$row['cash_received_date'];
	    $sub_array[] = $row['cash_amount_credit_to'];
        $sub_array[] = $row['cash_amount_credit'];
	    
    }

	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare('SELECT * FROM tbl_cash_book');
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