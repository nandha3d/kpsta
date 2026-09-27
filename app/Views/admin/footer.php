                </main>
                <footer class="modern-footer">
                    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="<?php echo site_url(); ?>" target="_blank" rel="noopener">KPSTA</a>.</strong> All rights reserved.
                </footer>
            </div> <!-- End of modern-main-content -->
        </div> <!-- End of modern-wrapper -->

        <div class="modal fade confirmation-modal" id="delete" role="confirmation" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body text-center">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger delete"  style="margin-right: 5px; margin-left: 5px;">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQueryUI -->
        <script src="<?php echo base_url(); ?>public/plugins/jQueryUI/jquery-ui.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="<?php echo base_url(); ?>public/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>public/plugins/datepicker/bootstrap-datepicker.js"></script>
        <!-- PACE -->
        <script src="<?php echo base_url(); ?>public/plugins/pace/pace.min.js"></script>
        <!-- AdminLTE App (Keep for legacy plugins) -->
        <script src="<?php echo base_url(); ?>public/js/app.min.js" data-pace-options='{ "ajax": false }'></script>
        <!-- SlimScroll 1.3.0 -->
        <script src="<?php echo base_url(); ?>public/plugins/slimScroll/jquery.slimscroll.min.js"></script>
        <!-- Custom -->
        <script src="<?php echo base_url(); ?>public/js/custom-admin.js?v=<?php echo time(); ?>"></script>

        <?php
        if (isset($special_js) && !empty($special_js)) {
            foreach ($special_js as $js) {
                echo '<script type="text/javascript" src="' . base_url("public/" . $js) . '?v=' . time() . '"  ></script>';
            }
        }
        ?>
    </body>
</html>
