<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl]) ?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body" >
    <?php if (isset($formValues['error'])) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error !</h4> <?php echo implode('<br>', $formValues['error']) ?>  
        </div>
    <?php } else if (validation_errors()) { ?>
        <div class = "alert alert-danger alert-dismissible">
            <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
        </div>
    <?php } ?>

    <?php $class = form_error('group_id') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?> ">
        <?php $selected = isset($formValues['group_id']) ? $formValues['group_id'] : "" ?>
        <label>Group</label>
        <?php echo form_dropdown('group_id', $groupSelect, $selected, 'class="form-control select2 has-default-select"    style="width: 100%;"   '); ?>
    </div>

    <?php $class = form_error('position') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >Position</label>
        <?php $formValues['position'] = isset($formValues['position']) ? $formValues['position'] : 25; ?>
        <?php echo form_dropdown('position', array_combine(range(1, 25), array_values(range(1, 25))), $formValues['position'], 'class="form-control select2 has-default-select" style="width: 100%;"   '); ?>

    </div>


    <?php $class = form_error('content') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="content">Content</label>
        <textarea class="form-control content-textarea"  name="content" rows="5" placeholder="Enter Content" required="required"><?php echo isset($formValues['content']) ? $formValues['content'] : '' ?></textarea>
    </div>


    <?php $class = form_error('pdfName') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">File </label>

        <div class="file-input file-input-new">
            <div class="input-group file-caption-main">
                <div class="form-control file-caption  kv-fileinput-caption" tabindex="500">
                    <div class="file-caption-name" title="">
                        <?php
                        $fileCalss = 'fileinput-remove-button';
                        if (isset($formValues['file_name']) && $formValues['file_name']) {
                            echo $formValues['file_name'];
                            $fileCalss = "";
                        }
                        ?>
                    </div>
                </div>

                <div class="input-group-btn">
                    <button data-href="<?php echo base_url('admin/forms/fileremove'); ?>" class="btn btn-default fileinput-remove fileinput-remove-button" title="Clear selected files" tabindex="500" type="button"><i class="glyphicon glyphicon-trash"></i>  <span class="hidden-xs">Remove</span></button>
                    <div class="btn btn-primary btn-file" tabindex="500"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">Browse …</span>
                        <input type="file" data-show-preview="true" class="file" id="pdf" name="file"  >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="content">Published</label>
        <div class="btn-group margin" data-toggle="buttons">
            <label class="btn btn-default <?php echo (isset($formValues['publish']) && $formValues['publish'] == 1) ? 'active' : '' ?>" data-active-class="success">
                <input type="radio" name="publish" value="1" autocomplete="off" <?php echo (isset($formValues['publish']) && $formValues['publish'] == 1) ? 'checked' : '' ?> > Yes
            </label>
            <label class="btn  btn-default <?php echo (isset($formValues['publish']) && $formValues['publish'] == 0 ) ? 'active' : '' ?>" data-active-class="danger">
                <input type="radio" name="publish" value="0" id="option2" autocomplete="off" <?php echo (isset($formValues['publish']) && $formValues['publish'] == 0 ) ? 'checked' : '' ?> > No
            </label>
        </div>

    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times text-danger "></i> Close</button>
    <button type="submit" class="btn btn-primary" ><i class="fa fa-save "></i> Save</button>
</div>
<?php echo form_close(); ?>



<script type="text/javascript">

    $(function () {

        $(".select2").select2({
            dropdownParent: $("#modal")
        });

    });

</script>


