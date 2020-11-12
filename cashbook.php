<?php
//credit.php

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
		$('#credit_date').datepicker({
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
                    	<div class="col-lg-8 col-md-8 col-sm-4 col-xs-4">
                            <h3 class="panel-title">Cash Book</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Add</button>    	
                        </div>
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" align="right">
						<select id = "month_name" name = "month_name" class="form-control">
							<option value = ''>Select Month</option>
							<?php echo fill_month_list($connect);?>
						</select>
                        </div>
                    </div>
                </div>
                <div class="panel-body hide" id = "showtable">
                	<table id="cash_data" class="table table-bordered table-striped">
                		<thead>
                            <th>Action</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Amount</th>
                            <th>Action</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Amount</th>

							<!-- <tr colspan = "6">
							    <th colspan = "6">Debit</th>
                            
                                <th colspan = "6">Credit</th>
							</tr>
                            <tr colspan = "6">
                                <th colspan = "2">Date</th>
                                <th colspan = "2">Particulars</th>
                                <th colspan = "2">Amount</th>

                                <th colspan = "2">Date</th>
                                <th colspan = "2">Particulars</th>
                                <th colspan = "2">Amount</th>
                                
                            </tr> -->
						</thead>
                	</table>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong>Detail of Debit / Credit</strong></div>
                            <div class="panel-body" align="center" id = "debit_credit_info">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="cashModal" class="modal fade">

        <div class="modal-dialog">
            <form method="post" id="cash_form" autocomplete="off">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i>Add Credit Amount</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Enter Person Name / Description</label>
                                    <input type="text" name="credit_name" id="credit_name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="text" name="credit_date" id="credit_date" class="form-control" required />
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label> Amount</label>
                            <input type="text" name="credit_amount" id="credit_amount" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label>Action</label>
                            <select name="amount_action" class="form-control" id="amount_action" required>
                                <option value="1">Debit</option>
                                <option value="2">Credit</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="credit_id" id="credit_id" />
                        <input type="hidden" name="btn_action" id="btn_action" />
                        <input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
                    </div>
                </div>
            </form>
        </div>

    </div>

		
<script type="text/javascript">
    $(document).ready(function(){

    	// var orderdataTable = $('#credit_data').DataTable({
		// 	"processing":true,
		// 	"serverSide":true,
		// 	"order":[],
		// 	"ajax":{
		// 		url:"credit_fetch.php",
		// 		type:"POST"
		// 	},
		// 	"pageLength": 10
		// });
    });

    $('#add_button').click(function(){
		$('#cashModal').modal('show');
		$('#cash_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Credit Cash");
		$('#action').val('Add');
		$('#btn_action').val('Add');
	});

    $(document).on('submit', '#cash_form', function(event){
		event.preventDefault();
		$('#action').attr('disabled', 'disabled');
		var form_data = $(this).serialize();
		$.ajax({
			url:"cashbook_action.php",
			method:"POST",
			data:form_data,
			success:function(data){
				$('#cash_form')[0].reset();
				$('#cashModal').modal('hide');
				$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
				$('#action').attr('disabled', false);
			}
		});
	});


	$('#month_name').change(function(){

        $('#debit_credit_info').html('');

		var value = $(this).val();
		$('#showtable').removeClass('hide');
		$('#showtable').show();
		
		var orderdataTable = $('#cash_data').DataTable({
			dom: 'Blfrtip',
        	buttons: [
            	'excelHtml5',
            	'pdfHtml5'
        	],
			"processing":true,
			"serverSide":true,
			"bDestroy": true,
			"order":[],
			"ajax":{
				url:"cashbook_fetch.php",
				type:"POST",
				"data" : {
            	"monthName" : value,
				}
			},
			"pageLength": 10
		});

        $.ajax({
            url:"cashbook_action.php",
			method:"POST",
			data:{
                "monthName":value, btn_action: "fetchinfo"
            },
			success:function(data){
                $('#debit_credit_info').append(data);
            }
        });
	});
</script>

<?php
include('footer.php');
?>