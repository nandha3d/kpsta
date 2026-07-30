  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title"><?php echo !empty($service['title']) ? htmlspecialchars($service['title']) : 'Service Items & Details'; ?></h1>
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
    <div class="container" style="max-width: 900px;">
      
      <div style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <a href="<?php echo base_url('service_corner'); ?>" style="display:inline-flex; align-items:center; gap:0.5rem; color:var(--color-primary); font-weight:600; text-decoration:none;">
          <span class="material-symbols-outlined">arrow_back</span> Back to Service Corner Hub
        </a>
        <span style="color:#64748b; font-weight:600; font-size:0.95rem;">Service Corner / <?php echo htmlspecialchars($service['title'] ?? 'Items'); ?></span>
      </div>

      <div class="accordion">
        
        <?php if(!empty($rules)): foreach($rules as $idx => $rule): ?>
        <div class="accordion-item <?php echo ($idx == 0) ? 'open' : ''; ?>">
          <button class="accordion-header">
            <span class="accordion-header-title">
              <?php if(!empty($rule['rule_number'])): ?>
                <?php echo htmlspecialchars($rule['rule_number']); ?>. 
              <?php else: ?>
                <?php echo ($idx + 1); ?>. 
              <?php endif; ?>
              <?php echo htmlspecialchars($rule['title']); ?>
            </span>
            <span class="accordion-toggle-btn"><?php echo ($idx == 0) ? '-' : '+'; ?></span>
          </button>
          <div class="accordion-content">
            <div style="color:#475569; line-height:1.7; white-space: pre-line; margin-bottom:1rem;">
              <?php echo nl2br(htmlspecialchars($rule['content'])); ?>
            </div>
            <?php if(!empty($rule['form_link'])): ?>
            <a href="<?php echo (strpos($rule['form_link'], 'http') === 0) ? htmlspecialchars($rule['form_link']) : base_url($rule['form_link']); ?>" target="_blank" style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.5rem 1rem; background:#eff6ff; color:var(--color-primary); border-radius:6px; font-weight:600; font-size:0.9rem; text-decoration:none; margin-top:0.5rem;">
              <span class="material-symbols-outlined" style="font-size:1.1rem;">download</span> Download Related Form / Application
            </a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; else: ?>
          <div class="alert alert-info">No specific rules or guidelines have been added for this service yet.</div>
        <?php endif; ?>

      </div>

    </div>
  </main>
