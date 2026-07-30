
<!-- Content Wrapper. Contains page content -->
<div class="container">
    <div class="row">
        <input type="hidden" name="url_type" id="url_type" value="<?php echo base_url('admin/order-circular/' . $this->uri->segment(3)); ?>">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="row">
                <div class="col-md-12">

                    <div class="page-header  box-background ">
                        <div class="box-layout ">
                            <div class="col-xs-7 col-sm-7 col-md-7 va-m">
                                <!--<h3 class="pull-left"> Member's List</h3>-->

                                <div id="toolbar" class="toolbar text-right pull-left">
                                    <div class="std-toolbar btn-group">


                                        <?php // if ($this->aauthGroupId == 1) { ?>
                                        <div class="btn-group view-btn-group" data-toggle="buttons" id="tab">

                                            <a class="btn btn-default  entered-btn <?php echo $view == 1 ? "active" : "" ?>" data-active-class="success" href="#entered" data-toggle="tab" <?php  echo ($year != $yearConfig) ? "style=display:none" : "" ?>>
                                                <input name="view_type" value="1"   type="radio"  <?php echo $view == 1 ? "checked" : "" ?>>   <i class="fa fa-futbol-o"></i><span> Entered</span>
                                            </a>
                                            <a class="btn btn-default confirmed-btn <?php echo $view == 2 ? "active" : "" ?>" data-active-class="success" href="#confirmed" data-toggle="tab">
                                                <input name="view_type" value="2"  type="radio"  <?php echo $view == 2 ? "checked" : "" ?>> <i class="fa fa-dot-circle-o"></i><span> Approved by Sub Dist</span></a>
                                            </a>
<!--                                            <a class="btn btn-default  <?php // echo $view == 6 ? "active" : "" ?>" data-active-class="success" href="#checked" data-toggle="tab">
                                                <input name="view_type" value="6"  type="radio"  <?php // echo $view == 2 ? "checked" : "" ?>> <i class="fa fa-dot-circle-o"></i><span> Checked</span></a>
                                            </a>-->
                                            <a class="btn btn-default  <?php echo $view == 3 ? "active" : "" ?>" data-active-class="success" href="#verified" data-toggle="tab">
                                                <input name="view_type" value="3"  type="radio"  <?php echo $view == 3 ? "checked" : "" ?>> <i class="fa fa-tint"></i><span> Approved by Rev. Dist</span>
                                            </a>
                                            <a class="btn btn-default  <?php echo $view == 4 ? "active" : "" ?>" data-active-class="success" href="#approved" data-toggle="tab">
                                                <input name="view_type" value="4"  type="radio"  <?php echo $view == 4 ? "checked" : "" ?>>  <i class="fa fa-check"></i><span> Approved by State</span>
                                            </a>
<!--                                            <a class="btn btn-default <?php // echo $view == 5 ? "active" : "" ?>" data-active-class="success" href="#all" data-toggle="tab">
                                                <input name="view_type" value="5"   type="radio" <?php // echo $view == 5 ? "checked" : "" ?> >   <i class="fa fa-futbol-o"></i><span> All</span>
                                            </a>-->
                                        </div>


                                        <?php // } else if ($this->aauthGroupId == 2) { ?>
                                        <!--Members List-->
                                        <!--                                            <a  class="btn   active btn-info"   data-toggle="modal" data-target="#modalProcess" data-model-title='Confirm members details' data-button-label='Confirm'>-->
                                                                                        <!--<i class="fa fa-check"></i> <span class="hidden-xs hidden-sm"> Confirm</span>-->
                                        <!--</a>-->
                                        <?php // } ?>

                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-5 va-m">
                                <div id="toolbar" class="toolbar text-right">
                                    <div class="std-toolbar btn-group">
<!--                                        <a  class="btn btn-default download"  data-type='pdf' data-href="<?php // echo base_url('membership/teacher/generate_pdf')                                              ?>">
                                            <i class="fa fa-print"></i> <span class="hidden-xs hidden-sm">Report</span>
                                        </a>-->
                                        <div class="dropdown-toolbar report-menu">
                                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-nospin  dropdown-toggle" type="button"><i class="fa fa-print"></i> Report Menu<i class="fa fa-caret-down"></i></button>
                                            <ul role="menu" class="page-list-actions dropdown-menu dropdown-menu-left">
                                                <li> <a class=" download " data-type='pdf' data-href="<?php echo base_url('membership/teacher/generate_pdf') ?>" data-aauthGroup=" <?php echo $this->aauthGroupId ?>"><span><i class="fa fa-group"></i> Membership list Report</span></a></li>
                                                <li> <a class=" download " data-type='pdf' data-href="<?php echo base_url('membership/teacher/designation_report') ?>"><span><i class="fa fa-sticky-note"></i> Designation wise Report</span></a></li>
                                                <?php if (in_array($this->aauthGroupId, [1])) { ?>
                                                    <li>    <a class=" download " data-group_view="2" data-type='pdf' data-href="<?php echo base_url('membership/teacher/consolidation_report') ?>"><span><i class="fa fa-chain"></i> Consolidation - District Wise Report</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array($this->aauthGroupId, [1, 2, 3])) { ?>
                                                    <li>  <a class=" download" data-group_view="4" data-type='pdf' data-href="<?php echo base_url('membership/teacher/consolidation_report') ?>"><span><i class="fa fa-chain"></i> Consolidation - Sub District Wise Report</span></a></li>
                                                <?php } ?>
                                                <?php if (in_array($this->aauthGroupId, [1, 2, 3, 4])) { ?>
                                                    <li>  <a class=" download " data-group_view="6" data-type='pdf' data-href="<?php echo base_url('membership/teacher/consolidation_report') ?>"><span><i class="fa fa-chain"></i> Consolidation - School Wise Report</span></a></li>
                                                <?php } ?>

                                            </ul>
                                        </div>
                                    </div>


                                    <?php if ($enable_entry && $this->session->userdata('group') != 7) { ?>
                                        <div class="std-toolbar btn-group">
                                            <a   href="<?php echo site_url("membership/teacher/add_bulk"); ?>"   class="btn btn-default btn-nospin   " type="button"><i class="fa fa-plus-circle"></i> Add Member</span></a>
<!--                                            <a  class="btn btn-default" data-toggle="modal" data-target="#modal" data-id="new" data-title-new="Add">
                                                <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">New</span>
                                            </a>-->
<!--                                            <div class="dropdown-toolbar btn-group">
                                                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                                <ul role="menu" class="dropdown-menu dropdown-menu-right"> </ul>
                                            </div>-->
                                        </div>
                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

            </div>
        </section>


        <div class="modal fade" id="modal" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" >
            <div class="modal-dialog">
                <div class="modal-content modal-content-form" >
                    <?php echo isset($form) ? $form : '' ?>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">

            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <div class="box-layout">
                                <div class="col-xs-12 col-sm-12 col-lg-8 va-m form-inline">
                                    <div class="input-group pull-left col-md-3 col-xs-12">
                                        <!--<span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-question-circle"></i></button></span>-->
                                        <input type="text" class="form-control" placeholder="Search..." value="<?php echo isset($search) ? $search : ''?>" name="search" data-href="<?php echo base_url('membership/teacher') ?>">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-flat" name="search"><i class="fa fa-search fa-fw"></i></button>
                                        </span>
                                    </div>

                                    <div class="form-group col-md-3 col-xs-12">
                                        <?php $selected = isset($groupId) ? $groupId : '' ?>
                                        <?php echo form_dropdown('group-search', $groupSearch, $selected, 'class="form-control " id="group-search" style="width: 100%;"  data-placeholder="Search by Group" data-action="teacher" data-href="' . base_url('membership/aauth/getOffice') . '"     '); ?>
                                    </div>

                                    <div class="form-group col-md-3 col-xs-12">
                                        <?php $selected = isset($officeId) ? $officeId : '' ?>
                                        <?php echo form_dropdown('office-search', $officeSearch, $selected, 'class="form-control" id="office-search" style="width: 100%;"  data-placeholder="Search by Office" '); ?>
                                    </div>

                                    <div class="form-group col-md-2 col-xs-12">
                                        <?php
                                        $selected = isset($year) ? $year : '';
                                        $yearArr = array_combine(range(2017, date('Y')), array_values(range(2017, date('Y'))));

                                        echo form_dropdown('year-search', $yearArr, $selected, 'class="form-control " id="year-search" style="width: 100%;"  data-placeholder="Sort by Date" ');
                                        ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-lg-4 va-m text-right">
                                   
                                    <div class="tab-content" <?php  echo ($year != $yearConfig) ? "style=display:none" : "" ?>>
                                        <div class="tab-pane" id="all"></div>

                                        <div class="tab-pane" id="entered">
                                             <?php if ($enable_entry && $this->session->userdata('group') != 7) { ?>
                                            <div style="display: inline-block;margin-right: 20px;">
                                                <button class="btn btn-sm btn-danger btn-nospin modal-delete-btn" type="button" data-toggle="modal" data-target="#delete" data-href="<?php echo base_url('membership/teacher/delete') ?>" <?php  echo ($year != $yearConfig) ? "style=display:none" : "" ?> data-year="<?php echo $year ?>" data-year-config="<?php echo $yearConfig ?>">
                                                    <i class="fa fa-remove"></i> Delete
                                                </button>
                                            </div>
                                            <?php } ?>
                                            
                                            <?php if (in_array($this->aauthGroupId, [1, 2, 4]) && $this->session->userdata('group') != 7) { ?>

                                                <div class="std-toolbar btn-group ">
                                                    <a class="btn btn-sm btn-primary" data-href="<?php echo base_url('membership/teacher/process') ?>"   data-toggle="modal" data-target="#process-modal"  data-precheck="" data-message="Confirm selected merber's ?" data-confirm-text="Confirm"  data-cancel-text="Cancel" data-process="confirm" >
                                                        <span data-toggle="tooltip" title="" data-placement="left"><i class="fa   fa-check"></i> <span class="">Confirm</span></span>
                                                    </a>  
                                                    <div class="dropdown-toolbar btn-group">
                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-sm btn-primary btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                            <?php if ($this->aauthGroupId == 1) { ?>
                                                                <li> <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Confirm By District wise ?" data-confirm-text="Confirm" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="confirm" data-process-group="2"><span><i class="fa fa-chain"></i> Rev. Dist Wise</span></a> </li>
                                                            <?php } ?>
                                                            <li> <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Confirm By Sub Dist wise ?" data-confirm-text="Confirm" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="confirm" data-process-group="4"><span><i class="fa fa-chain"></i> Sub Dist Wise</span></a> </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            
                                            
                                        </div>

                                        <!--<div class="tab-pane" id="confirmed">-->
                                            <?php // if (in_array($this->aauthGroupId, [1, 2, 3, 4]) && $this->session->userdata('group') != 7) { 
//                                                if (in_array($this->aauthGroupId, [1, 2, 3]) && $this->session->userdata('group') != 7) { ?>
<!--                                                <div class="std-toolbar btn-group ">
                                                    <a  href="javascript:void(0)" class="btn btn-sm btn-primary" data-href="<?php // echo base_url('membership/teacher/process') ?>"   data-toggle="modal" data-target="#process-modal"  data-precheck="" data-message="Check selected merber's ?" data-confirm-text="Check" data-cancel-text="Cancel" data-process="check" >
                                                        <span data-toggle="tooltip" title="" data-placement="left"><i class="fa   fa-check"></i> <span class="">Check</span></span>
                                                    </a> 
                                                    <div class="dropdown-toolbar btn-group">
                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-sm btn-primary btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                            <?php // if ($this->aauthGroupId == 1) { ?>
                                                                <li> <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Check By District wise ?" data-confirm-text="Check" data-href="<?php // echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="check" data-process-group="2"><span><i class="fa fa-chain"></i> Rev. Dist Wise</span></a> </li>
                                                            <?php // } ?>
                                                            <li>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Check By Sub Dist wise ?" data-confirm-text="Check" data-href="<?php // echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="check" data-process-group="4"><span><i class="fa fa-chain"></i> Sub Dist Wise</span></a>
                                                            </li>
                                                        </ul>
                                                    </div>-->
                                                <!--</div>-->
                                                 <?php // } ?>
<!--                                                <div class="std-toolbar btn-group ">
                                                    <a  href="javascript:void(0)" class="btn btn-sm btn-danger" data-href="<?php // echo base_url('membership/teacher/process') ?>"   data-toggle="modal" data-target="#process-modal" data-message="Reject confirmed merber's ?" data-confirm-text="Reject"   data-cancel-text="Cancel" data-process="confirm" data-action="reject" >
                                                        <span data-toggle="tooltip" title="" data-placement="left"><i class="fa   fa-remove"></i> <span class="">Reject</span></span>
                                                    </a> 
                                                    <?php // if (in_array($this->aauthGroupId, [1, 2, 3]) && $this->session->userdata('group') != 7) { ?>
                                                    <div class="dropdown-toolbar btn-group">
                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-sm btn-danger btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                            <?php // if ($this->aauthGroupId == 1) { ?>
                                                                <li><a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject confirmed By District wise ?" data-confirm-text="Reject" data-href="<?php // echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="confirm" data-process-group="2" data-action="reject"><span><i class="fa fa-chain"></i> Rev. Dist Wise</span></a>
                                                                </li>
                                                            <?php // } ?>
                                                            <li>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject confirmed By Sub Dist wise ?" data-confirm-text="Reject" data-href="<?php // echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="confirm" data-process-group="4" data-action="reject"><span><i class="fa fa-chain"></i> Sub Dist Wise</span></a>
                                                            </li>

                                                        </ul>
                                                    </div>
                                                    <?php // } ?>
                                                </div>-->
                                            <?php // } ?>
                                        <!--</div>-->
                                        <div class="tab-pane" id="confirmed">
                                            <?php if (in_array($this->aauthGroupId, [1, 2, 4]) && $this->session->userdata('group') != 7) { ?>
                                             <?php if (in_array($this->aauthGroupId, [1, 2]) && $this->session->userdata('group') != 7) { ?>
                                                <div class="std-toolbar btn-group ">
                                                    <a  href="javascript:void(0)" class="btn btn-sm btn-primary" data-href="<?php echo base_url('membership/teacher/process') ?>"   data-toggle="modal" data-target="#process-modal"  data-precheck="" data-message="Verify selected merber's ?" data-confirm-text="Verify" data-cancel-text="Cancel" data-process="verify" >
                                                        <span data-toggle="tooltip" title="" data-placement="left"><i class="fa   fa-check"></i> <span class="">Verify</span></span>
                                                    </a> 
                                                    <div class="dropdown-toolbar btn-group">
                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-sm btn-primary btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                            <?php if ($this->aauthGroupId == 1) { ?>
                                                                <li> <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Verify By District wise ?" data-confirm-text="Verify" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="verify" data-process-group="2"><span><i class="fa fa-chain"></i> Rev. Dist Wise</span></a> </li>
                                                            <?php } ?>
                                                            <li>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Verify By Sub Dist wise ?" data-confirm-text="Verify" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="verify" data-process-group="3"><span><i class="fa fa-chain"></i> Edu. Dist Wise</span></a>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Verify By Sub Dist wise ?" data-confirm-text="Verify" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="verify" data-process-group="4"><span><i class="fa fa-chain"></i> Sub Dist Wise</span></a>

                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                             <?php } ?>

                                                <div class="std-toolbar btn-group ">
                                                    <a  href="javascript:void(0)" class="btn btn-sm btn-danger" data-href="<?php echo base_url('membership/teacher/process') ?>"   data-toggle="modal" data-target="#process-modal" data-message="Reject confirmed merber's ?" data-confirm-text="Reject"   data-cancel-text="Cancel" data-process="confirm" data-action="reject" >
                                                        <span data-toggle="tooltip" title="" data-placement="left"><i class="fa fa-remove"></i> <span class="">Reject</span></span>
                                                    </a> 
                                                    <div class="dropdown-toolbar btn-group">
                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-sm btn-danger btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                            <?php if ($this->aauthGroupId == 1) { ?>
                                                                <li><a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject confirmed By District wise ?" data-confirm-text="Reject" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="confirm" data-process-group="2" data-action="reject"><span><i class="fa fa-chain"></i> Rev. Dist Wise</span></a></li>
                                                            <?php } ?>
                                                            <?php if (in_array($this->aauthGroupId, [1, 2]) && $this->session->userdata('group') != 7) { ?>
                                                                <li><a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject confirmed By Edu Dist wise ?" data-confirm-text="Reject" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="confirm" data-process-group="3" data-action="reject"><span><i class="fa fa-chain"></i> Edu. Dist Wise</span></a></li>
                                                            <?php } ?>
                                                            <li><a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject confirmed By Sub Dist wise ?" data-confirm-text="Reject" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="confirm" data-process-group="4" data-action="reject"><span><i class="fa fa-chain"></i> Sub Dist Wise</span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="tab-pane" id="verified">
                                            <?php if (in_array($this->aauthGroupId, [1, 2]) && $this->session->userdata('group') != 7) { ?>
                                            <?php if (in_array($this->aauthGroupId, [1]) && $this->session->userdata('group') != 7) { ?>
                                                <div class="std-toolbar btn-group ">
                                                    <a  href="javascript:void(0)" class="btn btn-sm btn-primary" data-href="<?php echo base_url('membership/teacher/process') ?>"   data-toggle="modal" data-target="#process-modal"   data-message="Approve selected merber's ?" data-confirm-text="Approve"   data-cancel-text="Cancel" data-process="approve" >
                                                        <span data-toggle="tooltip" title="" data-placement="left"><i class="fa   fa-check"></i> <span class="">Approve</span></span>
                                                    </a>  
                                                    <div class="dropdown-toolbar btn-group">
                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-sm btn-primary btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                            <li>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Approve By Dist wise ?" data-confirm-text="Approve"   data-cancel-text="Cancel" data-process="approve" data-process-group="2" data-href="<?php echo base_url('membership/teacher/process') ?>"  ><span><i class="fa fa-chain"></i> Rev. Dist Wise</span></a>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Approve By Education Dist wise ?" data-confirm-text="Approve"   data-cancel-text="Cancel" data-process="approve" data-process-group="3" data-href="<?php echo base_url('membership/teacher/process') ?>"  ><span><i class="fa fa-chain"></i> Edu. Dist Wise</span></a>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Approve By Sub Dist wise ?" data-confirm-text="Approve"   data-cancel-text="Cancel" data-process="approve" data-process-group="4" data-href="<?php echo base_url('membership/teacher/process') ?>"  ><span><i class="fa fa-chain"></i> Sub Dist Wise</span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                              <?php } ?>

                                                <div class="std-toolbar btn-group ">
                                                    <a  href="javascript:void(0)" class="btn btn-sm btn-danger" data-href="<?php echo base_url('membership/teacher/process') ?>"   data-toggle="modal" data-target="#process-modal"  data-precheck="" data-message="Reject verified merber's ?" data-confirm-text="Reject" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-process="verify" data-action="reject" >
                                                        <span data-toggle="tooltip" title="" data-placement="left"><i class="fa   fa-remove"></i> <span class="">Reject</span></span>
                                                    </a> 
                                                    <div class="dropdown-toolbar btn-group">
                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-sm btn-danger btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                            
                                                            <li>
                                                                <?php if (in_array($this->aauthGroupId, [1]) && $this->session->userdata('group') != 7) { ?>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject Verifed By District wise ?" data-confirm-text="Reject" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="verify" data-process-group="2" data-action="reject"><span><i class="fa fa-chain"></i> Rev. Dist Wise</span></a>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject Verifed By Sub District wise ?" data-confirm-text="Reject" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="verify" data-process-group="3" data-action="reject"><span><i class="fa fa-chain"></i> Edu Dist Wise</span></a>
                                                               <?php } ?> 
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject Verifed By Edu District wise ?" data-confirm-text="Reject" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="verify" data-process-group="4" data-action="reject"><span><i class="fa fa-chain"></i> Sub Dist Wise</span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        
                                        <?php } ?>
                                        
                                        
                                        <?php if (in_array($this->aauthGroupId, [1, 2]) && $this->session->userdata('group') != 7) { ?>

                                            <div class="tab-pane" id="approved">
                                                <div class="std-toolbar btn-group ">
                                                    <a  href="javascript:void(0)" class="btn btn-sm btn-danger" data-href="<?php echo base_url('membership/teacher/process') ?>"   data-toggle="modal" data-target="#process-modal"  data-precheck="" data-message="Reject approved merber's ?" data-confirm-text="Reject" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-process="approve" data-action="reject" >
                                                        <span data-toggle="tooltip" title="" data-placement="left"><i class="fa   fa-remove"></i> <span class="">Reject</span></span>
                                                    </a>  
                                                    <div class="dropdown-toolbar btn-group">
                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-sm btn-danger btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                            <li>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject Approved By District wise ?" data-confirm-text="Reject" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="approve" data-process-group="2" data-action="reject"><span><i class="fa fa-chain"></i> Rev. Dist Wise</span></a>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject Approved By Edu District wise ?" data-confirm-text="Reject" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="approve" data-process-group="3" data-action="reject"><span><i class="fa fa-chain"></i> Edu. Dist Wise</span></a>
                                                                <a href="#"  data-toggle="modal" data-target="#process-modal" data-message="Reject Approved By Sub District wise ?" data-confirm-text="Reject" data-href="<?php echo base_url('membership/teacher/process') ?>"  data-cancel-text="Cancel" data-process="approve" data-process-group="4" data-action="reject"><span><i class="fa fa-chain"></i> Sub Dist Wise</span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>


                                    </div>
                                </div>



                            </div>


                        </div>

                        <div class="overlay" >
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body" id="table-content">
                            <?php echo $content; ?>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                        </div>
                        <!-- /.box-footer -->
                    </div>
                </div>
            </div>


        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>


<div class="modal fade process-modal in" id="process-modal"  role="confirmation" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>

            <div class="message text-danger"></div>

            <div class="modal-body text-center form-office hide">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal  " >
                            <div class="form-group">
                                <label class="control-label col-sm-3" id="label-office-process" ><?php echo $reg['officeLabel'] ?></label>
                                <div class="col-sm-7">
                                    <?php echo form_dropdown('office_id', $officeSelect, '', 'class="form-control select2 " id="select2-office-process" style="width: 100%;"  required= "required" '); ?>
                                </div>
                                <input type="hidden" id="group-search">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">

                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger confirm"  style="margin-right: 5px; margin-left: 5px;">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade dowload-modal in" id="download-modal"  role="confirmation" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">SELECT SUB DISTRICT TO GENERATE MEMBERSHIP PDF</h4>
            </div>

            <div class="message text-danger"></div>

            <div class="modal-body text-center form-office hide">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal  " >
                            <div class="form-group">
                                <label class="control-label col-sm-3" id="label-office-process" ><?php echo $reg['officeLabel'] ?></label>
                                <div class="col-sm-7">
                                    <?php echo form_dropdown('office_id', $officeSelect, '', 'class="form-control select2 " id="select2-office-process" style="width: 100%;"  required= "required" '); ?>
                                </div>
                                <input type="hidden" id="group-search">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">

                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger confirm"  style="margin-right: 5px; margin-left: 5px;" data-href="<?php echo base_url('membership/teacher/generate_pdf') ?>">Delete</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        var configYear = "<?php echo $configYear ?>";
        $('#tab a.active').tab('show');

        $("#select2-office-process").select2({
            dropdownParent: $("#process-modal")
        });

        $('.category-search').select2().on("change", function (e) {
            seach(0);
        });

        $('#year-search').select2().on("change", function (e) {
            seach(0);
        });
        
//        yearChange();
    });
</script>