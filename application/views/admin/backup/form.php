<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl]) ?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body">

    <?php $class = form_error('description') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="title">Description</label>
        <input  class="form-control" name="description" placeholder="Enter Heading"  value="<?php echo isset($formValues['description']) ? $formValues['description'] : '' ?>" >
    </div>

    <?php $uploadType = isset($formValues['upload_type']) ? $formValues['upload_type'] : '' ?>

    <?php $class = form_error('upload_type') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="content">Upload Type</label>

        <div class="btn-group margin upload-type-toggle" data-toggle="buttons">
            <label class=" btn btn-default <?php echo ($uploadType == 'file') ? 'active' : '' ?>" data-active-class="success">
                <input type="radio" name="upload_type" data-type="file" value="file"  autocomplete="off" <?php echo ($uploadType == 'file') ? 'checked' : '' ?>  > PDF
            </label>
            <label class=" btn  btn-default <?php echo ($uploadType == 'url' ) ? 'active' : '' ?>" data-active-class="success">
                <input type="radio" name="upload_type" data-type="url" value="url" autocomplete="off" <?php echo ($uploadType == 'url' ) ? 'checked' : '' ?> > URL
            </label>
        </div>

    </div>

    <?php $class = form_error('path') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >URL <span class="text-danger"> [website name should start with https:// or http:// or www.]</span></label></label>
        <input  class="form-control" name="path" placeholder="Enter site url"  value="<?php echo ($uploadType == 'url') ? $formValues['path'] : '' ?>" <?php echo $uploadType == "file" ? 'disabled="disabled"' : '' ?> >
    </div>

    <?php $class = form_error('file') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">PDF/Image File </label>
        <div class="file-input file-input-new">
            <div class="input-group file-caption-main">
                <div class="form-control file-caption  kv-fileinput-caption" tabindex="500">
                    <div class="file-caption-name-file" title="">
                        <?php echo ($uploadType == 'file') ? $formValues['path'] : '' ?>
                    </div>
                </div>

                <div class="input-group-btn">
                    <button class="btn btn-default fileinput-remove fileinput-remove-button" title="Clear selected files" tabindex="500" type="button"><i class="glyphicon glyphicon-trash"></i>  <span class="hidden-xs">Remove</span></button>
                    <div class="btn btn-primary btn-file" tabindex="500">
                        <i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">Browse …</span>
                        <input type="file" data-show-preview="true" class="file" id="pdf" name="file" >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr style="width: 100%; color: black; height: 2px;" />

    <?php $class = form_error('image') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label>Upload Cover Image </label>

        <div class="file-input file-input-new">
            <div class="input-group file-caption-main">
                <div class="form-control file-caption  kv-fileinput-caption" tabindex="500">
                    <div class="file-caption-name-image" title="">
                        <?php echo isset($formValues['image']) ? $formValues['image'] : '' ?>
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
                    <img src="<?php echo isset($formValues['image']) ? base_url(ADAYAPAKA_SABHAM_IMAGE . '/' . $formValues['image']) . '?t=' . time() : '' ?>" id="thumbnail" style="max-width:100%;max-height: 300px;"  alt=""/>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="form-group">
                <label class="col-md-12">Thumbnail Preview </label>
                <div style=" float:left; position:relative; overflow:hidden; width:173px; height:214px;" class="thumbnail_preview_loader">
                    <img  style="position: relative; max-width: none !important;" id="thumbnail_preview"  src="<?php echo isset($formValues['image']) ? base_url(ADAYAPAKA_SABHAM_IMAGE . '/' . $formValues['image']) . '?t=' . time() : '' ?>" alt=""/>
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