<div class="subpages-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Contact us</h3>
            </div>

            <div class="col-sm-6 hidden-xs hidden-sm ">
                <ol class="breadcrumb pull-right">
                    <li class="breadcrumb-item"> <a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">Contact us</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="gallerytti">Kerala Pradesh School Teachers' Association</h4>
                    <p>
                        Recognised as per GO.(MS) 289/63 dt. 29.04.1963 &amp; <br>Govt. Letter No. 556498/J3/2016 G.Edn. dt.10.06.2016<br>
                        <a target="_blank" href="www.kpsta.in">www.kpsta.in </a><br>
                        E-mail: <a href="mailto:kpsta.in@gmail.com">kpsta.in@gmail.com</a>
                    </p>

                    <h4 class="gallerytti">  State Committee Office  </h4>
                    <p>
                        KPSTA BHAVAN,<br>
                        Chinmaya School Lane, Kunnumpuram,<br>
                        Trivandrum -1,Ph. 0471 -2575797
                    </p>

                    <h4 class="gallerytti">  Office Annex </h4>
                    <p>
                        1.KPSTA BHAVAN,<br>
                        Pulimoodu,Unni Lane, Tvm-1 <br><br>
                        2.KPSTA BHAVAN, DHARMALAYAM ROAD,<br>
                        OPP. Ayurveda College.
                        Tvm-1
                    </p>

                    <h4 class="gallerytti">   Centre Office</h4>
                    <p>
                        KPSTA Centre Office,<br>
                        Carrier Station Road,
                        Kochi -16<br>
                        Ph.0484 -2375817
                    </p>

                </div>

                <div class="col-sm-6">
                    <div class="callout callout-success">
                        <div class="row">

                            <?php foreach ($officeBearer as $row) { ?>

                                <div class="col-md-4 col-sm-6 col-xs-6">
                                    <div class="office-bearers-info">
                                        <span><?php echo ($row['designation'] == "GENERAL SECRETARY") ? "GEN . SECRETARY" : $row['designation'] ?></span>
                                        <h5><?php echo $row['name'] ?></h5>
                                        <span>
                                            <?php
                                            if (!empty($row['phone'])) {
                                                echo "PH:" . $row['phone'];
                                            }
                                            if (!empty($row['email'])) {
                                                echo "<br>" . $row['email'];
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                            <?php } ?>


                            <!--                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                            <div class="office-bearers-info">
                                                                <span>SECRETARY</span>
                                                                <h5>M SALAHUDHEEN</h5>
                                                                <span>PH: 789456133 <br>test@gmail.com</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-6">
                                                            <div class="office-bearers-info">
                                                                <span>TREASURER</span>
                                                                <h5>AK ABDUL SAMAD</h5>
                                                                <span>PH: 789456133 <br>test@gmail.com</span>
                                                            </div>
                                                        </div>-->
                        </div>
                        <a class="read-more m-top" href="<?php echo base_url('office_bearer') ?>">View All Office Bearers</a>
                    </div>


                    <?php if ($isMailSend) { ?>

                        <h4 class="gallerytti" style="font-size:14px;text-align: center;margin-top: 40px;">Thank you, <b><?php echo $mailInfo['name'] ?></b></h4>
                        <p style="font-size:14px;text-align: center;"> We will reply you soon </p>

                    <?php } else {
                        ?>
                        <h4 class="gallerytti">Send Enquiry</h4>
                        <?php if (validation_errors()) {
                            ?>
                            <div class = "alert alert-danger alert-dismissible">
                                <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
                            </div>
                        <?php } ?>

                        <?php echo form_open(base_url('contact'), ['autocomplete' => 'off']) ?> 
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <?php $class = form_error('name') ? 'form-group has-error' : 'form-group' ?>
                                <div class="<?php echo $class ?> ">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input  placeholder="Enter name"  name="name" class="form-control" value="<?php echo set_value('name') ?>">

                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <?php $class = form_error('email') ? 'form-group has-error' : 'form-group' ?>
                                <div class="<?php echo $class ?> ">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input  placeholder="Enter email" name="email"  class="form-control" value="<?php echo set_value('email') ?>">
                                </div>
                            </div>

                        </div>


                        <div class="form-group">
                            <label for="exampleInputEmail1">Address</label>
                            <input  placeholder="Enter address"  name="address" class="form-control" value="<?php echo set_value('address') ?>">
                        </div>

                        <?php $class = form_error('content') ? 'form-group has-error' : 'form-group' ?>
                        <div class="<?php echo $class ?> ">
                            <label for="exampleInputEmail1">Content <span class="text-danger">*</span></label>
                            <textarea rows="3" placeholder="Enter content"  name="content" class="form-control" value="<?php echo set_value('content') ?>"></textarea>
                        </div>

                        <button type="submit" class="submit" class="btn btn-primary"  >Send</button>
                        <?php
                        echo form_close();
                    }
                    ?>


                </div>

            </div>


            <br>
        </div>

    </div>


    <div class="row">
        <div class="col-sm-6">

            <div style="border:1px solid #ddd; padding:10px; ">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3946.090812232524!2d76.94294483040326!3d8.49055112745986!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3b05bba3138c2443%3A0xb10658bc028f321f!2sKPSTA+BHAVAN+-KPSTA+State+Committee+Office!5e0!3m2!1sen!2sin!4v1480273860880" width="100%" height="200" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
        </div>


        <div class="col-sm-6">
<!--            <div class="callout callout-success">
                <h5>Behind this website</h5>
                <div class="row">
                    <div class="col-md-4 col-sm-6" >
                        <p><b>Co-ordinator</b><br><span>Vinod Pichinattu,<br> PH: 9946548626</span></p>
                    </div>
                    <div class="col-md-4 col-sm-6" >
                        <p><b>Chairman</b><br><span>Padmakumar,<br> PH: 8848662638</span></p>
                    </div>
                    <div class="col-md-4 col-sm-6" >
                        <p><b>Convenor </b><br><span>SunilKumar,<br> PH: 9495053963</span></p>
                    </div>
                </div>
            </div>-->
        </div>


    </div>


</div>


