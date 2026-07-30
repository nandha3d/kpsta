<!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">Former Leaders</h1>
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
      <div class="grid-bearers" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 2.5rem;">
        <?php if(!empty($former_leaders)): ?>
          <?php foreach($former_leaders as $ob): ?>
          <div class="bearer-card">
            <div class="bearer-photo-wrapper">
               <?php if(!empty($ob['image'])) { ?>
                  <img src="<?php echo base_url('uploads/office_bearer/'.$ob['image']); ?>" alt="<?php echo htmlspecialchars($ob['name']); ?>">
               <?php } else { ?>
                  <div class="bearer-placeholder"><span class="material-symbols-outlined">person</span></div>
               <?php } ?>
            </div>
            <div class="bearer-name"><?php echo htmlspecialchars($ob['name']); ?></div>
            <div class="bearer-role" style="text-transform:none; font-weight:500; color:var(--color-text); line-height:1.4;">
              <?php echo htmlspecialchars($ob['designation']); ?> <?php if(!empty($ob['year'])) { echo '('.htmlspecialchars($ob['year']).')'; } ?>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No former leaders found.</p>
        <?php endif; ?>

      </div>
    </div>
  </main>
