
<?php 
error_reporting(0);
include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
	if($_POST['amount_action'] == 2){
		$query = "
			INSERT INTO tbl_cash_book (cash_amount_credit, cash_amount_credit_to, cash_action,
			cash_received_date) 
			VALUES (?, ?, ?, ?)
			";
		$statement = $connect->prepare($query);

		$statement->execute(
			array(
				$_POST['credit_amount'],
				$_POST['credit_name'],
				$_POST['amount_action'],
				$_POST['credit_date'],
			)
		);
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$cashid = $statement->fetchColumn();
		if($cashid)
		{
			echo 'Credit Added';
		}
		else{
			echo 'Credit Not Added';
		}
	}

	if($_POST['amount_action'] == 1){
		$query = "
			INSERT INTO tbl_cash_book (cash_amount_received, cash_received_from, cash_action,
			cash_received_date) 
			VALUES (?, ?, ?, ?)
			";
		$statement = $connect->prepare($query);

		$statement->execute(
			array(
				$_POST['credit_amount'],
				$_POST['credit_name'],
				$_POST['amount_action'],
				$_POST['credit_date'],
			)
		);
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$cashid = $statement->fetchColumn();
		if($cashid)
		{
			echo 'Credit Added';
		}
		else{
			echo 'Credit Not Added';
		}
	}

	if($_POST['btn_action'] == 'fetchinfo'){

		$query .= "SELECT SUM(cash_amount_received) as totaldebit, 
			SUM(cash_amount_credit) as totalcredit from tbl_cash_book WHERE ";

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(cash_received_date) = "'.$month.'" AND YEAR(cash_received_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();

		$output = '
			<div class="table-responsive">
				<table class="table table-bordered table-striped">
					<tr>
						<th>Total Debit</th>
						<th>Total Credit</th>
						<th>Cash On Hand</th>

					</tr>
		';

		$cash_on_hand = 0;
		foreach($result as $row)
		{
			$cash_on_hand = $row["totaldebit"] - $row["totalcredit"];
			$output .= '
			<tr>
				<td align="right"> '.$row["totaldebit"].'</td>
				<td align="right"> '.$row["totalcredit"].'</td>
				<td align="right"> '.$cash_on_hand.'</td>

			</tr>
			';
		}
		$output .= '
			</table></div>
		';
		echo $output;
	}
}