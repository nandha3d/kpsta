<?php
$section = $section ?? 'state';
$sectionMeta = $sectionMeta ?? [];
$activeRole = $activeRole ?? '';
$roleCounts = $roleCounts ?? [];
$districts = $districts ?? [];
$selectedDistrict = $selectedDistrict ?? '';
$baseRoute = $sectionMeta['base_route'] ?? 'admin/state_leaders';
$roles = $sectionMeta['roles'] ?? [];
$addLabel = ($activeRole !== '' && isset($roles[$activeRole]))
    ? ('Add ' . $roles[$activeRole]['label'])
    : ('Add ' . ($sectionMeta['title'] ?? 'Leader'));
?>
<div class="content-wrapper" id="leaderSectionRoot"
     data-section="<?php echo html_escape($section); ?>"
     data-base-url="<?php echo base_url($baseRoute); ?>"
     data-active-role="<?php echo html_escape($activeRole); ?>"
     data-active-district="<?php echo html_escape($selectedDistrict); ?>">

    <section class="content-header">
        <div class="page-header">
            <div class="box-layout">
                <div class="col-xs-7 col-sm-7 col-md-7 va-m">
                    <h3 class="pull-left" style="margin: 0; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <span><?php echo html_escape($sectionMeta['title'] ?? 'Leaders'); ?></span>
                        <?php if ($activeRole !== '' && isset($roles[$activeRole])) { ?>
                            <span class="badge" style="background: #6366f1; color: #fff; font-size: 12px; padding: 5px 10px; border-radius: 999px;">
                                <?php echo html_escape($roles[$activeRole]['label']); ?>
                            </span>
                        <?php } ?>
                    </h3>
                    <div class="clearfix"></div>
                    <p style="margin: 4px 0 0; font-size: 12.5px; color: #64748b;">
                        <?php echo html_escape($sectionMeta['description'] ?? ''); ?>
                    </p>
                </div>
                <div class="col-xs-5 col-sm-5 col-md-5 va-m">
                    <div id="toolbar" class="toolbar text-right">
                        <div class="std-toolbar btn-group">
                            <a class="btn btn-primary" data-toggle="modal" data-target="#modal" data-id="new" data-title-new="<?php echo html_escape($addLabel); ?>">
                                <i class="fa fa-plus"></i> <span><?php echo html_escape($addLabel); ?></span>
                            </a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 780px; max-width: 96vw;">
            <div class="modal-content modal-content-form">
                <?php echo isset($form) ? $form : ''; ?>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border" style="padding: 14px 16px;">
                        <!-- Role Quick-Navigation Pills -->
                        <div class="role-pills-bar" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
                            <?php
                            $allUrl = base_url($baseRoute) . ($section === 'district' && $selectedDistrict !== '' ? ('?district=' . urlencode($selectedDistrict)) : '');
                            $allCount = isset($roleCounts['all']) ? (int)$roleCounts['all'] : 0;
                            ?>
                            <a href="<?php echo $allUrl; ?>"
                               class="btn btn-sm role-pill-link <?php echo $activeRole === '' ? 'btn-primary' : 'btn-default'; ?>"
                               data-role-slug=""
                               style="border-radius: 999px; font-weight: 600;">
                                All <span class="badge role-count-badge" data-role-count="all" style="margin-left: 4px; background: rgba(0,0,0,0.12); color: inherit;"><?php echo $allCount; ?></span>
                            </a>
                            <?php foreach ($roles as $slug => $role) {
                                $rUrl = base_url($baseRoute . '/' . $slug) . ($section === 'district' && $selectedDistrict !== '' ? ('?district=' . urlencode($selectedDistrict)) : '');
                                $rCount = isset($roleCounts[$slug]) ? (int)$roleCounts[$slug] : 0;
                                $isCurrent = ($activeRole === $slug);
                            ?>
                                <a href="<?php echo $rUrl; ?>"
                                   class="btn btn-sm role-pill-link <?php echo $isCurrent ? 'btn-primary' : 'btn-default'; ?>"
                                   data-role-slug="<?php echo html_escape($slug); ?>"
                                   style="border-radius: 999px; font-weight: 600;">
                                    <?php echo html_escape($role['label']); ?>
                                    <span class="badge role-count-badge" data-role-count="<?php echo html_escape($slug); ?>" style="margin-left: 4px; background: rgba(0,0,0,0.12); color: inherit;"><?php echo $rCount; ?></span>
                                </a>
                            <?php } ?>
                        </div>

                        <!-- Clean Section-Specific Filter Toolbar -->
                        <div class="row" style="display: flex; flex-wrap: wrap; align-items: flex-end;">
                            <div class="<?php echo $section === 'district' ? 'col-md-3' : 'col-md-4'; ?> col-sm-6" style="margin-bottom: 8px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Search</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search name, phone, email..."
                                           name="search"
                                           value="<?php echo html_escape((string)$this->input->get('search')); ?>"
                                           data-href="<?php echo base_url($baseRoute . ($activeRole !== '' ? '/' . $activeRole : '')); ?>">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary btn-flat" name="btn-search"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </div>

                            <?php if ($section === 'district') { ?>
                                <div class="col-md-3 col-sm-6" style="margin-bottom: 8px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #555;">District</label>
                                    <select name="district-search" class="form-control filter-control">
                                        <option value="">All 14 Districts</option>
                                        <?php foreach ($districts as $d) { ?>
                                            <option value="<?php echo html_escape($d); ?>" <?php echo $selectedDistrict === $d ? 'selected' : ''; ?>>
                                                <?php echo html_escape($d); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } ?>

                            <div class="<?php echo $section === 'district' ? 'col-md-2' : 'col-md-3'; ?> col-sm-6" style="margin-bottom: 8px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Publish Status</label>
                                <?php $pubVal = (string)$this->input->get('is_publish'); ?>
                                <select name="publish-search" class="form-control filter-control">
                                    <option value="">All Status</option>
                                    <option value="1" <?php echo $pubVal === '1' ? 'selected' : ''; ?>>Active / Published</option>
                                    <option value="0" <?php echo $pubVal === '0' ? 'selected' : ''; ?>>Inactive / Unpublished</option>
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-6" style="margin-bottom: 8px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Sort By</label>
                                <?php $sortVal = (string)($this->input->get('sort') ?: 'position-asc'); ?>
                                <select name="sort-search" class="form-control filter-control">
                                    <option value="position-asc" <?php echo $sortVal === 'position-asc' ? 'selected' : ''; ?>>Hierarchy Order</option>
                                    <option value="name-asc" <?php echo $sortVal === 'name-asc' ? 'selected' : ''; ?>>Name (A to Z)</option>
                                    <option value="name-desc" <?php echo $sortVal === 'name-desc' ? 'selected' : ''; ?>>Name (Z to A)</option>
                                    <option value="designation-asc" <?php echo $sortVal === 'designation-asc' ? 'selected' : ''; ?>>Role (A to Z)</option>
                                </select>
                            </div>

                            <div class="<?php echo $section === 'district' ? 'col-md-2' : 'col-md-3'; ?> col-sm-12 text-right" style="margin-bottom: 8px;">
                                <button type="button" class="btn btn-default" id="btn-reset-section-filters" title="Reset filters">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                                <a class="btn btn-danger" href=""
                                   data-href="<?php echo base_url($baseRoute . '/batch_delete' . ($activeRole !== '' ? '?role=' . urlencode($activeRole) : '')); ?>"
                                   data-toggle="confirmation"
                                   data-precheck="batchActionPrecheck"
                                   data-message="Delete the selected leaders?"
                                   data-confirm-text="Delete"
                                   data-confirm-callback="executeSectionBatchAction"
                                   data-cancel-text="Cancel"
                                   data-cancel-callback="dismissConfirmation">
                                    <i class="fa fa-trash-o"></i> Delete Selected
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="box-body" id="table-content">
                        <?php echo $content; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
