<?php
//order.php

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
		$('#inventory_order_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
		$('#cash_received_date').datepicker({
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
                            <h3 class="panel-title">Order List</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Add</button>    	
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                	<table id="order_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Order ID</th>
								<th>Customer Name</th>
								<th>Total Amount</th>
								<th>Cash Receivable</th>
								<th>Profit</th>
								<th>Payment Mode</th>
								<th>Order Status</th>
								<th>Order Date</th>
								<?php
								if($_SESSION['type'] == 'master')
								{
									echo '<th>Created By</th>';
								}
								?>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
	</div>
	
	<div id="orderdetailsModal" class="modal fade">
        <div class="modal-dialog">
        	<form method="post" id="order_detail_form" autocomplete="off">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i> Order Details</h4>
                    </div>
                    <div class="modal-body">
                        <Div id="order_details"></Div>
                    </div>
                    <div class="modal-footer">     
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="orderModal" class="modal fade">

    	<div class="modal-dialog1">
			<div class="modal-content1">

    		<form method="post" id="order_form" autocomplete="off">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Create Order</h4>
    				</div>
    				<div class="modal-body">
    					<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Enter Receiver Name</label>
									<input type="text" name="inventory_order_name" id="inventory_order_name" class="form-control" required />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Date</label>
									<input type="text" name="inventory_order_date" id="inventory_order_date" class="form-control" required />
								</div>
							</div>
						</div>
						<div class = "row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Enter Receiver Mobile Number</label>
									<input type="text" name="customer_mobile_no" id="customer_mobile_no" class="form-control" required pattern="[+-]?([0-9]*[.])?[0-9]+" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Enter Product Details</label>
							<hr />
							<span id="span_product_details"></span>
							<hr />
						</div>
						
						<div class="form-group">
							<label>Cash Received</label>
							<input type="text" name="cash_received" id="cash_received" class="form-control" value = "0" />
						</div>
						<div class="form-group">
							<label>Cash Received At (Bank Name)/ By Hand</label>
							<input type="text" name="cash_received_at" id="cash_received_at" class="form-control" />
						</div>
						<div id = "for_edit">
							<div class="form-group" >
								<label>Cash Received Date</label>
								<input type="text" name="cash_received_date" id="cash_received_date" class="form-control" />
							</div>
							<div class="form-group"  >
								<label>Cash Remaining</label>
								<input type="text" name="cash_remaining" id="cash_remaining" class="form-control" readonly />
							</div>
						</div>
						<div class="form-group">
							<label>Select Payment Mode</label>
							<select name="payment_mode" id="payment_mode" class="form-control">
								<option value="cash">Cash</option>
								<option value="credit">Credit</option>
							</select>
						</div>
						<div class="form-group">
							<label>Select Payment Status</label>
							<select name="order_status" id="order_status" class="form-control">
								<option value="1">Completed</option>
								<option value="2">In Progress</option>
								<option value="3">InActive</option>
							</select>
						</div>
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="inventory_order_id" id="inventory_order_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
    				</div>
    			</div>
    		</form>
    	</div>

    </div>
		
<script type="text/javascript">
    $(document).ready(function(){

    	var orderdataTable = $('#order_data').DataTable({
			"processing":true,
			"serverSide":true,
			"order":[],
			"ajax":{
				url:"order_fetch.php",
				type:"POST"
			},
			<?php
			if($_SESSION["type"] == 'master')
			{
			?>
			"columnDefs":[
				{
					"targets":[4, 5, 6, 7, 8, 9],
					"orderable":false,
				},
			],
			<?php
			}
			else
			{
			?>
			"columnDefs":[
				{
					"targets":[4, 5, 6, 7, 8],
					"orderable":false,
				},
			],
			<?php
			}
			?>
			"pageLength": 10
		});

		$('#add_button').click(function(){
			$("#inventory_order_date").attr("disabled", false);
			$('#orderModal').modal('show');
			$('#for_edit').hide();
			$('#order_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create Order");
			$('#action').val('Add');
			$('#btn_action').val('Add');
			$('#span_product_details').html('');
			add_product_row();
		});

		function add_product_row(count = '')
		{
			var html = '';
			html += '<span id="row'+count+'"><div class="row">';
			html += '<div class="col-md-2"> Product Name';
			html += '<select name="product_id[]" id="product_id'+count+'" class="form-control selectpicker" data-live-search="true" required>';
			html += '<option value="" selected>Select Product</option>';
			html += '<?php echo fill_product_list($connect); ?>';
			html += '</select><input type="hidden" name="hidden_product_id[]" id="hidden_product_id'+count+'" />';
			html += '</div>';
			html += '<div class="col-md-1">Pieces';
			html += '<input type="text" id="quantity'+count+'" name="quantity[]" class="form-control" required />';
			html += '</div>';
			html += '<div class="col-md-2">Pieces Left';
			html +=	'<input type="text" name="hidden_quantity[]" class="form-control" id="hidden_quantity'+count+'" readonly />';
			html += '</div>';
			html += '<div class="col-md-1">Price';
			html += '<input type="text" name="sale_price[]" id="sale_price'+count+'" class="form-control" required />';
			html += '</div>';
			html += '<div class="col-md-2">Total Amount';
			html += '<input type="text" name="amount_total[]" id="amount_total'+count+'" class="form-control" readonly />';
			html += '</div>';
			html += '<div class="col-md-1">';
			if(count == '')
			{
				html += '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			}
			else
			{
				html += '<button type="button" name="remove" id="'+count+'" class="btn btn-danger btn-xs remove">-</button>';
			}
			html += '</div>';
			html += '</div></div><br /></span>';
			$('#span_product_details').append(html);

			$('.selectpicker').selectpicker();

			$('#product_id'+count).change(function(){
				var product_id = $(this).val();
				var btn_action = 'fetch_productweight';

				$.ajax({
					type:'POST',
					data:{product_id: product_id, btn_action: btn_action},
					url:'order_action.php',
					dataType:"json",
					success:function(data){
						$('#hidden_quantity'+count).val(data.product_weight_remaining);
					}
				});
			});

			$('#quantity'+count).change(function(){
				var quantity = $('#quantity'+count).val();
				quantity = parseInt(quantity);

				var hidden_quantity = $('#hidden_quantity'+count).val();
				hidden_quantity = parseInt(hidden_quantity);

				if(quantity > hidden_quantity)
					alert("Please Enter Correct Quantity");
			});

			$('#sale_price'+count).change(function(){
				var weight = $('#quantity'+count).val();
				var sale_price = $('#sale_price'+count).val();
				var btn_action = 'fetch_producttotal';

				$.ajax({
					type:'POST',
					data:{ weight:weight, sale_price:sale_price, btn_action: btn_action},
					url:'order_action.php',
					dataType:"json",
					success:function(data){
						$('#amount_total'+count).val(data);
					}
				});
			});
		}

		var count = 0;

		$(document).on('click', '#add_more', function(){
			count = count + 1;
			add_product_row(count);
		});
		$(document).on('click', '.remove', function(){
			var row_no = $(this).attr("id");
			$('#row'+row_no).remove();
		});

		$(document).on('submit', '#order_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"order_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#order_form')[0].reset();
					$('#orderModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					orderdataTable.ajax.reload();
				}
			});
		});

	$(document).on('click', '.view', function(){
        var order_id = $(this).attr("id");
        var btn_action = 'order_details';
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{order_id:order_id, btn_action:btn_action},
            success:function(data){
                $('#orderdetailsModal').modal('show');
                $('#order_details').html(data);
            }
        })
    });

		$(document).on('click', '.update', function(){
			var inventory_order_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url:"order_action.php",
				method:"POST",
				data:{inventory_order_id:inventory_order_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					$("#inventory_order_date").attr("disabled", true);
					$('#orderModal').modal('show');
					$('#for_edit').show();
					$('#inventory_order_name').val(data.inventory_order_name);
					$('#inventory_order_date').val(data.inventory_order_date);
					$('#customer_mobile_no').val(data.customer_mobile_no);
					$('#span_product_details').html(data.product_details);
					$('#cash_remaining').val(data.cash_remaining);
					$('#payment_mode').val(data.payment_mode);
					$('#order_status').val(data.inventory_order_status);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Order");
					$('#inventory_order_id').val(inventory_order_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			})
		});

		$(document).on('click', '.delete', function(){
			var inventory_order_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";
			if(confirm("Are you sure you want to change status?"))
			{
				$.ajax({
					url:"order_action.php",
					method:"POST",
					data:{inventory_order_id:inventory_order_id, status:status, btn_action:btn_action},
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