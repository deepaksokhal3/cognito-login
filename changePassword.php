<?php include('view/common/header.php');
    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();
    if(isset($_POST['submit'])){
        $response = $client->changePassword($_SESSION['AccessToken'], $_POST['oldPass'], $_POST['pass']);
        if(!is_string($response))
            $msg->success('Password successfully changed.');
        else
            $msg->error($response);
    }
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
                    <form id="changePassword" action="" method="post" class="was-validated">
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" name="oldPass" class="form-control" placeholder="" required>
                        </div>
                        <!-- form-group end.// -->
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="pass" class="form-control" placeholder="" required>
                        </div>
                        <!-- form-group end.// -->
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="Confirmpass" class="form-control" placeholder="" required>
                            <div class="invalid-feedback" style="display: none;">Confirm password not match</div>
                        </div>
                        <!-- form-group end.// -->
                        <div class="form-group">
                            <button type="submit" name="submit" class="btn btn-primary btn-block"> Submit </button>
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