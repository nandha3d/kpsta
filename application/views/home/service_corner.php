<!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">Service Corner Hub</h1>
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
        <?php if(!empty($services)): foreach($services as $service): 
          $card_link = (!empty($service['link']) && $service['link'] != 'Home/service_corner_details') ? $service['link'] : 'service_corner_details/' . $service['id'];
        ?>
          <a href="<?php echo base_url($card_link); ?>" class="service-hub-card" style="background: white; border-radius: 12px 0 12px 12px; clip-path: polygon(0 0, calc(100% - 24px) 0, 100% 24px, 100% 100%, 0 100%); text-decoration: none; display: flex; flex-direction: column; transition: transform 0.25s ease, filter 0.25s ease; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.08)); border: 1px solid var(--color-border); overflow: hidden;">
            <div style="background-color: var(--color-primary); color: white; padding: 1rem 1.25rem; display: flex; align-items: center;">
              <span class="material-symbols-outlined" style="font-size: 1.5rem; margin-right: 0.5rem;"><?php echo htmlspecialchars($service['icon'] ?? 'bookmark'); ?></span>
              <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: white; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo htmlspecialchars($service['service_number']); ?></h3>
            </div>
            <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem; flex-grow: 1; background: white;">
              <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-text-dark); margin: 0;"><?php echo htmlspecialchars($service['title']); ?></h4>
              <p style="color: var(--color-text-muted); font-size: 0.9rem; line-height: 1.5; margin: 0;"><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
              <div style="margin-top: auto; color: var(--color-primary); font-weight: 600; display:flex; align-items:center; gap:0.4rem; font-size: 0.9rem; transition: color 0.2s;">
                View Details <span class="material-symbols-outlined" style="font-size: 1.1rem; transition: transform 0.2s;">arrow_forward</span>
              </div>
            </div>
          </a>
        <?php endforeach; else: ?>
            <p>No services found.</p>
        <?php endif; ?></div>
    </div>
  </main>
