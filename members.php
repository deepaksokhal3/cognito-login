<?php include('view/common/header.php'); 
    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();
    $groups = $client->getGroups();   
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
        <h2> AWS USERS POOL CLIENT`S </h2>
        <div class="col-md-12">
        <?php  if(isset($_SESSION['AccessToken'])): ?>
            <a class="float-right btn" href="<?= $_SERVER['HTTP_ORIGIN']."/cognito-login/logout"?>"><small><i class="fa fa-power-off"></i>Logout</small></a>

            <a class="float-right btn" href="<?= $_SERVER['HTTP_ORIGIN']."/cognito-login/change-password"?>"><small><i class="fa fa-key" aria-hidden="true"></i>Change Password</small></a>
            <a class="float-right btn" href="<?= $_SERVER['HTTP_ORIGIN']."/cognito-login/add-group"?>"><small><i class="fa fa-plus" aria-hidden="true"></i>Create Group</small></a>
        <?php endif;?>
        </div>
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
                                <a href="<?= $_SERVER['HTTP_ORIGIN'].'/cognito-login/edit?uid='.$user['Username']?>"><i class="fa fa-edit" ></i></a>
                              <a href="<?= $_SERVER['HTTP_ORIGIN'].'/cognito-login/delete?action=delete&uid='.$user['Username']?>"><i class="fa fa-trash" ></i></a>
                              <a href="<?= $_SERVER['HTTP_ORIGIN'].'/cognito-login/enable?action='.$action.'&uid='.$user['Username']?>"><i class="fa fa-<?= $icon?>" ></i></a>
                            </td>
                        </tr>
                        <?php endforeach; 
                    else:
                        echo '<tr><td>No Records Found</td></tr>';
                    endif;?>
                </tbody>
            </table>
            <?php if(isset($groups['Groups']) && !empty($groups['Groups'])):?>
            <h2> GROUP'S </h2>
             <table class="table">
                <thead>
                    <tr>
                        <th>Group Name</th>
                        <th>Description</th>
                        <th>Precedence</th>
                        <th>Updated</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($groups['Groups'] as $key => $group):?>
                        <tr id="group-<?=  $group['GroupName'] ?>">
                            <td>
                                <?=  $group['GroupName'] ?>
                            </td>
                            <td>
                                <?=  $group['Description'] ?>
                            </td>
                            <td> - </td>
                            <td>
                                <?=  $group['LastModifiedDate']->format('d M,Y h:i a') ?>
                            </td>
                            <td>
                                <?=  $group['CreationDate']->format('d M,Y h:i a') ?>
                            </td>
                             <td>
                                <a href="javascript:;" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="getUserInGroup('<?= $group['GroupName']?>')"><i class="fa fa-users" ></i></a>
                              <a href="javascript:;" onclick="deleteGroup('<?=  $group['GroupName'] ?>')"><i class="fa fa-trash" ></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
            <?php  endif; if(!isset($_SESSION['AccessToken'])): ?>
            <div class="border-top card-body text-center">Have an account? <a href="<?= $_SERVER['HTTP_ORIGIN']."/cognito-login"?>">Log In</a></div>
       <?php endif;
       include('view/modal/addUserToGroup.php'); 
        ?>
    </div>

    <!--container end.//-->
    <?php  include('view/common/footer.php'); ?>