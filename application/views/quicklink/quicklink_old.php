<div class="subpages-banner">
    <div class="container">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <h3>QUICK LINK</h3>
                </div>

                <div class="col-sm-6 hidden-xs hidden-sm ">
                    <ol class="breadcrumb pull-right">
                        <li class="breadcrumb-item"> <a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active">Quick links</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="container">
    <div class="row">


        <div class="col-sm-12">
            <div class="row">
                <ul id="courseStream" class="widget">
                    <?php foreach ($quicklink as $key => $link) { ?>
                        <li class="col-md-4 m-left "><a target="_blank" href="<?php echo $link['path'] ?>" class="pinkColor" title="<?php echo $link['description'] ?>"><?php echo $link['description'] ?></a></li>
                    <?php } ?>

                </ul>



            </div>


            <div class="order-list-pagination">
                <?php echo $links ?>
            </div>

        </div>



<!--        <div class="col-sm-offset-1 col-sm-3">
            <div class="right-side-bar">

            </div>
        </div>-->
    </div>
</div>