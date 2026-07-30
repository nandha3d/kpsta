<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl]) ?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body">
    <?php if (isset($formValues['error'])) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error !</h4> <?php echo $formValues['error'] ?>  
        </div>
        <?php
    }
    if (validation_errors()) {
        ?>
        <div class = "alert alert-danger alert-dismissible">
            <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
        </div>
    <?php } ?>


    


    <?php $class = form_error('district') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="name">District</label>
        <input  class="form-control" name="district" placeholder="Enter Name"  value="<?php echo isset($formValues['district']) ? $formValues['district'] : '' ?>" required="required">
    </div>

    

    <?php $class = form_error('website_url') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="phone">Website url</label>
        <input  class="form-control" name="website_url" placeholder="Enter Website url"  value="<?php echo isset($formValues['website_url']) ? $formValues['website_url'] : '' ?>" >
    </div>




    


</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times text-danger "></i> Close</button>
    <button type="submit" class="btn btn-primary"  ><i class="fa fa-save "></i> Save</button>
</div>
<?php echo form_close(); ?>


