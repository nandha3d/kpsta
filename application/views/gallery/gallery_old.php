<div class="subpages-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Photo Gallery</h3>
            </div>
            <div class="col-sm-6 hidden-xs hidden-sm ">
                <ol class="breadcrumb pull-right">
                    <li class="breadcrumb-item"> <a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">Photo Gallery</li>
                </ol>
            </div>
        </div>
    </div>
</div>



<div class="container">
    <div class="row">


        <div class="col-sm-8">
            <!--<h4 class="gallerytt">Photos</h4>-->
            <div class="row">
                <?php foreach ($albums as $key => $album) { ?>
                    <div class="col-xs-6 col-md-4">
                        <div class="thumb-gallery">
                            <a href="<?php echo base_url('gallery/' . $album['guId']); ?>" class="thumbnail">
                                <?php $src = $album['coverImage'] ? GALLERY_THUMB . '/' . $album['coverImage'] : 'public/images/400x300.jpg' ?>
                                <img src="<?php echo base_url($src); ?>" alt="...">
                            </a>
                            <div class="thumb-gallery-title">
                                <a href="<?php echo base_url('gallery/' . $album['guId']); ?>" title="<?php echo $album['name'] ?>"><?php echo $album['name'] ?></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>




                <!--                <div class="col-xs-6 col-md-4">
                                    <div class="thumb-gallery">
                                        <a href="#" class="thumbnail" style="padding:0px">
                                            <div class="img" style="width: 100%;height: 192px;background-image:url(<?php echo base_url('public/'); ?>images/thumb01.jpg);background-position:50% 25%;background-size: 100% auto;position: relative; ">
                                            </div>
                                        </a>
                                        <div class="thumb-gallery-title">
                                            <h3>Untitled album</h3>
                                        </div>
                                    </div>
                                </div>-->



            </div>
        </div>



        <div class="col-sm-offset-1 col-sm-3">
            <div class="right-side-bar">
                <h4>Time line</h4>
                <div class="row">
                    <div class="timeline-centered">


                        <?php foreach (array_reverse(range(2016, date('Y'))) as $key => $year) { ?>

                            <article class="timeline-entry">
                                <div class="timeline-entry-inner">
                                    <div class="timeline-icon bg-success"><i class="entypo-feather"></i></div>
                                    <div class="timeline-label"><a href="<?php echo base_url('gallery?year=' . $year); ?>"><?php echo $year ?></a></div>
                                </div>
                            </article>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>