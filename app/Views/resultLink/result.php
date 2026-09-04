<main class="page-legacy">
<div class="subpages-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Results</h3>
            </div>

            <div class="col-sm-6 hidden-xs hidden-sm ">
                <ol class="breadcrumb pull-right">
                    <li class="breadcrumb-item"> <a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">Result</li>
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
                            ?>
                            <li class="view">
                                <h6><?php echo $single['description'] ?></h6>
                                <p><a href="<?php echo $single['path'] ?>" target="_blank"  class="download-links">Downloads Files</a></p>
                            </li>

                            <?php
                        } catch (\Exception $exc) {
                            
                        }
                    }
                    ?>
                </ul>

            </div>
        </div>

    </div>
</div>
</main>
