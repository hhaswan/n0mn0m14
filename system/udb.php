<?php
require_once("Baseconfig.php");
if (!file_exists($hostfilename)){
		echo "<script>
		alert('Please set your connection first!');
		window.location.href='usrv.php';
		</script>";
	}
else {
	require_once($hostfilename);
	if(!mysql_connect($_MYSQLHOST,$_MYSQLUSER,$_MYSQLPASS)){
		echo "<script>
			alert('Please fix your connection first!');
			window.location.href='usrv.php';
			</script>";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>.:Administrasi Database</title>
	<?php include_once("ui.php") ?>
</head>
<body>
<div class="container">
    <div class="row">
	<?php
	include_once("uimenu.php");
	?>
        <!-- form: -->
        <section>
            <div class="col-lg-8 col-lg-offset-2">
                <div class="page-header">
                    <h2>Update Your DB Name</h2>
                </div>

                <form id="formdb" method="post" class="form-horizontal" action="target.php">
                    <div id="alertdb" class="alert alert-danger" style="display: none;"></div>
					<?php
						$keyfilename = "prefix.txt";
						$lines = file($keyfilename);
					if(!empty($lines[0])){
					$prefix = trim($lines[0]);
					echo "
					<div class='form-group'>
                        <label class='col-lg-3 control-label'>Prefix</label>
                        <div class='col-lg-5'>
                            <input type='text' value='$prefix' disabled='disabled' class='form-control' name='prefix' />
                        </div>
                    </div>";
					}
					else {
						echo "<input type='text' value='' style='display:none;' class='form-control' name='prefix' />";
					}
					?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Database Name</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" name="dbname" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-9 col-lg-offset-3">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <!-- :form -->
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#formdb').bootstrapValidator({
            //live: 'submitted',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                dbname: {
                    validators: {
                        notEmpty: {
                            message: 'The dbname is required and can\'t be empty'
                        },
						/* pengecekan nama db realtime lewat variabel isValid 
                        remote: {
                            url: 'Setting.php',
							type: 'POST',
							message: 'No such database available',
                        },
						*/
                    }
                },
            }
        })
        .on('success.form.bv', function(e) {
            e.preventDefault();
			var $form = $(e.target), validator = $form.data('bootstrapValidator');
            var prefix = validator.getFieldElements('prefix').val();
			var db = validator.getFieldElements('dbname').val();
			if(prefix != '') var dbname = prefix+'_'+db;
			else var dbname = db;
            $.post("Setting.php",{dbname: dbname},function(output){
			//$.post("Setting.php",{isSaved: true, dbname: validator.getFieldElements('dbname').val()},function(output){
				var tablename = undefined; //set status ke home
			    $form.find('#alertdb').html(output).show();
			});
        });		
});
</script>
</body>
</html>