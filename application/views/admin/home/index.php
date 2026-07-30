<div class="content-wrapper">
    <section class="content-header">
        <h2 class="modern-page-title">Dashboard</h2>
    </section>

    <section class="content">
        <div class="modern-dashboard-grid">
            <div class="modern-stat-card bg-primary-soft">
                <div class="modern-stat-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="modern-stat-info">
                    <h3><?php echo isset($total_users) ? $total_users : 0; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            
            <div class="modern-stat-card bg-success-soft">
                <div class="modern-stat-icon">
                    <i class="fa fa-newspaper-o"></i>
                </div>
                <div class="modern-stat-info">
                    <h3><?php echo isset($total_news) ? $total_news : 0; ?></h3>
                    <p>News Articles</p>
                </div>
            </div>

            <div class="modern-stat-card bg-warning-soft">
                <div class="modern-stat-icon">
                    <i class="fa fa-picture-o"></i>
                </div>
                <div class="modern-stat-info">
                    <h3><?php echo isset($total_gallery) ? $total_gallery : 0; ?></h3>
                    <p>Gallery Items</p>
                </div>
            </div>

            <div class="modern-stat-card bg-danger-soft">
                <div class="modern-stat-icon">
                    <i class="fa fa-download"></i>
                </div>
                <div class="modern-stat-info">
                    <h3><?php echo isset($total_downloads) ? $total_downloads : 0; ?></h3>
                    <p>Downloads</p>
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="modern-card-header">
                <h3 class="modern-card-title">Welcome to the KPSTA Admin Panel</h3>
            </div>
            <div class="modern-card-body">
                <p>From this modern dashboard, you can manage all aspects of the KPSTA website. Use the sidebar navigation on the left to add new Flash News, update Orders &amp; Circulars, manage Gallery images, and administer user access.</p>
                <p>The system is built to reflect changes instantly on the public-facing website.</p>
            </div>
        </div>
    </section>
</div>