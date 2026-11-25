  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">LATEST NEWS</h1>
    </div>
  </section>


  <main style="padding: 3.5rem 0 5rem;">
    <div class="container">
        
        <?php if (!empty($listNews)) { ?>
          <div class="news-grid">
          <?php foreach ($listNews as $news) { ?>
            <div class="news-card">
              <?php if (!empty($news['image'])) { ?>
                <div class="news-image">
                  <img src="<?php echo base_url('uploads/news/' . $news['image']); ?>" alt="News Image">
                </div>
              <?php } else { ?>
                <div class="news-image-placeholder">
                  <span class="material-symbols-outlined">newspaper</span>
                </div>
              <?php } ?>
              
              <div class="news-content">
                <?php 
                  $newsDate = display_date($news['created_at'], 'F d, Y'); 
                  $cleanHeading = trim(strip_tags($news['heading']));
                  $cleanSnippet = trim(preg_replace('/\s+/', ' ', strip_tags($news['content'])));
                  if (mb_strlen($cleanSnippet) > 160) {
                      $cleanSnippet = mb_substr($cleanSnippet, 0, 157) . '...';
                  }
                  $newsShareText = "*KPSTA News*\n*" . $cleanHeading . "*\n\n" . $cleanSnippet . "\n\n" . base_url('news');
                  $waNewsUrl = 'https://api.whatsapp.com/send?text=' . rawurlencode($newsShareText);
                ?>
                <div class="news-card-header">
                  <?php if ($newsDate !== '') { ?>
                  <div class="news-date">
                    <span class="material-symbols-outlined" style="font-size: 16px; margin-right: 5px; vertical-align: text-bottom;">calendar_month</span>
                    <?php echo $newsDate; ?>
                  </div>
                  <?php } else { ?>
                  <div></div>
                  <?php } ?>
                  
                  <a href="<?php echo $waNewsUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-circle" title="Share on WhatsApp" aria-label="Share on WhatsApp">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="white" aria-hidden="true">
                      <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.15c-1.38 0-2.74-.35-3.94-1.06l-.29-.17-3.12.82.83-3.04-.19-.31a8.19 8.19 0 0 1-1.25-4.48c0-4.54 3.69-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.82c0 4.54-3.7 8.24-8.24 8.24zm4.51-6.17c-.25-.12-1.47-.72-1.7-.8-.23-.08-.4-.12-.56.12-.17.25-.64.8-.79.97-.14.16-.29.19-.53.07-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.22-1.46-1.36-1.7-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.15.17-.25.25-.42.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.83-.2-.48-.4-.42-.56-.43h-.48c-.16 0-.43.06-.65.3-.23.25-.87.85-.87 2.07s.89 2.4 1.01 2.56c.13.17 1.74 2.66 4.22 3.73.59.25 1.05.4 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.11-.23-.17-.48-.29z"/>
                    </svg>
                  </a>
                </div>
                
                <h3 class="news-heading"><?php echo $news['heading']; ?></h3>
                
                <div class="news-text">
                  <?php echo $news['content']; ?>
                </div>

                <div class="news-card-footer">
                  <a href="<?php echo $waNewsUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-pill" title="Share on WhatsApp" aria-label="Share on WhatsApp">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="white" aria-hidden="true">
                      <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.15c-1.38 0-2.74-.35-3.94-1.06l-.29-.17-3.12.82.83-3.04-.19-.31a8.19 8.19 0 0 1-1.25-4.48c0-4.54 3.69-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.82c0 4.54-3.7 8.24-8.24 8.24zm4.51-6.17c-.25-.12-1.47-.72-1.7-.8-.23-.08-.4-.12-.56.12-.17.25-.64.8-.79.97-.14.16-.29.19-.53.07-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.22-1.46-1.36-1.7-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.15.17-.25.25-.42.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.83-.2-.48-.4-.42-.56-.43h-.48c-.16 0-.43.06-.65.3-.23.25-.87.85-.87 2.07s.89 2.4 1.01 2.56c.13.17 1.74 2.66 4.22 3.73.59.25 1.05.4 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.11-.23-.17-.48-.29z"/>
                    </svg>
                    <span>Share on WhatsApp</span>
                  </a>
                </div>
              </div>
            </div>
          <?php } ?>
          </div>
        <?php } else { ?>
          <div class="text-center" style="padding: 3rem; background: #f8fafc; border-radius: 8px;">
            <span class="material-symbols-outlined" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px;">article</span>
            <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No news available at the moment.</p>
          </div>
        <?php } ?>

      <?php if (!empty($links)) { ?>
      <div class="pagination">
          <?php echo $links ?>
      </div>
      <?php } ?>
    </div>
  </main>

<style>
.news-grid {
    display: grid;
    /* min() keeps the column from staying wider than a small phone screen */
    grid-template-columns: repeat(auto-fill, minmax(min(340px, 100%), 1fr));
    gap: 2.5rem;
}

.news-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #f1f5f9;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
}

.news-image {
    height: 220px;
    width: 100%;
    overflow: hidden;
}

.news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.news-card:hover .news-image img {
    transform: scale(1.05);
}

.news-image-placeholder {
    height: 220px;
    width: 100%;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.news-image-placeholder .material-symbols-outlined {
    font-size: 64px;
    color: #94a3b8;
    opacity: 0.5;
}

.news-content {
    padding: 1.75rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.news-date {
    font-size: 0.875rem;
    color: #3b82f6;
    font-weight: 500;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
}

.news-heading {
    font-size: 1.35rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 0;
    margin-bottom: 1rem;
    line-height: 1.4;
    overflow-wrap: break-word;
    word-break: break-word;
}

.news-text {
    color: #475569;
    font-size: 1rem;
    line-height: 1.7;
    margin-bottom: 0;
    /* Basic styling for rich text from summernote — pasted content can carry
       long unbroken strings, so wrap them instead of widening the card */
    overflow-wrap: break-word;
    word-break: break-word;
}

.news-text p {
    margin-bottom: 0.75rem;
}

.news-text p:last-child {
    margin-bottom: 0;
}

.news-text img {
    max-width: 100%;
    height: auto;
    border-radius: 6px;
    margin: 10px 0;
}
</style>