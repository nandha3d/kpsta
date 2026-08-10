<main class="page-legacy">
<div class="subpages-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h3><a href="<?php echo base_url('gallery'); ?>">PHOTO ALBUMS </a><span> / <?php echo $albumData['name'].'<small>( '.$albumData['iCount'].' image\'s )</small>'  ?> </span></h3>
            </div>
        </div>
    </div>
</div>



<style>
.gallery-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 0;
    margin: 0;
    list-style: none;
}
.gallery-grid li {
    width: calc(25% - 15px);
}
@media (max-width: 991px) {
    .gallery-grid li { width: calc(33.333% - 13.33px); }
}
@media (max-width: 767px) {
    .gallery-grid li { width: calc(50% - 10px); }
}
@media (max-width: 479px) {
    .gallery-grid li { width: 100%; }
}
.gallery-thumbnail {
    display: block;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: #fff;
    padding: 5px;
    border: 1px solid #e2e8f0;
}
.gallery-thumbnail:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}
.gallery-thumbnail img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 4px;
    display: block;
}
</style>

<div class="container" style="margin-top: 40px; margin-bottom: 60px;">
    <div class="row">
        <div class="col-sm-12">
            <ul class="gallery-grid">
                <?php foreach ($images as $key => $image) { ?>
                    <li>
                        <a class="gallery-thumbnail" data-fslightbox="gallery" href="<?php echo base_url(GALLERY_ORIGINAL . '/' . $image['image']); ?>">
                            <img src="<?php echo base_url(GALLERY_THUMB . '/' . $image['image']); ?>" alt="Gallery Image">
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fslightbox/3.4.1/index.min.js"></script>
</main>
