<?php include('view/common/header.php'); 

    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();
    $user = $client->buildFormatedObject($client->getUser($_GET['uid']));
    if(isset($_POST['submit'])):
		$attributes =[
						"email" => isset($_POST['email'])?$_POST['email']:'',
		    			"custom:Fname"=> isset($_POST['fname'])?$_POST['fname']:'',
		    			"custom:Lname"=> isset($_POST['lname'])?$_POST['lname']:'',
					];
    	$client->updateUserAttributes($user['Username'],$attributes);
    	$msg->success('Profile updated succssfully.');

    endif;
    $user = $client->buildFormatedObject($client->getUser($_GET['uid']));

?>
<div class="container">
    <br>
    <br>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
            	<div class="col form-group">
            		<a class="float-right" href="<?= $_SERVER['HTTP_ORIGIN'].'/cognito-login'?>"><i class="fa fa-home"></i></a>
            	</div>
                <article class="card-body">
                    <?php  $msg->display(); ?>

                    <form class="was-validated" action="" method="post">

                        <div class="form-group">
                            <label>Email address / Username</label>
                            <input type="email" name="email" class="form-control" value="<?= isset($user['email'])?$user['email']:''?>" placeholder="" required>
                        </div>
                        <div class="form-row">
	                        <div class="col form-group">
	                            <label>First Name</label>
	                            <input class="form-control" name="fname" type="text" value="<?= isset($user['custom:Fname'])?$user['custom:Fname']:''?>" required>
	                        </div>
	                        <div class="col form-group">
	                            <label>Last Name</label>
	                            <input class="form-control" name="lname" type="text" value="<?= isset($user['custom:Lname'])?$user['custom:Lname']:''?>" required>
	                        </div>
	                    </div>
                        <!-- form-group end.// -->
                        <div class="form-group">
                            <button type="submit" name="submit" class="btn btn-primary btn-block"> Update </button>
                        </div>
                    </form>
                </article>
            </div>
            <!-- card.// -->
        </div>
        <!-- col.//-->

    </div>
    <!-- row.//-->

</div>
<!--container end.//-->
<?php  include('view/common/footer.php'); ?>