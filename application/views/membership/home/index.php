

<!-- Content Wrapper. Contains page content -->
<div class="container">


    <!-- Main content -->
    <section class="content">
        <div class="row row-membership-count">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="ion ion-ios-people"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">State </span>
                        <span class="info-box-number"><?php echo $membership['data']['total']['tlApproved'] ?><small> </small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="ion ion-ios-people"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Rev. District </span>
                        <span class="info-box-number"><?php echo $membership['data']['total']['tlVerified'] ?><small> </small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
<!--            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua-active"><i class="ion ion-ios-people"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Edu. District </span>
                        <span class="info-box-number"><?php // echo $membership['data']['total']['tlChecked'] ?><small> </small></span>
                    </div>
                     /.info-box-content 
                </div>
                 /.info-box 
            </div>-->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua-gradient"><i class="ion ion-ios-people"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Sub District </span>
                        <span class="info-box-number"><?php echo $membership['data']['total']['tlConfirmed'] ?><small> </small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">

                <div class="box membership-box">
                    <div class="box-header with-border box-header-process" style="cursor: pointer">
                        <div class="col-md-12 va-m form-inline">
                            <h3 class="box-title">Membership - <?php echo $membership['data']['officeName'] .' '. $membership['data']['groupName'] ?></h3>
                            <div class="box-tools pull-right">
                                 <?php
                                $selected = isset($membership['year']) ? $membership['year'] : '';
                                $yearArr = array_combine(range(2017, date('Y')), array_values(range(2017, date('Y'))));
                                echo form_dropdown('year-search', $yearArr, $selected, 'class="form-control " id="year-search" style="width: 100%;"  data-placeholder="Sort by Date" ');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="overlay" >
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive table-responsive-membership " >
                            <?php echo $membership['table']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="col-xs-6 col-lg-8 va-m form-inline">
                            <h3 class="box-title">What's New</h3>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="load-task-top-chart" data-source="/task/top-chart" class="custom-scrollbar" style="min-height: auto;max-height: 235px">
                            <?php foreach ($whatsNew as $row) { ?>
                                <div class="row">
                                    <div class="col-md-1">
                                        <p class="textAvatar" data-toggle="tooltip" title="" data-image-size="40" style="color: rgb(255, 255, 255); background-color: rgb(248, 89, 49); border: 0px solid rgb(221, 221, 221); display: inline-block; font-family: Arial,&quot;Helvetica Neue&quot;,Helvetica,sans-serif; font-size: 12px; border-radius: 40px; width: 30px; height: 30px; line-height: 30px; margin: 4px 0px 0px 15px; text-align: center; float: left; text-transform: uppercase;"  >
                                            <i class="fa fa-lg fa-newspaper-o   "></i>
                                        </p>
                                    </div>
                                    <div class="col-md-11">
                                        <p>
                                            <?php if ($row['file_name']) { ?>
                                                <?php $view = base_url(MEMBERSHIP_PATH) . '/' . $row['file_name'] ?>
                                                <a href="<?php echo $view ?>"  target="_blank" data-toggle="ajax"  >
                                                    <?php echo $row['content'] ?> 
                                                </a>


                                                <?php
                                            } else {
                                                echo $row['content'];
                                            }
                                            ?>
                                        </p>

                                    </div>
                                </div>
                            <?php } ?>


                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6">


                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-layout">
                            <div class="col-xs-6 col-lg-8 va-m form-inline">
                                <h3 class="box-title text-left">Office Bearers</h3>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">

                        <?php foreach ($officeBearer as $row) { ?>
                            <div class="col-md-6  pull-left">
                                <div class="row">
                                    <div class="col-sm-12 ">
                                        <p>
                                            <b><?php echo $row['designation'] ?></b><br>
                                            <span style="text-transform: uppercase;"><?php echo $row['name'] ?><br> PH: <?php echo $row['phone'] ?></span>
                                        </p>
                                    </div>

                                </div>
                            </div>

                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-layout">
                            <div class="col-xs-6 col-lg-8 va-m form-inline">
                                <h3 class="box-title text-left">For Technical Support</h3>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-12">
<!--                            <div class="row">
                                <div class="col-sm-6">
                                    <p><b>Website Chairman</b><br>
                                        <span>Vinod Pichinattu, PH: 9946548626</span></p>
                                </div>

                            </div>-->
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<div class="modal fade  table-membership-modal"  role="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-bold">Membership</h4>
            </div>
            <div class="modal-body text-center">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('body').on('click', '.office-name', function(){
        var year = $(this).data('year');
        var group = $(this).data('group');
        var office = $(this).data('office');
        
        if($(this).data('group') == "6"){
            window.open('<?php echo base_url('membership/teacher')?>?year='+ year +'&group=' + group + '&office=' + office + '&view=1' , '_blank');
            return false;
        }
        $table = $('.table-membership-modal');
        $table.find('.modal-title ').html('Membership');
        $table.find('.modal-body').html('<b class="text-center">Loading data...</b>');
        $table.modal();
        $.ajax({
            url: '<?php echo base_url('membership/home/membershipcount') ?>',
            type: 'GET',
            dataType: 'json',
            data: {office: $(this).data('office'), group: $(this).data('group'), year: $('#year-search').val()},
            success: function(res){
                $table.find('.modal-title').append(' - '+res.data.officeName +' '+ res.data.groupName);
                $table.find('.modal-body').html(res.table);
            }
        })
    });
    
    $('body').on('change', '#year-search', function(){
        $('.membership-box').find('.overlay').show();
        $.ajax({
            url: '<?php echo base_url('membership/home/membershipcount') ?>',
            type: 'GET',
            dataType: 'json',
            data: {year: $(this).val()},
            complete: function(){
              $('.membership-box').find('.overlay').hide();  
            },
            success: function(res){
                $('.table-responsive-membership').html(res.table);
            }
        })
    });

</script>