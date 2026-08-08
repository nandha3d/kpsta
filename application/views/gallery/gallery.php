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

  <main class="page-gallery">
    <div class="container">

      <?php
        $galleryYears = array();
        if(!empty($albums)) {
          foreach($albums as $album) { $galleryYears[] = date('Y', strtotime($album['created_at'])); }
        }
        $galleryYears = array_values(array_unique($galleryYears));
        rsort($galleryYears);
        if(empty($galleryYears)) { $galleryYears = array(date('Y')); }
        $activeYear = $galleryYears[0];
      ?>

      <div class="gallery-head">
        <h2 class="gallery-title">Photos &#8211; <span id="galleryYearLabel"><?php echo $activeYear; ?></span></h2>
        <div class="year-stepper">
          <button type="button" class="year-nav" data-dir="-1" aria-label="Previous year"></button>
          <select class="year-select" id="galleryYearSelect">
            <?php foreach($galleryYears as $gy) { ?>
              <option value="<?php echo $gy; ?>"><?php echo $gy; ?></option>
            <?php } ?>
          </select>
          <button type="button" class="year-nav next" data-dir="1" aria-label="Next year"></button>
        </div>
      </div>

      <div class="gallery-grid" id="galleryGrid">

        <?php if(!empty($albums)) { foreach($albums as $album) { ?>
        <a href="<?php echo base_url('Gallery/singleAlbum/'.$album['guId']); ?>" class="gallery-item" data-year="<?php echo date('Y', strtotime($album['created_at'])); ?>">
          <div class="gallery-thumb">
            <?php if(!empty($album['coverImage'])) { ?>
              <img src="<?php echo base_url(GALLERY_THUMB . '/' . $album['coverImage']); ?>" alt="<?php echo htmlspecialchars($album['name']); ?>">
            <?php } else { ?>
              <span class="material-symbols-outlined gallery-thumb-fallback">photo_camera</span>
            <?php } ?>
          </div>
          <h3 class="gallery-caption"><?php echo $album['name']; ?></h3>
        </a>
        <?php } } else { ?>
          <p>No albums available.</p>
        <?php } ?>

      </div>

    </div>
  </main>

  <script>
    (function () {
      var select = document.getElementById('galleryYearSelect');
      var label = document.getElementById('galleryYearLabel');
      var grid = document.getElementById('galleryGrid');
      if (!select || !grid) return;
      function apply() {
        var year = select.value;
        if (label) label.textContent = year;
        grid.querySelectorAll('.gallery-item').forEach(function (item) {
          item.style.display = (item.getAttribute('data-year') === year) ? '' : 'none';
        });
      }
      select.addEventListener('change', apply);
      document.querySelectorAll('.year-nav').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var dir = parseInt(btn.getAttribute('data-dir'), 10);
          var next = select.selectedIndex + dir;
          if (next >= 0 && next < select.options.length) {
            select.selectedIndex = next;
            apply();
          }
        });
      });
      apply();
    })();
  </script>
