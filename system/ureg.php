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
	<meta charset="UTF-8"/>
	<title>.:Register NomNomDB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />    
	<?php include_once("ui.php") ?>
<script>
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
                ureg: {
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
						remote: {
                            url: 'Setting.php',
							type: 'POST',
							message: 'Username already used'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'The email address is required and can\'t be empty'
                        },
                        emailAddress: {
                            message: 'The input is not a valid email address'
                        }
                    }
                },
	            password: {
					validators: {
						notEmpty: {
							message: 'The password is required and can\'t be empty'
						},
						stringLength: {
                            max: 6,
                            message: 'The password must be less than 6 characters long'
                        },
						identical: {
							field: 'confirmPassword',
							message: 'The password and its confirm are not the same'
						},
						different: {
							field: 'username',
							message: 'The password can\'t be the same as username'
						}
					}
				},
				confirmPassword: {
					validators: {
						notEmpty: {
							message: 'The confirm password is required and can\'t be empty'
						},
						identical: {
							field: 'password',
							message: 'The password and its confirm are not the same'
						}
					}
				},

                key: {
                    validators: {
                        notEmpty: {
                            message: 'Key is required and can\'t be empty'
                        },
						stringLength: {
                            max: 6,
                            message: 'The key must be less than 6 characters long'
                        },
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            // Prevent submit form
            e.preventDefault();
			
            var $form = $(e.target), validator = $form.data('bootstrapValidator');
			var ureg = validator.getFieldElements('ureg').val();
			var umail = validator.getFieldElements('email').val();
			var pass = validator.getFieldElements('password').val();
			var key = validator.getFieldElements('key').val();
			
            $.post("Setting.php",{isSaved: true, pword:pass, ureg:ureg, umail:umail, key: key},function(output){
				var text;
				var divtext = $form.find('#alertreg');
				if(output == '1') {
					divtext.removeClass('alert-danger').addClass('alert-success');
					text = "Congratulation! Your username '"+ureg+ "' registered. Please login <a href='login.php'>here</a> with your new user";
				}
				else{
					divtext.removeClass('alert-success').addClass('alert-danger');
					text = "Please Try Again";
				}
				divtext.html(text).show();
			});
        });
});
</script>
</head>
<body>
<div class="container">
    <div class="row">
        <!-- form: -->
        <section>
            <div class="col-lg-8 col-lg-offset-2">
                <div class="page-header">
                    <h2>Register User</h2>
                </div>

                <form id="defaultForm" method="post" class="form-horizontal" action="">
                    <div id="alertreg" class="alert" style="display: none;"></div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">Username</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" name="ureg" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">Email address</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" name="email" />
                        </div>
                    </div>
					
					<div class="form-group">
                            <label class="col-lg-3 control-label">Password</label>
                            <div class="col-lg-5">
                                <input type="password" class="form-control" name="password" />
                            </div>
                        </div>

					<div class="form-group">
						<label class="col-lg-3 control-label">Retype password</label>
						<div class="col-lg-5">
							<input type="password" class="form-control" name="confirmPassword" />
						</div>
					</div>
				
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Key</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" name="key" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-9 col-lg-offset-3">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <!-- :form -->
    </div>
</div>
</body>
</html>