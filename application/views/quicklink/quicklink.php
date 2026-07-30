  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">Online Links</h1>
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

  <main style="padding: 4rem 0 7rem;">
    <div class="container">
      <div class="quick-links-grid">
        
        <?php if(!empty($quicklink)) { foreach($quicklink as $link) { 
          $url = !empty($link['path']) ? $link['path'] : (!empty($link['url']) ? $link['url'] : '#');
          $title = !empty($link['description']) ? $link['description'] : (!empty($link['title']) ? $link['title'] : '');
        ?>
        <a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="quick-link-btn">
          <span><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></span>
          <span class="material-symbols-outlined" style="font-size:1.3rem;">open_in_new</span>
        </a>
        <?php } } else { ?>
          <p>No quick links available at the moment.</p>
        <?php } ?>

      </div>

      <!-- Pagination -->
      <?php if(!empty($links)) { ?>
      <div class="pagination" style="margin-top: 3rem;">
        <?php echo $links; ?>
      </div>
      <?php } ?>

    </div>
  </main>
