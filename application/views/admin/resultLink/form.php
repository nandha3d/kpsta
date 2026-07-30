<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl]) ?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body">
    <?php if (isset($error)) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error !</h4> <?php echo $error ?>  
        </div>
    <?php } else if (validation_errors()) { ?>
        <div class = "alert alert-danger alert-dismissible">
            <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
        </div>
    <?php } ?>


    <?php $class = form_error('position') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >Position</label>

        <?php
        $dropDown[1000] = "DEFAULT";
        for ($i = 1; $i <= 25; $i++) {
            $dropDown[$i] = $i;
        }
        ?>

        <?php $formValues['position'] = isset($formValues['position']) ? $formValues['position'] : 1000; 
  ?>
        <?php echo form_dropdown('position', $dropDown, $formValues['position'], 'class="form-control" style="width: 100%;"   '); ?>

<!--<input  class="form-control" name="position" placeholder="Enter Position(Number)"  value="<?php // echo $formValues['position']    ?>"  >-->
    </div>

    <?php $class = form_error('description') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >Description</label>
        <input  class="form-control" name="description" placeholder="Enter Heading"  value="<?php echo isset($formValues['description']) ? $formValues['description'] : '' ?>"  required="required">
    </div>


    <?php $class = form_error('path') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >URL   <span class="text-danger"> [website name should start with https:// or http:// or www.]</span></label>
        <input  class="form-control" name="path" placeholder="Enter site url"  value="<?php echo isset($formValues['path']) ? $formValues['path'] : '' ?>" >
    </div>



    <div class="form-group">
        <label for="content">Published</label>
        <div class="btn-group margin" data-toggle="buttons">
            <label class="btn btn-default <?php echo (isset($formValues['is_publish']) && $formValues['is_publish'] == 1) ? 'active' : '' ?>" data-active-class="success">
                <input type="radio" name="is_publish" value="1" autocomplete="off" <?php echo (isset($formValues['is_publish']) && $formValues['is_publish'] == 1) ? 'checked' : '' ?> > Yes
            </label>
            <label class="btn  btn-default <?php echo (isset($formValues['is_publish']) && $formValues['is_publish'] == 0 ) ? 'active' : '' ?>" data-active-class="danger">
                <input type="radio" name="is_publish" value="0" id="option2" autocomplete="off" <?php echo (isset($formValues['is_publish']) && $formValues['is_publish'] == 0 ) ? 'checked' : '' ?> > No
            </label>
        </div>

    </div>


</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times text-danger "></i> Close</button>
    <button type="submit" class="btn btn-primary"  ><i class="fa fa-save "></i> Save</button>
</div>
<?php echo form_close(); ?>

