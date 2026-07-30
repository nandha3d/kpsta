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

    <?php foreach ($albums as $key => $album) { ?>

        <div class="col-lg-3 col-md-4 col-xs-6 ">
            <div class="thumb">
                <a href="<?php echo base_url('admin/gallery/' . $album['guId']) ?>" class="thumbnail wraptocenter" >
                    <?php $src = $album['coverImage'] ? GALLERY_THUMB . '/' . $album['coverImage'] : 'public/images/300x300.png' ?>
                    <img alt="" src="<?php echo base_url($src); ?>" class="img-responsive"  alt="" width="300" height="300">
                </a>

                <div class="thumb-gallery-details">
                    <div class="gallerty-title">
                        
                        <a  title="<?php echo $album['name'] ?>" href="<?php echo base_url('admin/gallery/' . $album['guId']) ?>"><?php echo $album['name'] ?></a>
                    </div>

                    <div class="gallerty-sub-title">
                        <span><?php echo $album['iCount'] ?> images </span>
                        <div class="tools pull-right">
                            <a href="javascript:void(0)" class=" dropdown-toggle " data-toggle="dropdown">
                                <i class="fa fa-gear "></i>
                            </a>
                            <ul class="pull-left page-list-actions dropdown-menu" role="menu">
                                <li>
                                    <a href="javascript:void(0)"   class="edit"    data-href="<?php echo base_url('admin/gallery/edit/' . $album['id']) ?>" >
                                        <span><i class="fa fa-pencil-square-o"></i>  Edit</span>
                                    </a>
                                </li>
                                <li>
                                    <a  href="javascript:void(0)" class="" data-href="<?php echo base_url('admin/gallery/delete/' . $album['id']) ?>"   data-toggle="modal" data-target="#delete"  data-precheck="" data-message="Delete this Album ?" data-confirm-text="Delete" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation" >
                                        <span><i class="fa fa-fw fa-trash-o text-danger"></i> <span class="">Delete</span></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    <?php } ?>





</div>