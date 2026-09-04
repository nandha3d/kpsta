<main class="page-legacy">
<div class="subpages-banner">
    <div class="container">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <h3>ADHYAPAKA SABDHAM </h3>
                </div>

                <div class="col-sm-6 hidden-xs hidden-sm ">
                    <ol class="breadcrumb pull-right">
                        <li class="breadcrumb-item"> <a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active">Adhyapaka Sabdham </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="container">
    <div class="row">


        <div class="col-sm-8">

            <div class="callout callout-success">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <a class="read-more" target="_blank" href="https://as.kpsta.in">Click here For New Registration/Renewal</a>
                    </div>
                </div>
            </div>

            <?php foreach ($content as $key => $row) { ?>
                <div class="col-xs-6 col-md-4">
                    <div class="thumb-gallery">
                        <?php $uploadType = isset($row['upload_type']) ? $row['upload_type'] : '' ?>
                        <a target="_blank" href="<?php echo ($uploadType == 'file') ? base_url(ADAYAPAKA_SABHAM_FILE . '/' . $row['path']) : $row['path']; ?>" class="thumbnail">
                            <?php $src = $row['image'] ? ADAYAPAKA_SABHAM_IMAGE . '/' . $row['image'] : 'public/images/400x300.jpg' ?>
                            <img src="<?php echo base_url($src); ?>" alt="...">
                        </a>
                        <div class="thumb-gallery-title">
                            <a target="_blank" href="<?php echo ($uploadType == 'file') ? base_url(ADAYAPAKA_SABHAM_FILE . '/' . $row['path']) : $row['path']; ?>" title="<?php echo $row['description'] ?>"><?php echo $row['description'] ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>



        </div>




        <div class="col-sm-offset-1 col-sm-3">
            <div class="right-side-bar">
                <!--<h4>Time line</h4>-->
                <div class="row">
                    <div class="timeline-centered">


                        <?php // foreach (array_reverse(range(2016, date('Y'))) as $key => $year) { ?>

<!--                            <article class="timeline-entry">
                                <div class="timeline-entry-inner">
                                    <div class="timeline-icon bg-success"><i class="entypo-feather"></i></div>
                                    <div class="timeline-label"><a href="<?php // echo base_url('adayapaka_sabham?year=' . $year); ?>"><?php // echo $year ?></a></div>
                                </div>
                            </article>-->
                        <?php // } ?>

                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
</main>
