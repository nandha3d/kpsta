  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">Office Bearers</h1>
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

  <main style="padding: 3rem 0 5rem;">
    <div class="container">
      <div style="display:flex; justify-content:flex-end; margin-bottom:1.5rem;">
        <a href="<?php echo base_url('Home/former_leaders'); ?>" class="btn-blue">Former leaders →</a>
      </div>

      <?php if(!empty($content)) { foreach($content as $designation => $bearers) { ?>
      <!-- Ribbon -->
      <div class="ribbon-section-header">
        <div class="ribbon-badge"><?php echo $designation; ?></div>
        <div class="ribbon-line"></div>
      </div>

      <div class="grid-bearers">
        <?php foreach($bearers as $ob) { ?>
        <div class="bearer-card">
          <div class="bearer-photo-wrapper">
             <?php if(!empty($ob['image'])) { ?>
                <img src="<?php echo base_url('uploads/office_bearer/'.$ob['image']); ?>" alt="<?php echo $ob['name']; ?>">
             <?php } else { ?>
                <div class="bearer-placeholder"><span class="material-symbols-outlined">person</span></div>
             <?php } ?>
          </div>
          <div class="bearer-name"><?php echo $ob['name']; ?></div>
          <div class="bearer-role"><?php echo $ob['designation']; ?> <?php if(!empty($ob['year'])) { echo '('.$ob['year'].')'; } ?></div>
          <?php if(!empty($ob['phone'])) { ?>
          <a href="tel:<?php echo $ob['phone']; ?>" class="bearer-phone"><span class="material-symbols-outlined" style="font-size:1rem;">call</span> <?php echo $ob['phone']; ?></a>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <?php } } else { ?>
        <p>No office bearers available.</p>
      <?php } ?>

    </div>
  </main>
