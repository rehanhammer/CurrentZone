<?php

//product_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{

	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO tbl_expense (expense_name, expense_description, expense_price, expense_date, expense_created_by) 
		VALUES (:expense_name, :expense_description, :expense_price, :expense_date, :expense_created_by)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':expense_name'			=>	$_POST['expense_name'],
				':expense_description'	=>	$_POST['expense_description'],
                ':expense_price'	    =>	$_POST['expense_price'],
                ':expense_date'         =>  $_POST['expense_date'],
				':expense_created_by'		=>	$_SESSION["user_id"],
			)
		);
		$result = $statement->fetchAll();

		$insertSubQuery = "INSERT INTO tbl_cash_book
		( cash_amount_credit, cash_amount_credit_to, cash_action, cash_received_date)
		VALUES(:cash_amount_credit, :cash_amount_credit_to, :cash_action, :cash_received_date)
		";
		$substatement = $connect->prepare($insertSubQuery);
		$substatement->execute(
			array(
				':cash_amount_credit'		=>	$_POST['expense_price'],
				':cash_amount_credit_to'	=>	$_POST['expense_name'],
				':cash_action'				=>	2,
				':cash_received_date'		=>	$_POST['expense_date'],
			)
		);

		if(isset($result))
		{
			echo 'Expense Added';
		}
	}
	
	if($_POST['btn_action'] == 'expense_details')
	{
		$query = "
		SELECT * FROM tbl_expense 
		INNER JOIN user_details ON user_details.user_id = tbl_expense.expense_created_by 
		WHERE tbl_expense.expense_id = '".$_POST["expense_id"]."'
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
			if($row['expense_status'] == '1')
			{
				$status = '<span class="label label-success">Active</span>';
			}
			else
			{
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$output .= '
			<tr>
				<td>Expense Name</td>
				<td>'.$row["expense_name"].'</td>
			</tr>
			<tr>
				<td>Expense Description</td>
				<td>'.$row["expense_description"].'</td>
			</tr>
			
			<tr>
				<td>Expense Price</td>
				<td>'.$row["expense_price"].'</td>
			</tr>
            <tr>
				<td>Expense Date</td>
				<td>'.$row["expense_date"].'</td>
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
		SELECT * FROM tbl_expense WHERE expense_id = :expense_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':expense_id'	=>	$_POST["expense_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['expense_name'] = $row['expense_name'];
			$output['expense_description'] = $row['expense_description'];
			$output['expense_date'] = $row['expense_date'];
			$output['expense_price'] = $row['expense_price'];
			$output['expense_status'] = $row['expense_status'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$datetime = getDateTime();
		$query = "
		UPDATE tbl_expense 
		set expense_name = :expense_name,
		expense_description = :expense_description, 
		expense_price = :expense_price, 
        expense_date = :expense_date,
        expense_udt = :expense_udt
		WHERE expense_id = :expense_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':expense_name'			=>	$_POST['expense_name'],
				':expense_description'	=>	$_POST['expense_description'],
				':expense_price'		=>	$_POST['expense_price'],
				':expense_date'			=>	$_POST['expense_date'],
				':expense_udt'          => $datetime,
				':expense_id'			=>	$_POST['expense_id']
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Expense Details Edited';
		}
    }
    
	if($_POST['btn_action'] == 'delete')
	{
        $datetime = getDateTime();

		$status = '1';
		if($_POST['status'] == '1')
		{
			$status = '2';
        }
        
		$query = "
		UPDATE tbl_expense 
        SET expense_status = :expense_status,
        expense_edt = :expense_edt
		WHERE expense_id = :expense_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':expense_status'	=>	$status,
				':expense_edt'	    =>	$datetime,
				':expense_id'		=>	$_POST["expense_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Expense status changed';
		}
	}
}


?>