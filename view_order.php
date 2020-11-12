<?php
error_reporting(0);
//view_order.php

if(isset($_GET["pdf"]) && isset($_GET['order_id']))
{
	require_once 'pdf.php';
	include('database_connection.php');
	include('function.php');
	if(!isset($_SESSION['type']))
	{
		header('location:login.php');
	}
	$output = '';
	$statement = $connect->prepare("
		SELECT * FROM inventory_order 
		WHERE inventory_order_id = :inventory_order_id
		LIMIT 1
	");
	$statement->execute(
		array(
			':inventory_order_id'       =>  $_GET["order_id"]
		)
	);
	$result = $statement->fetchAll();
	$total_value = 0;
	foreach($result as $row)
	{
		$total_value = $row['inventory_order_total'];
		$output .= '
		<table width="100%" border="1" cellpadding="5" cellspacing="0">
			<tr>
				<td colspan="2" align="center" style="font-size:18px"><b>Invoice</b></td>
			</tr>
			<tr>
				<td colspan="2">
				<table width="100%" cellpadding="5">
					<tr>
						<td width="65%">
							To,<br />
							<b>RECEIVER (BILL TO)</b><br />
							Name : '.$row["customer_name"].'<br />	
							Mobile Number : '.$row["customer_mobile_no"].'<br />
						</td>
						<td width="35%">
							Reverse Charge<br />
							Invoice No. : '.$row["inventory_order_id"].'<br />
							Invoice Date : '.$row["inventory_order_date"].'<br />
						</td>
					</tr>
				</table>
				<br />
				<table width="100%" border="1" cellpadding="5" cellspacing="0">
					<tr>
						<th>Sr No.</th>
						<th>Product</th>
						<th>Weight</th>
						<th>Price</th>
						<th>Cash Received</th>
						<th>Cash Receivable</th>
						<th>Actual Amt.</th>
						<th>Total</th>
					</tr>
					
		';
		$statement = $connect->prepare("
			SELECT * FROM inventory_order_product 
			WHERE inventory_order_id = :inventory_order_id
		");
		$statement->execute(
			array(
				':inventory_order_id'       =>  $_GET["order_id"]
			)
		);
		$product_result = $statement->fetchAll();
		$count = 0;
		$total = 0;
		$total_actual_amount = 0;
		$total_tax_amount = 0;
		foreach($product_result as $sub_row)
		{
			$count = $count + 1;
			$product_data = fetch_product_details($sub_row['product_id'], $connect);
			$actual_amount = $sub_row['product_amount'];//$sub_row["weight"] * $sub_row["sale_price"];
			
			$total_product_amount = $actual_amount;
			$total_actual_amount = $total_actual_amount + $actual_amount;
			$total = $total + $total_product_amount;

			$output .= '
				<tr>
					<td>'.$count.'</td>
					<td>'.$product_data['product_name'].'</td>
					<td>'.$sub_row["weight"].'</td>
					<td aling="right">'.$sub_row["sale_price"].'</td>
					<td>'.$row["order_cash_received"].'</td>
					<td>'.$row["inventory_order_cash_receivable"].'</td>
					<td align="right">'.number_format($sub_row['product_amount'], 2).'</td>
					
					<td align="right">'.number_format($sub_row['product_amount'], 2).'</td>
				</tr>
			';
		}
		$output .= '
		<tr>
			<td align="right" colspan="6"><b>Total</b></td>
			<td align="right"><b>'.number_format($total_actual_amount, 2).'</b></td>
			<td align="right"><b>'.number_format($total, 2).'</b></td>
		</tr>
		';
		$output .= '
						</table>
						<br />
						<br />
						<br />
						<br />
						<br />
						<br />
						<p align="right">----------------------------------------<br />Receiver Signature</p>
						<br />
						<br />
						<br />
					</td>
				</tr>
			</table>
		';
	}
	$pdf = new Pdf();
	$file_name = 'Order-'.$row["inventory_order_id"].'.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}

?>