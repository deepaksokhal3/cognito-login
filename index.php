<?php include('view/common/header.php'); 


    use pmill\AwsCognito\CognitoClient;
    use pmill\AwsCognito\Exception\ChallengeException;
    use pmill\AwsCognito\Exception\PasswordResetRequiredException;

    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();

    if(isset($_POST['submit'])):
      try {
            $authenticationResponse = $client->authenticate($_POST['username'], $_POST['pass']);
            if(!isset($authenticationResponse['AccessToken']))
                $msg->error($response);
            if(isset($authenticationResponse['AccessToken']))
            $msg->success('login successfully! ');

        } catch (ChallengeException $e) {
            if ($e->getChallengeName() === CognitoClient::CHALLENGE_NEW_PASSWORD_REQUIRED) {
                $authenticationResponse = $client->respondToNewPasswordRequiredChallenge($username, 'password_new', $e->getSession());
                if(!isset($authenticationResponse['AccessToken']))
                $msg->error($response);
            }
        } catch (PasswordResetRequiredException $e) {
            $msg->info('PASSWORD RESET REQUIRED');
        }  
    endif;
   
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
                            <input type="email" name="username" class="form-control" placeholder="">
                        </div>
                        <!-- form-group end.// -->

                        <div class="form-group">
                            <label>Password</label>
                            <input class="form-control" name="pass" type="password">
                        </div>
                        <!-- form-group end.// -->
                        <div class="form-group">
                            <button type="submit" name="submit" class="btn btn-primary btn-block"> Login </button>
                        </div>
                        <!-- form-group// -->
                        <small class="text-muted">By clicking the 'Sign Up' button, you confirm that you accept our <br> Terms of use and Privacy Policy.</small>
                    </form>
                </article>
                <!-- card-body end .// -->
                <div class="border-top card-body text-center">Create an account? <a href="<?= $_RVER['TTP_ORIGIN']."/cognito-login/register.php"?>">Sign Up</a></div>
            </div>
            <!-- card.// -->
        </div>
        <!-- col.//-->

    </div>
    <!-- row.//-->

</div>
<!--container end.//-->
<?php  include('view/common/footer.php'); ?>