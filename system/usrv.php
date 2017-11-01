<!DOCTYPE html>
<html>
<head>
    <title>.:Adminstrasi Info Server</title>
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
                    <h2>Update Your MySQL Server Info</h2>
                </div>

                <form id="formsrv" method="post" class="form-horizontal" action="target.php">
                    <div id="alertsrv" class="alert" style="display: none;"></div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">MySQL Host</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" name="host" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">MySQL User</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" name="user" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">MySQL Password</label>
                        <div class="col-lg-5">
                            <input type="password" class="form-control" name="password" />*Leave it blank if there is no MySQL password
                        </div>
                    </div>

					<div class="form-group">
                        <label class="col-lg-3 control-label">Prefix Database Name</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" name="prefix" />*Prefix usually used for hosting purpose only
                        </div>
						
                    </div>
					
                    <div class="form-group">
                        <div class="col-lg-9 col-lg-offset-3">
                            <button type="submit" class="btn btn-primary">Connect</button>
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
    $('#formsrv')
        .bootstrapValidator({
            message: 'This value is not valid',
            //live: 'submitted',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                host: {
                    validators: {
                        notEmpty: {
                            message: 'The host is required and can\'t be empty'
                        },
                    }
                },
                user: {
                    validators: {
                        notEmpty: {
                            message: 'The username is required and can\'t be empty'
                        },
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            // Prevent submit form
            e.preventDefault();
            var $form = $(e.target),validator = $form.data('bootstrapValidator');
			var host = validator.getFieldElements('host').val();
			var user = validator.getFieldElements('user').val();
			var pass = validator.getFieldElements('password').val();
			var prefix = validator.getFieldElements('prefix').val();
            $.post("Setting.php",{host:host, huser:user, hpass: pass, hprefix: prefix},function(output){
				var text;
				var divtext = $form.find('#alertsrv');
				if(output == '1') {
					//divtext.removeClass('alert-danger').addClass('alert-success');
					text = "Congratulation! Nomnom is already installed. Proceed to the application";
					alert(text);
					window.location.href = "../application/";
				}
				else{
					divtext.removeClass('alert-success').addClass('alert-danger');
					text = "Can't Connect to Your DB Server! Please Try Again";
					divtext.html(text).show();
				}
			});
			
			
        });
});
</script>
</body>
</html>