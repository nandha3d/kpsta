<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", 'enctype' => 'multipart/form-data', "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl]) ?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body" >
    <?php if (isset($error)) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error !</h4> <?php echo $error ?>  
        </div>
    <?php } else if (validation_errors()) { ?>
        <div class = "alert alert-danger alert-dismissible">
            <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
        </div>
    <?php } ?>


    <?php $class = form_error('heading') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">Heading</label>
        <input  class="form-control" name="heading" placeholder="Enter Heading"  value="<?php echo isset($formValues['heading']) ? $formValues['heading'] : '' ?>" required="required">
    </div>

    <?php $class = form_error('content') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="content">Content</label>
        <textarea class="form-control content-textarea"  name="content" rows="5" placeholder="Enter Content" required="required"><?php echo isset($formValues['content']) ? $formValues['content'] : '' ?></textarea>
    </div>

    <div class="form-group">
        <label for="image">Featured Image</label>
        <input type="file" class="form-control" name="image" accept="image/*">
        <?php if (!empty($formValues['image'])) { ?>
            <div class="mt-2" style="margin-top:10px;">
                <img src="<?php echo base_url('uploads/news/'.$formValues['image']); ?>" alt="Current Image" style="max-height: 100px;">
                <p class="help-block">Leave blank to keep the current image.</p>
            </div>
        <?php } ?>
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
        $('#modal').on('show.bs.modal', function (e) {
            $('.content-textarea').summernote({
                height: 150,
                tabsize: 2,
            });
        });


        
    });


</script>