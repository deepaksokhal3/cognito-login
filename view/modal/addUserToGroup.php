<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
   <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title h4" id="myLargeModalLabel">Manage Users In Group</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
      <table class="table">
        <thead>
            <tr>
              <th></th>
                <th>Email</th>
                <th>Created At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>       
          <?php if($users):
            foreach ($users as $key => $user):
                          $action = $user['Enabled']?'disable':'enable';
                          $icon = $user['Enabled']?'lock-open':'lock';
                          ?>
                      <tr id="out<?= $user['Username']?>">
                        <td><a id="<?= 'link'.$user['Username']?>" href="javascript:;"  onclick="addUserInGroup('<?= $user['Username']?>')"><i class="fa fa-plus-circle"></i></a></td>
                          <td>
                              <?=  $user['email'] ?>
                          </td>
                          <td>
                              <?=  $user['UserCreateDate']->format('d M,Y') ?>
                          </td>
                          <td>
                              <?=  $user['UserStatus'] ?>
                          </td>
                      </tr>
                      <?php endforeach;  endif;?>
              </tbody>
          </table>
          <h5 class="modal-title h4" id="myLargeModalLabel">Add users to group(<small id="groupNameTitle"></small>)</h5>

           <table class="table">
              <thead>
                  <tr>
                      <th>User Name</th>
                      <th>Email</th>
                      <th>Created At</th>
                      <th>Status</th>
                      <th>Remove</th>
                  </tr>
              </thead>
              <tbody id="userGroup">
              </tbody>
            </table>
      </div>
    </div>
  </div>
</div>
