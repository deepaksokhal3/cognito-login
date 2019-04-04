<?php include('view/common/header.php'); 
    use csrfhandler\csrf as csrf;
    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();
    if(isset($_POST['submit']) && csrf::post()){
       $response =  $client->registerUser($_POST['email'], $_POST['pass'], [
            'email' => $_POST['email'],
        ]);
            $_SESSION['username'] = $_POST['email'];
            if(isset($response['error'])):
                $msg->error($response['error']);
            else:
                header("Location:".$_SERVER['HTTP_ORIGIN']."/cognito-login/confirm");
            endif;   
        }
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
                                <input type="hidden" name="_token" value="<?php echo csrf::token()?>">
                                <!-- form-group end.// -->
                                <div class="form-group">
                                    <label>Email address</label>
                                    <input type="email" class="form-control" name="email" placeholder="Email">
                                    <small class="form-text text-muted">We'll never share your email with anyone else.</small>
                                </div>
                                <!-- form-group end.// -->
                                <div class="form-group">
                                    <label>Create password</label>
                                    <input class="form-control" name="pass" type="password" placeholder="Password">
                                </div>
                                <!-- form-group end.// -->
                                <div class="form-group">
                                    <button type="submit" name="submit" class="btn btn-primary btn-block"> Register </button>
                                </div>
                                <!-- form-group// -->
                                <small class="text-muted">By clicking the 'Sign Up' button, you confirm that you accept our <br> Terms of use and Privacy Policy.</small>
                            </form>
                    </article>
                    <!-- card-body end .// -->
                    <div class="border-top card-body text-center">Have an account? <a href="/cognito-login">Log In</a></div>
                </div>
                <!-- card.// -->
            </div>
            <!-- col.//-->
        </div>
        <!-- row.//-->
    </div>
    <!--container end.//-->
    <?php include('view/common/footer.php'); ?>