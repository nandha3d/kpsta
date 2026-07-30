<div class="subpages-banner">
    <div class="container">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <h3>District Office Bearers</h3>
                </div>

                <div class="col-sm-6 hidden-xs hidden-sm ">
                    <ol class="breadcrumb pull-right">
                        <li class="breadcrumb-item"> <a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active">District && Office Bearers</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="container">
    <div class="row">


        <div class="col-sm-8">




            <?php foreach ($content as $key => $district) { ?>
                <br>
                <h4 class="date-title"><span><?php echo $key ?></span>
                    <?php if (strlen($district['website'])) { ?>
                        <a class="pull-right" href="<?php echo $district['website'] ?>" target="_blank" >Website</a>
                    <?php } ?>
                </h4>
                <div class= "row">
                    <?php foreach ($district['officeBearer'] as $key1 => $row) { ?>
                        <div class="col-md-4 col-sm-3">
                            <div class="office-bearers-card">
                                <div class="card ">
                                    <img src="<?php echo OFFICE_BEARER . '/' . $row['image'] ?>" alt="" >
                                    <div class="name-captions">
                                        <span><?php echo ($row['designation'] == "GENERAL SECRETARY") ? "SECRETARY" : $row['designation'] ?></span>
                                    </div>
                                </div>
                                <div class="card-captions">
                                    <h5 title="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></b></h5>
                                    <?php if ($row['email']) { ?>
                                        <p title="<?php echo $row['email'] ?>"><i class="fa fa-envelope-o"></i> <?php echo $row['email'] ?> </p>
                                    <?php } ?>

                                    <?php if ($row['phone']) { ?>     
                                        <p title="<?php echo $row['phone'] ?>"><i class="fa fa-phone"></i> <?php echo $row['phone'] ?></p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>



        </div>



        <div class="col-sm-offset-1 col-sm-3">
            <div class="right-side-bar">

            </div>
        </div>
    </div>
</div>