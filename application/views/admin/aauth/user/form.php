<?php echo form_open('', ['autocomplete' => 'off', "name" => "editUserForm", 'id' => $formId, "data-id" => isset($id) ? $id : '']) ?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <label for="exampleInputEmail1">Email Address</label>
        <input type="email" class="form-control" name="email" placeholder="Enter Address" value="<?php echo isset($email) ? $email : '' ?>">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">User Name</label>
        <input  class="form-control" name="username" placeholder="Enter Username"  value="<?php echo isset($username) ? $username : '' ?>">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Password</label>
        <input   class="form-control" name="password" placeholder="Enter Password">
    </div>

    <div class="form-group">
        <?php $selected = isset($group_id) ? $group_id : '' ?>
        <label>Group</label>
        <?php echo form_dropdown('group', $groupSelect, $selected, 'class="form-control select2-category" style="width: 100%;"  required= "required" '); ?>
        
    </div>

    <div class="form-group">
        <label>Status</label>
        <select class="form-control" name="status" required="required"> 
            <option value="" > - - SELECT STATUS - - </option>
            <option class="text-success" <?php echo isset($banned) ? ($banned == 0 ? 'selected' : '') : '' ?> value="0">Active</option>
            <option class="text-danger" <?php echo isset($banned) ? ($banned == 1 ? 'selected' : '') : '' ?>  value="1">Lock</option>
        </select>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary"  >Save</button>
</div>
<?php echo form_close(); ?>