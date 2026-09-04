<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl]) ?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body">

    <?php $class = form_error('heading') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="title">Title</label>
        <input  class="form-control" name="title" placeholder="Enter Heading"  value="<?php echo isset($formValues['title']) ? $formValues['title'] : '' ?>" >
    </div>

    <?php $class = form_error('content') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">File </label>

        <div class="file-input file-input-new">
            <div class="input-group file-caption-main">
                <div class="form-control file-caption  kv-fileinput-caption" tabindex="500">
                    <div class="file-caption-name" title="">
                        <?php // echo isset($formValues['upload_type']) && $formValues['upload_type'] == 'file' ? $formValues['pdfName'] : '' ?>
                    </div>
                </div>

                <div class="input-group-btn">
                    <button class="btn btn-default fileinput-remove fileinput-remove-button" title="Clear selected files" tabindex="500" type="button"><i class="glyphicon glyphicon-trash"></i>  <span class="hidden-xs">Remove</span></button>
                    <div class="btn btn-primary btn-file" tabindex="500"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">Browse …</span><input type="file" data-show-preview="true" class="file"  name="image" ></div>
                </div>
            </div>
        </div>
    </div>


    <div class="row" >
        <div class="col-md-7" >
            <div class="form-group"  >
                <label class="col-md-12">Original Image </label>
                <div class="pull-left thumbnail-div">
                    <img src="<?php echo isset($formValues['image']) ? base_url(GALLERY_ORIGINAL . '/' . $formValues['image']) . '?t=' . time() : '' ?>" id="thumbnail" style="max-width:100%;max-height: 300px;"  alt=""/>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="form-group">
                <label class="col-md-12">Thumbnail Preview </label>
                <div style=" float:left; position:relative; overflow:hidden; width:150px; height:150px;" class="thumbnail_preview_loader">
                    <img  style="position: relative; max-width: none !important;" id="thumbnail_preview"  src="<?php echo isset($formValues['image']) ? base_url(GALLERY_ORIGINAL . '/' . $formValues['image']) . '?t=' . time() : '' ?>" />
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

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times text-danger "></i> Close</button>
    <button type="submit" class="btn btn-primary" id="save"  ><i class="fa fa-save "></i> Save</button>
</div>
<?php echo form_close(); ?>