  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">Contact Us</h1>
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

  <main class="page-contact">
    <div class="container">
      
      <!-- Contact V2 Grid matching exact PDF layout -->
      <div class="contact-v2-grid">
        
        <!-- Left Side: Addresses and Recognition Info -->
        <div class="contact-left-info">
          
          <div class="contact-section-group">
            <h3>Kerala Pradesh School Teachers' Association</h3>
            <p class="contact-recog-text">
              Recognised as per GO.(MS) 269/63 dt. 29.04.1963 &amp;<br>
              Govt. Letter No. 556498/J3/2016 G.Edn. dt.10.06.2016
            </p>
            <div class="contact-icon-row">
              <div class="contact-circle-icon"><span class="material-symbols-outlined" >language</span></div>
              <div style="padding-top:0.5rem;"><a href="https://www.kpsta.in" target="_blank">www.kpsta.in</a></div>
            </div>
            <div class="contact-icon-row">
              <div class="contact-circle-icon"><span class="material-symbols-outlined" >mail</span></div>
              <div style="padding-top:0.5rem;"><a href="mailto:kpsta.in@gmail.com">kpsta.in@gmail.com</a></div>
            </div>
          </div>

          <div class="contact-section-group">
            <h3>State Committee Office</h3>
            <div class="contact-icon-row">
              <div class="contact-circle-icon"><span class="material-symbols-outlined" >location_on</span></div>
              <div style="padding-top:0.25rem;">KPSTA BHAVAN, Chinmaya School Lane,<br>Kunnumpuram, Trivandrum -1</div>
            </div>
            <div class="contact-icon-row">
              <div class="contact-circle-icon"><span class="material-symbols-outlined" >call</span></div>
              <div style="padding-top:0.5rem;">0471 - 2575797</div>
            </div>
          </div>

          <div class="contact-section-group">
            <h3>Office Annex</h3>
            <div class="contact-icon-row">
              <div class="contact-circle-icon"><span class="material-symbols-outlined" >location_on</span></div>
              <div style="padding-top:0.4rem;">KPSTA BHAVAN, Pulimoodu, Unni Lane, Tvm-1</div>
            </div>
            <div class="contact-icon-row">
              <div class="contact-circle-icon"><span class="material-symbols-outlined" >location_on</span></div>
              <div style="padding-top:0.25rem;">KPSTA BHAVAN, DHARMALAYAM ROAD,<br>OPP. Ayurveda College. Tvm-1</div>
            </div>
          </div>

          <div class="contact-section-group">
            <h3>Centre Office</h3>
            <div class="contact-icon-row">
              <div class="contact-circle-icon"><span class="material-symbols-outlined" >location_on</span></div>
              <div style="padding-top:0.25rem;">KPSTA Centre Office, Carrier Station Road,<br>Kochi -16</div>
            </div>
            <div class="contact-icon-row">
              <div class="contact-circle-icon"><span class="material-symbols-outlined" >call</span></div>
              <div style="padding-top:0.5rem;">0484 -2375817</div>
            </div>
          </div>

        </div>

        <!-- Right Side: Dark Teal Cards matching PDF -->
        <div class="contact-right-panel">
          
          <!-- Box 1: Office Bearers Card -->
          <div class="dark-teal-card">
            <div class="office-bearers-mini-grid">
              <?php if(!empty($officeBearer)) { foreach($officeBearer as $ob) { ?>
              <div class="mini-bearer-item">
                <h4><?php echo $ob['name']; ?></h4>
                <p><?php echo $ob['designation']; ?></p>
                <a href="tel:<?php echo $ob['phone']; ?>" class="phone-badge-orange">
                  <span class="phone-circle-icon"><span class="material-symbols-outlined" >call</span></span>
                  <span><?php echo $ob['phone']; ?></span>
                </a>
              </div>
              <?php } } ?>
            </div>

            <a href="<?php echo base_url('OfficeBearer'); ?>" class="btn-more-bearers">More Office Bearers</a>
          </div>

          <h2 class="editorial-board-title">Editorial Board Members</h2>

          <!-- Box 2: Editorial Board Card -->
          <div class="editorial-card">
            <div class="editorial-list">
              <?php if (!empty($editorialMembers)) { ?>
                <?php foreach ($editorialMembers as $em) { ?>
                  <div class="editorial-row">
                    <div class="editorial-left">
                      <div class="quill-circle"><span class="material-symbols-outlined">edit</span></div>
                      <span><?php echo htmlspecialchars($em['designation']); ?></span>
                    </div>
                    <div class="editorial-line"></div>
                    <span class="editorial-name"><?php echo htmlspecialchars($em['name']); ?></span>
                  </div>
                <?php } ?>
              <?php } else { ?>
                <div class="editorial-row">
                  <div class="editorial-left">
                    <div class="quill-circle"><span class="material-symbols-outlined">edit</span></div>
                    <span>Editor-in-Chief</span>
                  </div>
                  <div class="editorial-line"></div>
                  <span class="editorial-name">Abdul Majeed K</span>
                </div>
                <div class="editorial-row">
                  <div class="editorial-left">
                    <div class="quill-circle"><span class="material-symbols-outlined">edit</span></div>
                    <span>Associate Editor</span>
                  </div>
                  <div class="editorial-line"></div>
                  <span class="editorial-name">Abdul Majeed K</span>
                </div>
                <div class="editorial-row">
                  <div class="editorial-left">
                    <div class="quill-circle"><span class="material-symbols-outlined">edit</span></div>
                    <span>Technical Editor</span>
                  </div>
                  <div class="editorial-line"></div>
                  <span class="editorial-name">Abdul Majeed K</span>
                </div>
                <div class="editorial-row">
                  <div class="editorial-left">
                    <div class="quill-circle"><span class="material-symbols-outlined">edit</span></div>
                    <span>Editorial Advisor</span>
                  </div>
                  <div class="editorial-line"></div>
                  <span class="editorial-name">Abdul Majeed K</span>
                </div>
                <div class="editorial-row">
                  <div class="editorial-left">
                    <div class="quill-circle"><span class="material-symbols-outlined">edit</span></div>
                    <span>Editorial Member</span>
                  </div>
                  <div class="editorial-line"></div>
                  <span class="editorial-name">Abdul Majeed K</span>
                </div>
              <?php } ?>
            </div>
          </div>

        </div>

      </div>

    </div>
  </main>
