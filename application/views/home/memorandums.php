<!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">Memorandums</h1>
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
<main class="page-download">
    <div class="container">
      <div class="download-list">
        
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>
        <div class="download-row has-icon">
          <div class="download-info"><span class="pdf-icon-badge">PDF</span><span class="download-title">Lorem ipsum dolor sit amet consectetur adipiscing elit.</span></div>
          <a href="#" class="btn-circle-download"><span class="material-symbols-outlined">download</span></a>
        </div>

      </div>
    </div>
  </main>
