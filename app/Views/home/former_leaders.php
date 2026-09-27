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

  <main class="page-bearers" style="padding: 3.5rem 0 5rem;">
    <div class="container">

      <!-- Interactive Filter & Search Bar -->
      <div class="former-leaders-toolbar">
        <div class="filter-pills">
          <button type="button" class="pill-btn active" data-filter="all">All Former Leaders</button>
          <button type="button" class="pill-btn" data-filter="State">State Level</button>
          <button type="button" class="pill-btn" data-filter="District">District Level</button>
        </div>
        <div class="search-box-wrapper">
          <span class="material-symbols-outlined">search</span>
          <input type="text" id="leaderSearchInput" placeholder="Search leader by name, role, year..." autocomplete="off">
        </div>
      </div>

      <div class="grid-bearers" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 2.5rem;">
        <?php if(!empty($former_leaders)): ?>
          <?php foreach($former_leaders as $ob): ?>
            <?php 
              // Collect positions
              $positions = !empty($ob['all_positions']) ? $ob['all_positions'] : [
                [
                  'designation' => $ob['designation'],
                  'year' => !empty($ob['year']) ? $ob['year'] : '',
                  'level' => !empty($ob['level']) ? $ob['level'] : 'State',
                  'section_heading' => !empty($ob['section_heading']) ? $ob['section_heading'] : ''
                ]
              ];

              $levels = [];
              $keywords = [$ob['name']];
              foreach ($positions as $p) {
                $lvl = !empty($p['level']) ? $p['level'] : 'State';
                $levels[] = $lvl;
                $keywords[] = $p['designation'];
                $keywords[] = $p['year'];
                $keywords[] = $lvl;
                if (!empty($p['section_heading'])) {
                  $keywords[] = $p['section_heading'];
                }
              }
              $levelStr = implode(' ', array_unique($levels));
              $keywordStr = mb_strtolower(implode(' ', array_filter($keywords)));
            ?>
          <div class="bearer-card former-leader-card" data-levels="<?php echo htmlspecialchars($levelStr); ?>" data-keywords="<?php echo htmlspecialchars($keywordStr); ?>" style="padding-bottom: 2rem;">
            <div class="bearer-photo-wrapper">
               <?php if(!empty($ob['image'])) { ?>
                  <img src="<?php echo base_url('uploads/office_bearer/'.$ob['image']); ?>" alt="<?php echo htmlspecialchars($ob['name']); ?>">
               <?php } else { ?>
                  <div class="bearer-placeholder"><span class="material-symbols-outlined">person</span></div>
               <?php } ?>
            </div>
            <div class="bearer-name"><?php echo htmlspecialchars($ob['name']); ?></div>
            
            <div class="former-leader-positions">
              <?php foreach($positions as $pos): ?>
                <div class="former-pos-item">
                  <span class="former-pos-desig"><?php echo htmlspecialchars($pos['designation']); ?></span>
                  <?php if(!empty($pos['year'])): ?>
                    <span class="former-pos-year">(<?php echo htmlspecialchars($pos['year']); ?>)</span>
                  <?php endif; ?>
                  <?php if(!empty($pos['level']) && $pos['level'] !== 'State'): ?>
                    <span class="former-pos-level"><?php echo htmlspecialchars($pos['level'] . (!empty($pos['section_heading']) ? ' - ' . $pos['section_heading'] : '')); ?></span>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Standard placeholder showcase when no DB records are active -->
          <div class="bearer-card former-leader-card" data-levels="State" data-keywords="former president president 2012-2013 general secretary 2014-2015" style="padding-bottom: 2rem;">
            <div class="bearer-photo-wrapper"><div class="bearer-placeholder"><span class="material-symbols-outlined">person</span></div></div>
            <div class="bearer-name">Former President</div>
            <div class="former-leader-positions">
              <div class="former-pos-item">
                <span class="former-pos-desig">President</span>
                <span class="former-pos-year">(2012-2013)</span>
              </div>
              <div class="former-pos-item">
                <span class="former-pos-desig">General Secretary</span>
                <span class="former-pos-year">(2014-2015)</span>
              </div>
            </div>
          </div>
          <div class="bearer-card former-leader-card" data-levels="State" data-keywords="former general secretary general secretary 2015-2016" style="padding-bottom: 2rem;">
            <div class="bearer-photo-wrapper"><div class="bearer-placeholder"><span class="material-symbols-outlined">person</span></div></div>
            <div class="bearer-name">Former General Secretary</div>
            <div class="former-leader-positions">
              <div class="former-pos-item">
                <span class="former-pos-desig">General Secretary</span>
                <span class="former-pos-year">(2015-2016)</span>
              </div>
            </div>
          </div>
          <div class="bearer-card former-leader-card" data-levels="State" data-keywords="former treasurer treasurer 2016-2017" style="padding-bottom: 2rem;">
            <div class="bearer-photo-wrapper"><div class="bearer-placeholder"><span class="material-symbols-outlined">person</span></div></div>
            <div class="bearer-name">Former Treasurer</div>
            <div class="former-leader-positions">
              <div class="former-pos-item">
                <span class="former-pos-desig">Treasurer</span>
                <span class="former-pos-year">(2016-2017)</span>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </div>

      <!-- No Results Message (Hidden by default) -->
      <div id="noResultsMessage" style="display: none; text-align: center; padding: 4rem 1rem;">
        <span class="material-symbols-outlined" style="font-size: 3rem; color: #94a3b8; margin-bottom: 0.5rem;">search_off</span>
        <h3 style="font-size: 1.25rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">No Former Leaders Found</h3>
        <p style="color: #64748b; font-size: 0.95rem;">Try adjusting your search terms or filter selection.</p>
      </div>

    </div>
  </main>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const pills = document.querySelectorAll('.former-leaders-toolbar .pill-btn');
    const searchInput = document.getElementById('leaderSearchInput');
    const cards = document.querySelectorAll('.grid-bearers .former-leader-card');
    const noResults = document.getElementById('noResultsMessage');

    let currentFilter = 'all';
    let searchQuery = '';

    function applyFilters() {
      let visibleCount = 0;
      cards.forEach(card => {
        const cardLevels = (card.getAttribute('data-levels') || '').split(' ');
        const keywords = (card.getAttribute('data-keywords') || '');

        const matchesFilter = (currentFilter === 'all') || cardLevels.includes(currentFilter);
        const matchesSearch = !searchQuery || keywords.includes(searchQuery);

        if (matchesFilter && matchesSearch) {
          card.style.display = 'flex';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      if (noResults) {
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
      }
    }

    pills.forEach(btn => {
      btn.addEventListener('click', function() {
        pills.forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.getAttribute('data-filter');
        applyFilters();
      });
    });

    if (searchInput) {
      searchInput.addEventListener('input', function() {
        searchQuery = this.value.trim().toLowerCase();
        applyFilters();
      });
    }
  });
  </script>
