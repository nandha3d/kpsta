
<!-- Content Wrapper. Contains page content -->
<div class="container">
    <div class="row">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="row">
                <div class="col-md-12">

                    <div class="page-header  box-background ">
                        <div class="box-layout ">
                            <div class="col-xs-6 col-sm-6 col-md-6 va-m">
                                <!--<h3 class="pull-left"> Member's List</h3>-->

                                <div id="toolbar" class="toolbar text-right pull-left">
                                    <div class="std-toolbar btn-group">
                                        Configuration settings
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-6 col-sm-6 col-md-6 va-m">
                                <div id="toolbar" class="toolbar text-right">
                                    <div class="std-toolbar btn-group">


                                    </div>



                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

            </div>
        </section>








        <!-- Main content -->
        <section class="content">

            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Settings</h3>
                        </div>

                        <?php echo form_open(site_url('membership/settings/config'), ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'class' => 'form-horizontal']) ?> 

                        <div class="box-body col-md-10 col-sm-12">

                            <?php if (isset($formValues['error'])) { ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <h4><i class="icon fa fa-ban"></i> Error !</h4> <?php echo $formValues['error'] ?>  
                                </div>
                                <?php
                            }

                            if (isset($formValues['success'])) {
                                ?>
                                <div class="alert alert-success alert-dismissible">
                                    <h4><i class="icon fa fa-send"></i> Success !</h4> <?php echo $formValues['success'] ?>  
                                </div>
                                <?php
                            }
                            if (validation_errors()) {
                                ?>
                                <div class = "alert alert-danger alert-dismissible">
                                    <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
                                </div>
                            <?php } ?>
                            <div class="form-group"></div>
                            <div class="form-group">
                                <label  class="col-sm-4 control-label">Enable New Member Entry</label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            <?php $checkbox = isset($data['enable_entry']) ? $data['enable_entry'] : "" ?>

                                            <input type="checkbox" name="enable_entry"  <?php echo isset($data['enable_entry']) ? ($data['enable_entry'] ? "checked='checked'" : "" ) : "" ?>> By checking this button will enable to add member
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group"></div>
                            <div class="form-group">
                                <label  class="col-sm-4 control-label">Select Year</label>
                                <div class="col-sm-8">

                                    <label>
                                        <?php
                                        $yearArr = array_combine(range(2017, date('Y')), array_values(range(2017, date('Y'))));
                                        $selected = isset($data['year']) ? $data['year'] : "";
                                        echo form_dropdown('year', $yearArr, $selected, 'class="form-control category-search" style="width: 100%;"  data-placeholder="Select Year" ');
                                        ?>

                                    </label>

                                </div>
                            </div>

                            <div class="form-group"></div>
                        </div>

                        <div class="box-footer text-center ">
                            <button type="submit" class="col-sm-offset-2 btn btn-primary flash-news"   ><i class="fa fa-save "></i> Save</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>


                </div>
            </div>


        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>


<script type="text/javascript">
    $(function () {


    });
</script>
</div>
<!-- /.content-wrapper -->

