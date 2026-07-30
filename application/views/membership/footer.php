</div>
</div>
<div class="modal fade confirmation-modal in" id="delete" role="confirmation" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body text-center">
                <div class="message text-danger"></div>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger delete"  style="margin-right: 5px; margin-left: 5px;">Delete</button>
            </div>
        </div>
    </div>
</div>
<footer class="  main-footer ">
    <div class="pull-right hidden-xs">
        <!--<b>Version</b> 2.3.6-->
    </div>
    <strong>Copyright &copy; 2015-<?php echo date('Y') ?> <a href="#">KPSTA</a>.</strong> All rights
    reserved.
</footer>


<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- jQueryUI -->
<script src="<?php echo base_url(); ?>public/plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url(); ?>public/js/bootstrap.min.js"></script>

<script src="<?php echo base_url(); ?>public/plugins/datepicker/bootstrap-datepicker.js"></script>

<!-- PACE -->
<script src="<?php echo base_url(); ?>public/plugins/pace/pace.min.js"></script>

<!-- FastClick -->
<!--<script src="plugins/fastclick/fastclick.js"></script>-->
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>public/js/app.min.js" data-pace-options='{ "ajax": false }'></script>
<!-- Sparkline -->
<!--<script src="plugins/sparkline/jquery.sparkline.min.js"></script>-->
<!-- jvectormap -->
<!--<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>-->
<!--<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>-->
<!-- SlimScroll 1.3.0 -->
<script src="<?php echo base_url(); ?>public/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!--<script src="http://code.jquery.com/jquery-migrate-1.0.0.js"></script>-->
<!-- ChartJS 1.0.1 -->
<!--<script src="plugins/chartjs/Chart.min.js"></script>-->
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!--<script src="dist/js/pages/dashboard2.js"></script>-->
<!-- AdminLTE for demo purposes -->
<!--<script src="dist/js/demo.js"></script>-->


<!-- Custom -->
<script src="<?php echo base_url("public/js/custom-admin.js?v=".$configVars['asset_version']); ?>"> </script>

<?php
/* loading custom css files */
if (isset($special_js) && !empty($special_js)) {
    foreach ($special_js as $js) {
        echo '<script type="text/javascript" src="' . base_url("public/" . $js . '?v=' . $configVars['asset_version']) . '"  ></script>';
    }
}
?>
</body>
</html>
