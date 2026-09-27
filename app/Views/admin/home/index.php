<?php
/**
 * Admin dashboard.
 *
 * Every content area is listed with both its admin screen and the public page
 * it feeds, so an editor can jump straight from editing to verifying.
 */
$stats = array(
    array((int) (isset($total_office_bearers) ? $total_office_bearers : 0), 'Office Bearers', 'fa-user', 'bg-primary-soft', 'admin/office_bearer'),
    array((int) (isset($total_news) ? $total_news : 0), 'Published News', 'fa-newspaper-o', 'bg-success-soft', 'admin/news'),
    array((int) (isset($total_orders) ? $total_orders : 0), 'Orders & Circulars', 'fa-book', 'bg-warning-soft', 'admin/order-circular/general'),
    array((int) (isset($total_downloads) ? $total_downloads : 0), 'Downloads', 'fa-download', 'bg-danger-soft', 'admin/download/forms'),
    array((int) (isset($total_gallery) ? $total_gallery : 0), 'Gallery Albums', 'fa-picture-o', 'bg-info-soft', 'admin/gallery'),
    array((int) (isset($total_users) ? $total_users : 0), 'Admin Users', 'fa-users', 'bg-purple-soft', 'admin/aauth/users'),
);

$sections = array(
    array('State Office Bearers', 'admin/office_bearer', 'OfficeBearer', 'fa-user'),
    array('District Office Bearers', 'admin/district', 'District', 'fa-map-marker'),
    array('Former Leaders', 'admin/office_bearer?is_former=1', 'former-leaders', 'fa-history'),
    array('Memorandums', 'admin/memorandums', 'memorandums', 'fa-file-text'),
    array('Notices & Posters', 'admin/notice_poster', 'notice_poster', 'fa-thumb-tack'),
    array('Latest News', 'admin/news', 'news', 'fa-newspaper-o'),
    array('Order & Circular', 'admin/order-circular/general', 'order-circular', 'fa-book'),
    array('Forms', 'admin/download/forms', 'download/forms', 'fa-file-text-o'),
    array('Service Corner', 'admin/ServiceCorner', 'service-corner', 'fa-cubes'),
    array('Melakal', 'admin/melakal', 'melakal', 'fa-smile-o'),
    array('Gallery', 'admin/gallery', 'Gallery', 'fa-picture-o'),
    array('Reaction Gallery', 'admin/reaction_gallery', '', 'fa-comments-o'),
    array('Home Slider & Headings', 'admin/slider', '', 'fa-sliders'),
    array('Quick Links', 'admin/quicklink', 'Quicklink', 'fa-link'),
);
?>
<div class="content-wrapper">
    <section class="content">

        <div class="modern-dashboard-grid">
            <?php foreach ($stats as $stat) { ?>
                <a class="modern-stat-card" href="<?php echo site_url($stat[4]); ?>">
                    <div class="modern-stat-icon <?php echo $stat[3]; ?>">
                        <i class="fa <?php echo $stat[2]; ?>"></i>
                    </div>
                    <div class="modern-stat-info">
                        <h3><?php echo $stat[0]; ?></h3>
                        <p><?php echo $stat[1]; ?></p>
                    </div>
                </a>
            <?php } ?>
        </div>

        <div class="modern-dash-columns">
            <div class="modern-card">
                <div class="modern-card-header modern-flex-between">
                    <h3 class="modern-card-title">Manage &amp; preview</h3>
                    <a class="modern-link" href="<?php echo site_url(); ?>" target="_blank" rel="noopener">
                        Open website <i class="fa fa-external-link"></i>
                    </a>
                </div>
                <div class="modern-card-body">
                    <p class="modern-card-hint">Each section below links to its admin screen and to the page it publishes on the public website.</p>
                    <ul class="modern-section-list">
                        <?php foreach ($sections as $section) { ?>
                            <li>
                                <a class="modern-section-main" href="<?php echo site_url($section[1]); ?>">
                                    <i class="fa <?php echo $section[3]; ?>"></i>
                                    <span><?php echo $section[0]; ?></span>
                                </a>
                                <?php if ($section[2] !== '') { ?>
                                    <a class="modern-section-view" href="<?php echo site_url($section[2]); ?>" target="_blank" rel="noopener" title="Open on the public website">
                                        <i class="fa fa-external-link"></i> View
                                    </a>
                                <?php } else { ?>
                                    <span class="modern-section-view is-muted" title="Appears across the public site">Site-wide</span>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="modern-dash-side">
                <div class="modern-card">
                    <div class="modern-card-header">
                        <h3 class="modern-card-title">Active term</h3>
                    </div>
                    <div class="modern-card-body">
                        <p class="modern-term-value"><?php echo html_escape(isset($active_term) ? $active_term : '-'); ?></p>
                        <p class="modern-card-hint">
                            Office bearers tagged with this term are the ones shown publicly.
                            Change the rollover month under <a class="modern-link" href="<?php echo site_url('admin/settings'); ?>">Settings</a>.
                        </p>
                    </div>
                </div>

                <div class="modern-card">
                    <div class="modern-card-header modern-flex-between">
                        <h3 class="modern-card-title">Recent news</h3>
                        <a class="modern-link" href="<?php echo site_url('admin/news'); ?>">Manage</a>
                    </div>
                    <div class="modern-card-body">
                        <?php if (!empty($recent_news)) { ?>
                            <ul class="modern-recent-list">
                                <?php foreach ($recent_news as $item) { ?>
                                    <li>
                                        <span class="modern-recent-title"><?php echo html_escape($item['heading']); ?></span>
                                        <?php if (empty($item['publish'])) { ?>
                                            <span class="modern-pill is-draft">Draft</span>
                                        <?php } else { ?>
                                            <span class="modern-pill is-live">Live</span>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } else { ?>
                            <p class="modern-card-hint">No news items yet.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>
