<style>
.flex-row {
    display: flex;
    flex-wrap: wrap;
    clear: both;
}
.flex-row::before, .flex-row::after { 
    display: none !important; 
}
</style>
<div class="row flex-row">

    <?php
    foreach ($images as $key => $image) {  ?>
        <div class="col-lg-3 col-md-4 col-xs-6 ">
            <div class="thumb">
                <a href="#" class="thumbnail wraptocenter" >
                    <img alt="" src="<?php echo base_url(GALLERY_THUMB . '/' . $image['image']) . '?t=' . time(); ?>" class="img-responsive" >
                </a>

                <div class="thumb-gallery-details" style="min-height: 40px; border: 1px solid #ddd; border-top: none; margin-top: -20px; padding: 10px 15px; border-radius: 0 0 4px 4px; background: #fff;">
                    <div class="tools image-tools pull-right">
                        <a href="javascript:void(0)" class=" dropdown-toggle " data-toggle="dropdown">
                            <i class="fa fa-pencil edit-pencil" ></i>

                    </a>

                    <ul class="pull-left page-list-actions dropdown-menu" role="menu">
                        <li>
                            <a href="javascript:void(0)"   class="make-cover" data-href="<?php echo base_url('admin/gallery/' . $image['album_id'] . '/makecover/' . $image['id']) ?>"    >
                                <span><i class="fa fa-fw fa-file-image-o"></i>  Make Album Cover</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)"   class="edit"  data-href="<?php echo base_url('admin/gallery/' . $image['album_id'] . '/edit/' . $image['id']) ?>"  >
                                <span><i class="fa fa-pencil-square-o"></i>  Edit</span>
                            </a>
                        </li>
                        <li>
                            <a  href="javascript:void(0)" class="" data-href="<?php echo base_url('admin/gallery/' . $image['album_id'] . '/delete/' . $image['id']) ?>"   data-toggle="modal" data-target="#delete"  data-precheck="" data-message="Delete this Image ?" data-confirm-text="Delete" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation" >
                                <span><i class="fa fa-fw fa-trash-o text-danger"></i> <span class="">Delete</span></span>
                            </a>
                        </li>
                    </ul>
                </div>
                </div>






            </div>
        </div>

<?php } ?>


</div>