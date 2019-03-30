<?php include('view/common/header.php');
    $client = require __DIR__ . '/lib/bootstrap.php';
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();
    if(isset($_POST['submit'])){
        $response = $client->createGroup($_POST['name'],$_POST['description']);
        if(!is_string($response))
            $msg->success('User group successfully created.');
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
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" placeholder="" pattern="[\p{L}\p{M}\p{S}\p{N}\p{P}]+"  oninvalid="setCustomValidity('Spaces are not allowed in group name')" required>
                        </div>
                        <!-- form-group end.// -->
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control" placeholder="">
                        </div>
                        <!-- form-group end.// -->
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