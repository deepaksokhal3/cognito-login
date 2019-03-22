<?php include('view/common/header.php'); 
    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();
	try {
		$user = $client->poolclient();
	} catch (Exception $e) {
		$msg->error("An error occurred: " . $e->getMessage());
	}
  ?>
    <div class="container">
        <br/>
        <br/>
        <h2> AWS USERS POOL CLIENT`S</h2>
        <?php  $msg->display(); ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
				    	if(isset($user['Users'])):
				    	foreach ($user['Users'] as $key => $user):?>
                        <tr>
                            <td>
                                <?=  $user['Attributes'][2]['Value'] ?>
                            </td>
                            <td>
                                <?=  $user['UserCreateDate']->format('d M,Y') ?>
                            </td>
                            <td>
                                <?=  $user['UserStatus'] ?>
                            </td>
                        </tr>
                        <?php endforeach; 
                    else:
                        echo '<tr><td>Please login</td></tr>';
                    endif;?>
                </tbody>
            </table>
            <?php 
            if($user): ?>
            <div class="row col-md-12">
            	<a class="btn btn-primary btn-block text-center" href="<?= $_RVER['HTTP_ORIGIN']."/cognito-login/logout.php"?>" style="color: #ffffff;">Logout</a>
	        <a class="btn btn-danger btn-block text-center" href="<?= $_RVER['HTTP_ORIGIN']."/cognito-login/delete.php"?>" style="color: #ffffff;">Delete</a>
	        </div>
        <?php endif; ?>
    </div>
    <!--container end.//-->
    <?php  include('view/common/footer.php'); ?>