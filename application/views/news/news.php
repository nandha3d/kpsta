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
                <?php $newsDate = display_date($news['created_at'], 'F d, Y'); ?>
                <?php if ($newsDate !== '') { ?>
                <div class="news-date">
                  <span class="material-symbols-outlined" style="font-size: 16px; margin-right: 5px; vertical-align: text-bottom;">calendar_month</span>
                  <?php echo $newsDate; ?>
                </div>
                <?php } ?>
                
                <h3 class="news-heading"><?php echo $news['heading']; ?></h3>
                
                <div class="news-text">
                  <?php echo $news['content']; ?>
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