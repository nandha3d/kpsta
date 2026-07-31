  <!-- Hero Section -->
  <section class="hero-banner home-hero">
    <!-- Background Slider Elements -->
    <div class="hero-slider-bg" id="heroBgSlider">
      <?php if(!empty($sliderImages)) { foreach($sliderImages as $key => $image) { ?>
        <div class="hero-slide <?php echo $key === 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo base_url('uploads/slider/' . $image['image']); ?>');"></div>
      <?php } } else { ?>
        <div class="hero-slide active" style="background-color: #276269;"></div>
      <?php } ?>
      <div class="hero-overlay"></div>
    </div>
    
    <div class="container hero-content">
      <div class="hero-flag">
        <picture>
          <source srcset="<?php echo base_url('public/Page References/giphy_cropped.webp'); ?>" type="image/webp">
          <img src="<?php echo base_url('public/Page References/giphy_cropped.gif'); ?>" alt="KPSTA Flag" style="height: 85px; width: auto; margin: 0 auto; filter: drop-shadow(0 8px 14px rgba(0,0,0,0.4));">
        </picture>
      </div>
      <h1 class="hero-title">KPSTA</h1>
      <p class="hero-subtitle">Kerala Pradesh School Teacher's Association</p>
      <div class="hero-tagline">UNITE FOR QUALITY EDUCATION</div>
      <p class="hero-subtagline">Better education for a better world</p>
    </div>
  </section>

  <!-- Hero Background Slider Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const slides = document.querySelectorAll('#heroBgSlider .hero-slide');
      if (slides.length > 1) {
        let currentSlide = 0;
        setInterval(() => {
          slides[currentSlide].classList.remove('active');
          currentSlide = (currentSlide + 1) % slides.length;
          slides[currentSlide].classList.add('active');
        }, 5000); // Change slide every 5 seconds
      }
    });
  </script>

  <!-- Marquee Ticker -->
  <div class="news-ticker-bar">
    <div class="ticker-badge">KPSTA NEWS</div>
    <marquee class="ticker-content" onmouseover="this.stop();" onmouseout="this.start();">
      <?php if(!empty($news)) { foreach($news as $n) { ?>
        <?php $link = !empty($n['url']) ? $n['url'] : (!empty($n['path']) ? $n['path'] : ''); ?>
        <?php if(!empty($link)): ?>
            <a href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>" style="color: inherit; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><?php echo $n['description']; ?></a>
        <?php else: ?>
            <?php echo $n['description']; ?>
        <?php endif; ?>
         &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; 
      <?php } } else { ?>
        Welcome to KPSTA
      <?php } ?>
    </marquee>
  </div>

  <!-- Welcome Section -->
  <section class="welcome-section">
    <div class="container">
      <div class="welcome-card">
        <h2>Welcome To KPSTA</h2>
        <p>KPSTA - Kerala Pradesh School Teachers Association is the largest and most prestigious organization of school teachers in Kerala. Various organizations representing school teachers at various levels in the state and formed over a long period of time since 1931 came under a single umbrella called 'Kerala Pradesh School Teachers Association'. These were formed with the aim of providing better services to the school teachers of the kerala state.</p>
        <a href="<?php echo base_url('office_bearer'); ?>" class="btn-outline">Read More</a>
      </div>
    </div>
  </section>

  <!-- Office Bearers Section -->
  <section style="padding: 2rem 0 5rem;">
    <div class="container">
      <h2 class="section-title">Office Bearers</h2>
      
      <div class="home-office-bearers-layout">
        <div>
          <div class="home-office-bearers-grid">
            <?php if(!empty($officeBearer)) { foreach($officeBearer as $ob) { ?>
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
            </div>
            <?php } } ?>
          </div>
          <div style="text-align: center;">
            <a href="<?php echo base_url('office_bearer'); ?>" class="btn-green">More Office Bearers</a>
          </div>
        </div>

        <div class="action-box-grid">
          <a href="<?php echo base_url('membership'); ?>" class="action-card-link">
            <div class="action-icon"><span class="material-symbols-outlined">badge</span></div>
            <div class="action-title">Membership & Magazine</div>
          </a>
          <a href="<?php echo base_url('order-circular'); ?>" class="action-card-link">
            <div class="action-icon"><span class="material-symbols-outlined">description</span></div>
            <div class="action-title">Order & Circular</div>
          </a>
          <a href="<?php echo base_url('download/academic_corner'); ?>" class="action-card-link">
            <div class="action-icon"><span class="material-symbols-outlined">school</span></div>
            <div class="action-title">Academic Corner</div>
          </a>
          <a href="<?php echo base_url('Home/service_corner'); ?>" class="action-card-link">
            <div class="action-icon"><span class="material-symbols-outlined">support_agent</span></div>
            <div class="action-title">Service Corner</div>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Split News Window & Reaction Gallery -->
  <div class="split-section">
    <div class="news-window-side">
      <div class="news-header">
        <h2>News Window</h2>
        <a href="<?php echo base_url('news'); ?>" class="btn-view-all">View All</a>
      </div>
      <div class="news-list">
        <?php if(!empty($listNews)) { foreach($listNews as $newsItem) { ?>
        <div class="news-item-card">
          <div class="news-thumb">
             <?php if(!empty($newsItem['image'])) { ?>
                <img src="<?php echo base_url('uploads/news/'.$newsItem['image']); ?>" alt="News thumbnail">
             <?php } else { ?>
                <img src="<?php echo base_url('public/images/home_01.jpg'); ?>" alt="News thumbnail">
             <?php } ?>
          </div>
          <div class="news-info">
            <div class="news-date"><?php echo isset($newsItem['created_at']) ? date('M d, Y', strtotime($newsItem['created_at'])) : ''; ?></div>
            <div class="news-title"><?php echo isset($newsItem['heading']) ? $newsItem['heading'] : ''; ?></div>
            <!-- If news details exists, use its route, else point to news page -->
            <a href="<?php echo base_url('news'); ?>" class="news-readmore">Read More</a>
          </div>
        </div>
        <?php } } ?>
      </div>
    </div>

    <div class="reaction-gallery-side">
      <h2>Reaction Gallery</h2>
      <?php if(!empty($reactionGalleryImages)) { ?>
        <div class="reaction-slider-container" style="position: relative; max-width: 360px; margin: 0 auto;">
          <?php foreach($reactionGalleryImages as $idx => $rg) { ?>
            <div class="poster-card reaction-slide <?php echo empty($rg['description']) ? 'no-title' : ''; ?>" style="<?php echo $idx === 0 ? 'display: block;' : 'display: none;'; ?>">
              <?php if(!empty($rg['image'])) { ?>
                <a href="<?php echo base_url('uploads/reaction_gallery/'.$rg['image']); ?>" target="_blank" style="display: block; width: 100%;">
                  <img src="<?php echo base_url('uploads/reaction_gallery/'.$rg['image']); ?>" alt="<?php echo htmlspecialchars($rg['description']); ?>" style="width:100%; object-fit: cover; max-height: 450px;">
                </a>
                <?php if(!empty($rg['description'])) { ?>
                  <div class="poster-card-title">
                    <img src="<?php echo base_url('public/Page References/logo.png'); ?>" alt="KPSTA" class="poster-logo">
                    <span><?php echo htmlspecialchars($rg['description']); ?></span>
                  </div>
                <?php } ?>
              <?php } else { ?>
                <div style="background: linear-gradient(135deg, #d97706, #ea580c); color:white; padding:1.5rem; border-radius:var(--radius-lg); font-weight:800; font-size:1.3rem; text-align:center;">
                  <?php echo htmlspecialchars($rg['description']); ?>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
          <?php if(count($reactionGalleryImages) > 1) { ?>
            <div class="reaction-dots" style="margin-top: 1.25rem; display: flex; justify-content: center; gap: 0.5rem;">
              <?php foreach($reactionGalleryImages as $idx => $rg) { ?>
                <span class="rg-dot <?php echo $idx === 0 ? 'active' : ''; ?>" onclick="showReactionSlide(<?php echo $idx; ?>)" style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo $idx === 0 ? '#f97316' : 'rgba(255,255,255,0.4)'; ?>; cursor: pointer; display: inline-block; transition: background 0.3s;"></span>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
        <script>
          let currentRgSlide = 0;
          const rgSlides = document.querySelectorAll('.reaction-slide');
          const rgDots = document.querySelectorAll('.rg-dot');
          function showReactionSlide(index) {
            rgSlides.forEach((slide, i) => {
              slide.style.display = (i === index) ? 'block' : 'none';
            });
            rgDots.forEach((dot, i) => {
              dot.style.background = (i === index) ? '#f97316' : 'rgba(255,255,255,0.4)';
            });
            currentRgSlide = index;
          }
          if (rgSlides.length > 1) {
            setInterval(() => {
              currentRgSlide = (currentRgSlide + 1) % rgSlides.length;
              showReactionSlide(currentRgSlide);
            }, 4000);
          }
        </script>
      <?php } else { ?>
        <p>No recent reactions.</p>
      <?php } ?>
    </div>
  </div>

  <!-- Gallery Preview Section -->
  <section style="padding: 5rem 0;">
    <div class="container">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2.5rem;">
        <h2 class="section-title" style="margin-bottom:0;">Gallery</h2>
        <div style="display:flex; gap:0.5rem;">
          <a href="<?php echo base_url('gallery'); ?>" class="btn-green" style="padding:0.6rem 1.25rem; border-radius:50px;">←</a>
          <a href="<?php echo base_url('gallery'); ?>" class="btn-green" style="padding:0.6rem 1.25rem; border-radius:50px;">→</a>
        </div>
      </div>

      <div class="home-gallery-grid">
        <?php if(!empty($images)) { foreach(array_slice($images, 0, 5) as $img) { ?>
        <a href="<?php echo base_url('Gallery/singleAlbum/'.$img['guId']); ?>" class="gallery-card" style="text-decoration: none; display: block; color: inherit; transition: transform 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden; background: #fff;">
          <div class="gallery-img-wrapper" style="overflow: hidden; border-radius: 12px;">
             <img src="<?php echo base_url('uploads/gallery/original/'.$img['image']); ?>" alt="Gallery Image" style="width: 100%; height: 200px; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
          </div>
          <div class="gallery-caption" style="padding: 10px; text-align: center; font-weight: bold; font-size: 1.1rem;"><?php echo !empty($img['title']) ? $img['title'] : $img['name']; ?></div>
        </a>
        <?php } } ?>
      </div>
    </div>
  </section>

