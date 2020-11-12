<?php
//expense.php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');
?>

    <link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>

	<script>
	$(document).ready(function(){
		$('#expense_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	</script>

<span id="alert_action"></span>
	<div class="row">
		<div class="col-lg-12">
			
			<div class="panel panel-default">
                <div class="panel-heading">
                	<div class="row">
                    	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title">Expense List</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Add</button>    	
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                	<table id="expense_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Expense ID</th>
								<th>Expense Name</th>
								<th>Expense Price</th>
								<th>Expense Date</th>
								<th>Expense Created By</th>
								<!-- <th>Expense Status</th> -->
								<th>Expense Enter Date</th>
								<th></th>
								<!-- <th></th>
								<th></th> -->
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>

    <div id="expenseModal" class="modal fade">

    	<div class="modal-dialog">
    		<form method="post" id="expense_form" autocomplete="off">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Create Expense</h4>
    				</div>
    				<div class="modal-body">
    					<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Enter Expense Name</label>
									<input type="text" name="expense_name" id="expense_name" class="form-control" required />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Date</label>
									<input type="text" name="expense_date" id="expense_date" class="form-control" required />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Enter Expense Description</label>
							<textarea name="expense_description" id="expense_description" class="form-control"></textarea>
						</div>
						<div class="form-group">
							<label>Expense Price</label>
                            <input type="text" name="expense_price" id="expense_price" class="form-control" required />
						</div>
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="expense_id" id="expense_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
    				</div>
    			</div>
    		</form>
    	</div>

    </div>

    <div id="expensedetailsModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="expense_detail_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Expense Details</h4>
                        </div>
                        <div class="modal-body">
                            <Div id="expense_details"></Div>
                        </div>
                        <div class="modal-footer">
                            
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
		
<script type="text/javascript">
    $(document).ready(function(){

    	var orderdataTable = $('#expense_data').DataTable({
			"processing":true,
			"serverSide":true,
			"order":[],
			"ajax":{
				url:"expense_fetch.php",
				type:"POST"
			},
			"pageLength": 10
		});

		$('#add_button').click(function(){
			$('#expenseModal').modal('show');
			$('#expense_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create Expense");
			$('#action').val('Add');
			$('#btn_action').val('Add');
		});

		var count = 0;

		$(document).on('submit', '#expense_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"expense_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#expense_form')[0].reset();
					$('#expenseModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					orderdataTable.ajax.reload();
				}
			});
		});

        $(document).on('click', '.view', function(){
        var expense_id = $(this).attr("id");
        var btn_action = 'expense_details';
        $.ajax({
            url:"expense_action.php",
            method:"POST",
            data:{expense_id:expense_id, btn_action:btn_action},
            success:function(data){
                $('#expensedetailsModal').modal('show');
                $('#expense_details').html(data);
            }
        })
    });

		$(document).on('click', '.update', function(){
			var expense_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url:"expense_action.php",
				method:"POST",
				data:{expense_id:expense_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					$('#expenseModal').modal('show');
					$('#expense_name').val(data.expense_name);
					$('#expense_date').val(data.expense_date);
					$('#expense_description').val(data.expense_description);
					$('#expense_price').val(data.expense_price);
					$('#order_status').val(data.expense_status);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Expense");
					$('#expense_id').val(expense_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			})
		});

		$(document).on('click', '.delete', function(){
			var expense_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";
			if(confirm("Are you sure you want to change status?"))
			{
				$.ajax({
					url:"expense_action.php",
					method:"POST",
					data:{expense_id:expense_id, status:status, btn_action:btn_action},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						orderdataTable.ajax.reload();
					}
				})
			}
			else
			{
				return false;
			}
		});

    });
</script>

<?php
include('footer.php');
?>