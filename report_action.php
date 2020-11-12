<?php 
error_reporting(0);
include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
    if($_POST['btn_action'] == 'fetchtotalcompletedorder'){

		$query .= "	SELECT * FROM inventory_order WHERE inventory_order_status='1' AND ";

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(inventory_order_date) = "'.$month.'" AND YEAR(inventory_order_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
        echo $statement->rowCount();
    }

    if($_POST['btn_action'] == 'fetchtotalpendingorder'){

		$query .= "	SELECT * FROM inventory_order WHERE inventory_order_status='2' AND ";

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(inventory_order_date) = "'.$month.'" AND YEAR(inventory_order_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
        echo $statement->rowCount();
    }

    if($_POST['btn_action'] == 'fetchtotalsaleprice'){

		$query .= "	SELECT sum(inventory_order_total) as total_order_value FROM inventory_order 
            WHERE (inventory_order_status='1' OR inventory_order_status = '2') AND ";

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(inventory_order_date) = "'.$month.'" AND YEAR(inventory_order_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $row)
        {
            echo number_format($row['total_order_value'], 2);
        }    
    }

    if($_POST['btn_action'] == 'fetchtotalbaseprice'){

		$query .= "	SELECT sum(inventory_order_actual_total) as total_base_order_value FROM inventory_order 
            WHERE (inventory_order_status='1' OR inventory_order_status = '2') AND ";

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(inventory_order_date) = "'.$month.'" AND YEAR(inventory_order_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $row)
        {
            echo number_format($row['total_base_order_value'], 2);
        }    
    }

    if($_POST['btn_action'] == 'fetchtotalweightin'){

		$query .= "	SELECT sum(product_total_weight) as total_weight_in, product_unit FROM product 
            WHERE ";

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(product_date) = "'.$month.'" AND YEAR(product_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $row)
        {
            echo number_format($row['total_weight_in'], 2)." ". $row['product_unit'];
        }    
    }

    if($_POST['btn_action'] == 'fetchtotalweightout'){

		$query .= "	SELECT sum(product_weight_sold) as total_weight_out, product_unit FROM product 
            WHERE ";

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(product_date) = "'.$month.'" AND YEAR(product_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $row)
        {
            echo number_format($row['total_weight_out'], 2)." ". $row['product_unit'];
        }    
    }

    if($_POST['btn_action'] == 'fetchtotalcashreceived'){

		$query .= "SELECT sum(order_cash_received) as total_cash_received FROM inventory_order 
        WHERE (inventory_order_status ='1' OR inventory_order_status = '2') AND ";

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(inventory_order_date) = "'.$month.'" AND YEAR(inventory_order_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $row)
        {
            echo number_format($row['total_cash_received'], 2);
        }    
    }

    if($_POST['btn_action'] == 'fetchtotalcashreceivable'){

		$query .= "SELECT sum(inventory_order_cash_receivable) as total_cash_receivable FROM inventory_order 
        WHERE (inventory_order_status ='1' OR inventory_order_status = '2') AND ";

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(inventory_order_date) = "'.$month.'" AND YEAR(inventory_order_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $row)
        {
            echo number_format($row['total_cash_receivable'], 2);
        }    
    }

    if($_POST['btn_action'] == 'fetchtotalprofit'){

		$query .= "SELECT sum(inventory_order_product.product_profit) as totalprofit
        FROM inventory_order
        LEFT JOIN inventory_order_product ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
        Where (inventory_order.inventory_order_status='1' OR inventory_order.inventory_order_status='2' ) AND "
        ;

		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

			$query .= 'MONTH(inventory_order_date) = "'.$month.'" AND YEAR(inventory_order_date) ="'.$year.'"';
		}

		//print_r($query);exit();

		$statement = $connect->prepare($query);
		$statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $row)
        {
            echo number_format($row['totalprofit'], 2);
        }   
    }

    if($_POST['btn_action'] == 'fetchtotalprofitwithexpense'){

        
		if(isset($_POST["monthName"]))
		{
			$month = getMonth($_POST["monthName"]);
			$year = date("Y");

            $query .= "SELECT  (SELECT sum(inventory_order_product.product_profit)
             as totalprofit FROM inventory_order
            LEFT JOIN inventory_order_product
            ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
            Where (inventory_order.inventory_order_status='1'
            OR inventory_order.inventory_order_status='2' )
            AND  MONTH(inventory_order_date) = ".$month." AND YEAR(inventory_order_date) = ".$year.")
            - (SELECT SUM(tbl_expense.expense_price)
            FROM tbl_expense WHERE MONTH(tbl_expense.expense_date) = ".$month."
            AND YEAR(tbl_expense.expense_date) = ".$year.") AS totalprofitwithoutexpense
            ";
            
            //print_r($query);exit();
            $statement = $connect->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            foreach($result as $row)
            {
                echo number_format($row['totalprofitwithoutexpense'], 2);
            }
        }   
    }
}