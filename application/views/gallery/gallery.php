  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">Gallery</h1>
    </div>
  </section>

  <!-- Marquee Ticker -->
  <div class="news-ticker-bar">
    <div class="ticker-badge">KPSTA NEWS</div>
    <marquee class="ticker-content" onmouseover="this.stop();" onmouseout="this.start();">
      <?php 
      $CI =& get_instance();
      $CI->load->model('FlashNews_model');
      $dynamic_news = $CI->FlashNews_model->getAll(array('isPublish' => TRUE, 'limit' => 25));
      if(!empty($dynamic_news)) { foreach($dynamic_news as $n) { 
          $link = !empty($n['url']) ? $n['url'] : (!empty($n['path']) ? $n['path'] : '');
          if(!empty($link)): ?>
            <a href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>" style="color: inherit; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><?php echo $n['description']; ?></a>
        <?php else: ?>
            <span><?php echo $n['description']; ?></span>
        <?php endif; ?>
        &nbsp; &nbsp; | &nbsp; &nbsp; 
      <?php } } else { ?>
        KPSTA സംസ്ഥാന ഭാരവാഹികളുടെ സത്യപ്രതിജ്ഞ @ എറണാകുളം 22.02.2026
      <?php } ?>
    </marquee>
  </div>

  <main style="padding: 4rem 0 6rem;">
    <div class="container">
      
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem;">
        
        <?php if(!empty($albums)) { foreach($albums as $album) { ?>
        <!-- Gallery Item -->
        <a href="<?php echo base_url('Gallery/singleAlbum/'.$album['guId']); ?>" style="text-decoration:none;">
          <div style="border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm); background: white; transition: transform 0.3s ease, box-shadow 0.3s ease;" class="gallery-card">
            <div style="height: 240px; background: linear-gradient(135deg, #0b2545, #134074); display:flex; align-items:center; justify-content:center; color:white; font-size:3rem; position:relative;">
              
              <?php if(!empty($album['coverImage'])) { ?>
                <img src="<?php echo base_url(GALLERY_THUMB . '/' . $album['coverImage']); ?>" style="width:100%; height:100%; object-fit:cover;">
              <?php } else { ?>
                <span class="material-symbols-outlined" style="font-size:4rem; opacity:0.7;">photo_camera</span>
              <?php } ?>

              <div style="position:absolute; inset:0; background:rgba(0,0,0,0.3); opacity:0; transition:opacity 0.3s ease; display:flex; align-items:center; justify-content:center;" class="gallery-overlay">
                <span class="material-symbols-outlined" style="font-size:2.5rem;">zoom_in</span>
              </div>
            </div>
            <div style="padding: 1.25rem;">
              <h3 style="font-size: 1.15rem; font-weight: 600; color: var(--color-primary); margin-bottom: 0.25rem;"><?php echo $album['name']; ?></h3>
              <p style="font-size: 0.85rem; color: #64748b;"><?php echo date('F d, Y', strtotime($album['created_at'])); ?></p>
            </div>
          </div>
        </a>
        <?php } } else { ?>
          <p>No albums available.</p>
        <?php } ?>

      </div>

    </div>
  </main>

  <style>
    .gallery-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
    }
    .gallery-card:hover .gallery-overlay {
      opacity: 1 !important;
    }
  </style>
