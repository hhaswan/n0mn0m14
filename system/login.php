<?php
require_once("Baseconfig.php");
if (!file_exists($hostfilename)){
		echo "<script>
		alert('Please set your connection first!');
		window.location.href='usrv.php';
		</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>.:Login Area</title>
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
                    <h2>Login Page</h2>
                </div>

                <form id="defaultForm" method="post" class="form-horizontal" action="target.php">
                    <div id="alertlogin" class="alert alert-success" style="display: none;"></div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">Username</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" name="username" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Password</label>
                        <div class="col-lg-5">
                            <input type="password" class="form-control" name="password" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-9 col-lg-offset-3">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </form>
				<a href="ureg.php">Register here</a>
            </div>
        </section>
        <!-- :form -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#defaultForm')
        .bootstrapValidator({
            message: 'This value is not valid',
            //live: 'submitted',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                username: {
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The username is required and can\'t be empty'
                        },
                        stringLength: {
                            min: 4,
                            max: 20,
                            message: 'The username must be more than 6 and less than 30 characters long'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9_\.]+$/,
                            message: 'The username can only consist of alphabetical, number, dot and underscore'
                        },
			
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'The password is required and can\'t be empty'
                        }
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
			e.preventDefault();
            var $form     = $(e.target),validator = $form.data('bootstrapValidator');
			var uname = validator.getFieldElements('username').val();
			var pass = validator.getFieldElements('password').val();
            $.post("Setting.php",{ulogin: uname, pass:pass},function(output){
				if(output == "0"){
					$form.find('#alertlogin').html("Wrong username or password! Please Try Again!").show();
				}
				else{
					var tablename = undefined;
					window.location.href = '../application/index.php?token='+output;
				}
				//$form.find('#alertlogin').html(output).show();
			});
        });
});
</script>
</body>
</html>