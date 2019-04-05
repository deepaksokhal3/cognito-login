<?php include('view/common/header.php');
    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();
    $entercode = false;
    if(isset($_POST['code'])){
        $response =  $client->sendForgottenPasswordRequest($_POST['username']);
        
        if(isset($response['CodeDeliveryDetails'])){
            $msg->success("we have sent code to your mail.");
            $entercode = true;
        }
        else
            $msg->error($response);  

    }
    if(isset($_POST['reset'])){
        $response =  $client->resetPassword($_POST['confirmationCode'],$_POST['username'],$_POST['pass']);
        if(!empty($response))
            header("Location:".$_SERVER['HTTP_ORIGIN']."/cognito-login");
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
                    <?php if(!$entercode){ ?>
                    <form action="" method="post">

                        <div class="form-group">
                            <label>Email address / Username</label>
                            <input type="email" name="username" class="form-control" placeholder="">
                        </div>
                        <!-- form-group end.// -->
                        <div class="form-group">
                            <button type="submit" name="code" class="btn btn-primary btn-block"> Submit </button>
                        </div>
                    </form>
                <?php } else{?>
                    <form action="" method="post">

                        <div class="form-group">
                            <label>Email address / Username</label>
                            <input type="email" name="username" class="form-control" placeholder="">
                        </div>
                        <div class="form-group">
                            <label>Confimation Code</label>
                            <input class="form-control" name="confirmationCode" type="text">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input class="form-control" name="pass" type="password">
                        </div>
                        <!-- form-group end.// -->
                        <div class="form-group">
                            <button type="submit" name="reset" class="btn btn-primary btn-block"> Submit </button>
                        </div>
                    </form>
                <?php } ?>
                </article>
                <!-- card-body end .// -->
                <div class="border-top card-body text-center">Create an account? <a href="/cognito-login">Sign In</a></div>
            </div> 
            <!-- card.// -->
        </div>
        <!-- col.//-->

    </div>
    <!-- row.//-->

</div>
<!--container end.//-->
<?php  include('view/common/footer.php'); ?>