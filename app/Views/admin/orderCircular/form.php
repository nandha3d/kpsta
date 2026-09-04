<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl] ) ?> 
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


    <?php $class = form_error('date') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >Date</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker pull-right" name="date" required="required" value="<?php echo isset($formValues['date']) ? $formValues['date'] : '' ?>" data-date-format="dd-mm-yyyy">
        </div>
    </div>

    <?php $class = form_error('category') ? 'form-group has-error' : 'form-group' ?>
    <?php $selected = isset($formValues['category']) ? $formValues['category'] : '' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">Category</label>
        <?php echo form_dropdown('category', $category, $selected, 'class="form-control select2-category" style="width: 100%;"  required= "required" '); ?>
    </div>



    <?php $class = form_error('description') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >Description</label>
        <input  class="form-control" name="description" placeholder="Enter Heading"  value="<?php echo isset($formValues['description']) ? $formValues['description'] : '' ?>"  required="required">
    </div>


    <?php $class = form_error('upload_type') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="content">Upload Type</label>


        <div class="btn-group margin upload-type-toggle" data-toggle="buttons">
            <label class=" btn btn-default <?php echo (isset($formValues['upload_type']) && $formValues['upload_type'] == 'file') ? 'active' : '' ?>" data-active-class="success">
                <input type="radio" name="upload_type" data-type="file" value="file"  autocomplete="off" <?php echo (isset($formValues['upload_type']) && $formValues['upload_type'] == 'file') ? 'checked' : '' ?>  > PDF
            </label>
            <label class=" btn  btn-default <?php echo (isset($formValues['upload_type']) && $formValues['upload_type'] == 'url' ) ? 'active' : '' ?>" data-active-class="success">
                <input type="radio" name="upload_type" data-type="url" value="url" autocomplete="off" <?php echo (isset($formValues['upload_type']) && $formValues['upload_type'] == 'url' ) ? 'checked' : '' ?> > URL
            </label>
        </div>

    </div>

    <?php $class = form_error('path') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >URL  <span class="text-danger"> [website name should start with https:// or http:// or www.]</span></label>
        <?php $uploadType = isset($formValues['upload_type']) ? $formValues['upload_type'] : '' ?>
        <input  class="form-control" name="path" placeholder="Enter site url"  value="<?php echo isset($formValues['path']) ? $formValues['path'] : '' ?>" <?php echo $uploadType == "file" ? 'disabled="disabled"' : '' ?> >
    </div>

    <?php $class = form_error('pdfName') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">File </label>
        <input type="hidden" name="pdfName" value="<?php echo isset($formValues['upload_type']) && $formValues['upload_type'] == 'file' ? $formValues['pdfName'] : '' ?>">

        <div class="file-input file-input-new">
            <div class="input-group file-caption-main">
                <div class="form-control file-caption  kv-fileinput-caption" tabindex="500">
                    <div class="file-caption-name" title="">
                        <?php $view = (isset($formValues['upload_type']) && $formValues['upload_type'] == 'file') ? base_url(ORDER_CIRCULAR_PATH . '/' . ($formValues['path'] ?? '')) : 'javascript:void(0)'; ?>
                        <?php echo isset($formValues['upload_type']) && $formValues['upload_type'] == 'file' ? $formValues['pdfName'] : '' ?>
                    </div>
                </div>

                <div class="input-group-btn">
                    <button class="btn btn-default fileinput-remove fileinput-remove-button" title="Clear selected files" tabindex="500" type="button"><i class="glyphicon glyphicon-trash"></i>  <span class="hidden-xs">Remove</span></button>
                    <div class="btn btn-primary btn-file" tabindex="500"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">Browse …</span><input type="file" data-show-preview="true" class="file" id="pdf" name="file" ></div>
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


<script type="text/javascript">


    $(function () {
        $(".select2-category").select2({
            dropdownParent: $("#modal")
        });
        $('.datepicker').datepicker({
            autoclose: true
        });

    });

</script>