<?php include('view/common/header.php'); 
    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();

    if(isset($_GET['uid']) && isset($_GET['action'])){
        try{
            if($_GET['action'] == 'delete'){  
                $res = $client->deleteUser($_GET['uid']);
                $txtMessage = 'User deleted successfully.';
            }
            else if($_GET['action'] == 'disable'){ // disable spacific user
                $res =  $client->disableUser($_GET['uid']);
                $txtMessage = 'User disable successfully.';
            }
            else if($_GET['action'] == 'enable'){ // enable spacific user
                $res =  $client->enableUser($_GET['uid']);
                $txtMessage = 'User enable successfully.';
            }
            if(!is_string($res))
                $msg->success($txtMessage);
            else
                $msg->error($res);
        }catch(Exception $e){
            $msg->error("An error occurred: " . $e->getMessage());
        }
    }

    try {
        $users = $client->buildAdminFormatedObject($client->poolclient());
    } catch (Exception $e) {
        $msg->error("An error occurred: " . $e->getMessage());
    }
  ?>
    <div class="container">
        <br/>
        <br/>
        <h2> AWS USERS POOL CLIENT`S <a class="float-right" href="<?= $_SERVER['HTTP_ORIGIN']."/cognito-login/changePassword.php"?>"><small><i class="fa fa-key" aria-hidden="true"></i>Change Password</a></small></h2>
        <?php  $msg->display(); ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
				    	if($users):
				    	foreach ($users as $key => $user):
                            $action = $user['Enabled']?'disable':'enable';
                            $icon = $user['Enabled']?'lock-open':'lock';
                            ?>
                        <tr>
                            <td>
                                <?=  $user['email'] ?>
                            </td>
                            <td>
                                <?=  $user['UserCreateDate']->format('d M,Y') ?>
                            </td>
                            <td>
                                <?=  $user['UserStatus'] ?>
                            </td>
                             <td>
                                <a href="<?= $_SERVER['HTTP_ORIGIN'].'/cognito-login/profile.php?uid='.$user['Username']?>"><i class="fa fa-edit" ></i></a>
                              <a href="<?= $_SERVER['HTTP_ORIGIN'].'/cognito-login/members.php?action=delete&uid='.$user['Username']?>"><i class="fa fa-trash" ></i></a>
                              <a href="<?= $_SERVER['HTTP_ORIGIN'].'/cognito-login/members.php?action='.$action.'&uid='.$user['Username']?>"><i class="fa fa-<?= $icon?>" ></i></a>
                            </td>
                        </tr>
                        <?php endforeach; 
                    else:
                        echo '<tr><td>No Records Found</td></tr>';
                    endif;?>
                </tbody>
            </table>
            <?php 
            if(isset($_SESSION['AccessToken'])): ?>
            <div class="row col-md-12">
            	<a class="btn btn-primary btn-block text-center" href="<?= $_SERVER['HTTP_ORIGIN']."/cognito-login/logout.php"?>" style="color: #ffffff;">Logout</a>
	        </div>
        <?php else:?>
            <div class="border-top card-body text-center">Have an account? <a href="<?= $_SERVER['HTTP_ORIGIN']."/cognito-login"?>">Log In</a></div>
       <?php endif; ?>
    </div>
    <!--container end.//-->
    <?php  include('view/common/footer.php'); ?>