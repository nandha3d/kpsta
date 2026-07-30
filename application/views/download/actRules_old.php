<div class="subpages-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3><?php echo $contentTitle ?></h3>
            </div>

            <div class="col-sm-6 hidden-xs hidden-sm ">
                <ol class="breadcrumb pull-right">
                    <li class="breadcrumb-item"> <a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"><?php echo $contentTitle ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>



<div class="container">
    <div class="row">
        <div class="col-sm-8">
            <div class="psd-listing">
                <ul>



                    <?php
                    foreach ($data as $key => $single) {
                        try {


                            if ($single['upload_type'] == 'file') {
                                $url = base_url() . DOWNLOAD_PATH . $single['path'];
                            } else if ($single['upload_type'] == 'url') {
                                $url = $single['path'];
                            } else {
                                $url = "#";
                            }
                            ?>
                            <li class="download">
                                <h6><?php echo $single['description'] ?></h6>
                                <p><a href="<?php echo $url ?>" target="_blank"  class="download-links">Downloads Files</a></p>
                            </li>

                            <?php
                        } catch (\Exception $exc) {
                            
                        }
                    }
                    ?>
                </ul>

            </div>
        </div>
        <div class="col-sm-4">
                        <div class="right-side-bar">
                            <h4>Latest Orders</h4>
                            <div id="homeQuick">
                                <ul id="newsEvents">
                                    <li><span class="homeDate"><span class="hMonth">Jul</span><span class="hDate">04</span></span><a href="/newsevents">Correction pages - Malayalam 2016</a></li>
                                    <li><span class="homeDate"><span class="hMonth">Jul</span><span class="hDate">05</span></span><a href="/newsevents">Correction pages - Malayalam 2016</a></li>
                                    <li><span class="homeDate"><span class="hMonth">Jul</span><span class="hDate">04</span></span><a href="/newsevents">Correction pages - Malayalam 2016</a></li>
                                    <li><span class="homeDate"><span class="hMonth">Jul</span><span class="hDate">05</span></span><a href="/newsevents">Correction pages - Malayalam 2016</a></li>
                                    <li><span class="homeDate"><span class="hMonth">Jul</span><span class="hDate">04</span></span><a href="/newsevents">Correction pages - Malayalam 2016</a></li>
                                    <li><span class="homeDate"><span class="hMonth">Jul</span><span class="hDate">05</span></span><a href="/newsevents">Correction pages - Malayalam 2016</a></li>
                                    <li><span class="homeDate"><span class="hMonth">Jul</span><span class="hDate">04</span></span><a href="/newsevents">Correction pages - Malayalam 2016</a></li>
                                    <li><span class="homeDate"><span class="hMonth">Jul</span><span class="hDate">05</span></span><a href="/newsevents">Correction pages - Malayalam 2016</a></li>
                                </ul>
                            </div>
                        </div>
        </div>
    </div>
</div>