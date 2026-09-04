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
        <?php $formValues['position'] = isset($formValues['position']) ? $formValues['position'] : 25; ?>
        <?php echo form_dropdown('position', array_combine(range(1, 6), array_values(range(1, 6))), $formValues['position'], 'class="form-control" style="width: 100%;"   '); ?>

<!--<input  class="form-control" name="position" placeholder="Enter Position(Number)"  value="<?php // echo $formValues['position']   ?>"  >-->
    </div>

    <?php $class = form_error('description') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >Description</label>
        <input  class="form-control" name="description" placeholder="Enter Description"  value="<?php echo isset($formValues['description']) ? $formValues['description'] : '' ?>"  required="required">
    </div>



    <?php $class = form_error('content') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="photo">Select Photo </label>

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
                    <img src="<?php echo isset($formValues['image']) ? base_url(SLIDER_IMAGES . '/' . $formValues['image']) . '?t=' . time() : '' ?>" id="thumbnail" style="max-width:100%;max-height: 300px;"  alt=""/>
                </div>
            </div>
        </div>


        <div class="col-md-5">
            <div class="form-group">
                <label class="col-md-12">Thumbnail Preview </label>
                <div style=" float:left; position:relative; overflow:hidden; width:150px; height:50.14px;" class="thumbnail_preview_loader">
                    <img  style="position: relative; max-width: none !important;" id="thumbnail_preview"  src="<?php echo isset($formValues['image']) ? base_url(SLIDER_IMAGES . '/' . $formValues['image']) . '?t=' . time() : '' ?>" />
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
        <label>Display Location</label><br>
        <div style="margin-bottom: 10px;">
            <label style="font-weight: normal; margin-right: 15px;">
                <input type="checkbox" name="show_on_home" value="1" <?php echo (!isset($formValues['show_on_home']) || $formValues['show_on_home'] == 1) ? 'checked' : ''; ?>> Home Page Slider
            </label>
            <label style="font-weight: normal; margin-right: 15px;">
                <input type="checkbox" name="is_heading_bg" value="1" <?php echo (isset($formValues['is_heading_bg']) && $formValues['is_heading_bg'] == 1) ? 'checked' : ''; ?>> All Pages Heading Background
            </label>
            <label style="font-weight: normal;">
                <input type="checkbox" id="chk_individual" name="chk_individual" value="1" <?php echo (!empty($formValues['heading_pages'])) ? 'checked' : ''; ?>> Individual Pages Heading Background
            </label>
        </div>
        
        <div id="pages_dropdown" style="display: <?php echo (!empty($formValues['heading_pages'])) ? 'block' : 'none'; ?>;">
            <label for="heading_pages">Select Pages</label>
            <?php 
                $selected_pages = !empty($formValues['heading_pages']) ? explode(',', $formValues['heading_pages']) : [];
            ?>
            <select name="heading_pages[]" class="form-control select2" multiple="multiple" style="width: 100%;">
                <option value="OfficeBearer" <?php echo in_array('OfficeBearer', $selected_pages) ? 'selected' : ''; ?>>State Office Bearers</option>
                <option value="District" <?php echo in_array('District', $selected_pages) ? 'selected' : ''; ?>>District Office Bearers</option>
                <option value="Home/former_leaders" <?php echo in_array('Home/former_leaders', $selected_pages) ? 'selected' : ''; ?>>Former Leaders</option>
                <option value="melakal" <?php echo in_array('melakal', $selected_pages) ? 'selected' : ''; ?>>Melakal (Kalolsavam)</option>
                <option value="order-circular" <?php echo in_array('order-circular', $selected_pages) ? 'selected' : ''; ?>>Order & Circular</option>
                <option value="download/forms" <?php echo in_array('download/forms', $selected_pages) ? 'selected' : ''; ?>>Forms</option>
                <option value="Home/service_corner" <?php echo in_array('Home/service_corner', $selected_pages) ? 'selected' : ''; ?>>Service Corner</option>
                <option value="download/academic_corner" <?php echo in_array('download/academic_corner', $selected_pages) ? 'selected' : ''; ?>>Academic Corner</option>
                <option value="download/softwares" <?php echo in_array('download/softwares', $selected_pages) ? 'selected' : ''; ?>>Software Tools</option>
                <option value="Home/memorandums" <?php echo in_array('Home/memorandums', $selected_pages) ? 'selected' : ''; ?>>Memorandums</option>
                <option value="notice_poster" <?php echo in_array('notice_poster', $selected_pages) ? 'selected' : ''; ?>>Notices & Posters</option>
                <option value="Gallery" <?php echo in_array('Gallery', $selected_pages) ? 'selected' : ''; ?>>Gallery</option>
                <option value="Quicklink" <?php echo in_array('Quicklink', $selected_pages) ? 'selected' : ''; ?>>Online Links</option>
                <option value="Contact" <?php echo in_array('Contact', $selected_pages) ? 'selected' : ''; ?>>Contact Us</option>
            </select>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        $('.select2').select2();
        $('#chk_individual').change(function() {
            if($(this).is(':checked')) {
                $('#pages_dropdown').show();
            } else {
                $('#pages_dropdown').hide();
                $('.select2').val(null).trigger('change');
            }
        });
    });
    </script>

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

