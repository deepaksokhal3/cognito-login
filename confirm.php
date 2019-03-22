<?php include('view/common/header.php'); 

    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();
    if(isset($_POST['submit'])):
        $response =   $client->confirmUserRegistration($_POST['confirmcode'], $_POST['email']);
        if($response):
            $msg->error($response);
        else:
            header("Location:".$_SERVER['HTTP_ORIGIN']."/cognito-login");
        endif; 
    endif
?>
<div class="container">
    <br>
    <br>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">

                <article class="card-body">
                    <?php  $msg->display(); ?>
                    <form action="" method="post">

                        <div class="form-group">
                            <label>Email address / Username</label>
                            <input type="email" name="email" class="form-control" placeholder="Email">
                        </div>
                        <!-- form-group end.// -->

                        <div class="form-group">
                            <label>Code</label>
                            <input class="form-control" name="confirmcode" type="text">
                        </div>
                        <!-- form-group end.// -->
                        <div class="form-group">
                            <button type="submit" name="submit" class="btn btn-primary btn-block">Confirm Account</button>
                        </div>
                        <!-- form-group// -->
                        <small class="text-muted">By clicking the 'Sign Up' button, you confirm that you accept our <br> Terms of use and Privacy Policy.</small>
                    </form>
                </article>
                <!-- card-body end .// -->
                <div class="border-top card-body text-center">Have an account? <a href="<?= $_RVER['HTTP_ORIGIN']."/cognito-login"?>">Log In</a></div>
            </div>
            <!-- card.// -->
        </div>
        <!-- col.//-->

    </div>
    <!-- row.//-->

</div>
<!--container end.//-->
<?php  include('view/common/footer.php'); ?>