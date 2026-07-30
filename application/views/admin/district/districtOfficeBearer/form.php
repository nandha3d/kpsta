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
    <?php } 
    if (validation_errors()) { ?>
        <div class = "alert alert-danger alert-dismissible">
            <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
        </div>
    <?php } ?>


    <?php $class = form_error('designation') ? 'form-group has-error' : 'form-group' ?>
    <?php $selected = isset($formValues['designation']) ? $formValues['designation'] : '' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">Designation</label>
        <?php echo form_dropdown('designation', $designation, $selected, 'class="form-control select2-category" style="width: 100%;"   '); ?>
    </div>


    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label >Position</label>
                <?php $formValues['position'] = isset($formValues['position']) ? $formValues['position'] : 25; ?>
                <?php echo form_dropdown('position', array_combine(range(1, 25), array_values(range(1, 25))), $formValues['position'], 'class="form-control" style="width: 100%;"   '); ?>
            </div>
        </div>
        <div class="col-md-9">
            <?php $class = form_error('name') ? 'form-group has-error' : 'form-group' ?>
            <div class="<?php echo $class ?>">
                <label for="name">Name</label>
                <input  class="form-control" name="name" placeholder="Enter Name"  value="<?php echo isset($formValues['name']) ? $formValues['name'] : '' ?>" required="required">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php $class = form_error('email') ? 'form-group has-error' : 'form-group' ?>
            <div class="<?php echo $class ?>">
                <label for="email">Email</label>
                <input  class="form-control" name="email" placeholder="Enter Email"  value="<?php echo isset($formValues['email']) ? $formValues['email'] : '' ?>" >
            </div>
        </div>
        <div class="col-md-6">
            <?php $class = form_error('phone') ? 'form-group has-error' : 'form-group' ?>
            <div class="<?php echo $class ?>">
                <label for="phone">Phone</label>
                <input  class="form-control" name="phone" placeholder="Enter Phone"  value="<?php echo isset($formValues['phone']) ? $formValues['phone'] : '' ?>" >
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?php $class = form_error('year') ? 'form-group has-error' : 'form-group' ?>
            <div class="<?php echo $class ?>">
                <label for="year">Year</label>
                <select class="form-control" name="year" id="year">
                    <option value="">- Select Year -</option>
                    <?php 
                        $current_year = date('Y');
                        for($i = $current_year + 1; $i >= 2000; $i--) {
                            $yr = $i . '-' . ($i+1);
                            $selected = (isset($formValues['year']) && $formValues['year'] == $yr) ? 'selected' : '';
                            echo "<option value=\"$yr\" $selected>$yr</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="is_former">Status</label>
                <div class="btn-group" data-toggle="buttons" style="display:block;">
                    <label class="btn btn-default <?php echo (isset($formValues['is_former']) && $formValues['is_former'] == 0) ? 'active' : '' ?>" data-active-class="success">
                        <input type="radio" name="is_former" value="0" autocomplete="off" <?php echo (isset($formValues['is_former']) && $formValues['is_former'] == 0) ? 'checked' : '' ?> > Active
                    </label>
                    <label class="btn btn-default <?php echo (isset($formValues['is_former']) && $formValues['is_former'] == 1 ) ? 'active' : '' ?>" data-active-class="danger">
                        <input type="radio" name="is_former" value="1" autocomplete="off" <?php echo (isset($formValues['is_former']) && $formValues['is_former'] == 1 ) ? 'checked' : '' ?> > Former
                    </label>
                </div>
            </div>
        </div>
    </div>

    <?php $class = form_error('content') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="photo">Upload Photo </label>
        <div style="margin-bottom: 15px;">
            <div class="btn btn-primary btn-file" tabindex="500">
                <i class="glyphicon glyphicon-folder-open"></i>&nbsp; <span class="hidden-xs">Browse …</span>
                <input type="file" data-show-preview="true" class="file" name="image" >
            </div>
            <!-- Assuming there's a script that clears the file input, we keep a remove button just in case -->
            <button class="btn btn-default fileinput-remove-button" onclick="document.querySelector('input[name=image]').value=''; return false;" title="Clear selected files" type="button"><i class="glyphicon glyphicon-trash"></i> Remove</button>
        </div>
    </div>


    <div class="row" >
        <div class="col-md-7" >
            <div class="form-group"  >
                <label class="col-md-12">Original Image </label>
                <div class="pull-left thumbnail-div">
                    <img src="<?php echo isset($formValues['image']) ? base_url(OFFICE_BEARER . '/' . $formValues['image']) . '?t=' . time() : '' ?>" id="thumbnail" style="max-width:100%;max-height: 300px;"  alt=""/>
                </div>
            </div>
        </div>


        <div class="col-md-5">
            <div class="form-group">
                <label class="col-md-12">Thumbnail Preview </label>
                <div style=" float:left; position:relative; overflow:hidden; width:173px; height:214px;" class="thumbnail_preview_loader">
                    <img  style="position: relative; max-width: none !important;" id="thumbnail_preview"  src="<?php echo isset($formValues['image']) ? base_url(OFFICE_BEARER . '/' . $formValues['image']) . '?t=' . time() : '' ?>" />
                    <input type="hidden"  id="x1">
                    <input type="hidden"  id="y1">
                    <input type="hidden"  id="x2">
                    <input type="hidden"  id="y2">
                    
                    <input type="hidden"  id="w">
                    <input type="hidden"  id="h">
                </div>
            </div>
        </div>
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

