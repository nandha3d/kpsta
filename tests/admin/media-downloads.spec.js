const { test, expect } = require('@playwright/test');
const { adminLogin } = require('../utils/auth');
const { openAddModal, submitForm, verifyInTable, editItem, deleteItem } = require('../utils/crudHelper');

test.describe('Admin: Media & Downloads Modules', () => {
    
    test.beforeEach(async ({ page }) => {
        await adminLogin(page);
    });

    test('Reaction Gallery CRUD', async ({ page }) => {
        await page.goto('/admin/reaction_gallery');
        
        await openAddModal(page);
        const testReaction = `Playwright Reaction ${Date.now()}`;
        await page.fill('input[name="description"]', testReaction);
        await page.setInputFiles('input[name="image"]', 'tests/utils/dummy.jpg');
        await submitForm(page);
        
        await verifyInTable(page, testReaction);
        
        await editItem(page, testReaction);
        const updatedReaction = `${testReaction} - Edited`;
        await page.fill('input[name="description"]', updatedReaction);
        await submitForm(page);
        
        await verifyInTable(page, updatedReaction);
        
        await deleteItem(page, updatedReaction);
        await expect(page.locator(`tbody tr:has-text("${updatedReaction}")`)).toHaveCount(0);
    });

    test('Downloads - Forms CRUD', async ({ page }) => {
        await page.goto('/admin/download/forms');
        
        await openAddModal(page);
        const testForm = `Playwright Form ${Date.now()}`;
        await page.fill('input[name="description"]', testForm);
        await page.evaluate(() => {
            const urlRadio = document.querySelector('input[name="upload_type"][value="url"]');
            if (urlRadio) {
                urlRadio.checked = true;
                urlRadio.dispatchEvent(new Event('change', { bubbles: true }));
                const label = urlRadio.closest('label');
                if (label) label.classList.add('active');
            }
            const pathInput = document.querySelector('input[name="path"]');
            if (pathInput) pathInput.removeAttribute('disabled');
        });
        await page.fill('input[name="path"]', 'http://example.com/form.pdf');
        await submitForm(page);
        
        await verifyInTable(page, testForm);
        
        await editItem(page, testForm);
        const updatedForm = `${testForm} - Edited`;
        await page.fill('input[name="description"]', updatedForm);
        await submitForm(page);
        
        await verifyInTable(page, updatedForm);
        
        await deleteItem(page, updatedForm);
        await expect(page.locator(`tbody tr:has-text("${updatedForm}")`)).toHaveCount(0);
    });

    test('Photo Gallery CRUD', async ({ page }) => {
        await page.goto('/admin/gallery');
        
        await openAddModal(page);
        const testGallery = `Playwright Gallery ${Date.now()}`;
        await page.fill('input[name="name"]', testGallery);
        await submitForm(page);
        
        await expect(page.locator(`.thumb-gallery-details:has-text("${testGallery}")`)).toBeVisible();
        
        // Open dropdown gear menu and click Edit
        const album = page.locator(`.thumb:has-text("${testGallery}")`).first();
        await album.locator('.dropdown-toggle').click();
        await album.locator('a.edit').click();
        await page.waitForSelector('#modal.in, #modal.show, #modal:visible', { state: 'visible' });
        
        const updatedGallery = `${testGallery} - Edited`;
        await page.fill('input[name="name"]', updatedGallery);
        await submitForm(page);
        
        await expect(page.locator(`.thumb-gallery-details:has-text("${updatedGallery}")`)).toBeVisible();
        
        // Open dropdown gear menu and click Delete
        const updatedAlbum = page.locator(`.thumb:has-text("${updatedGallery}")`).first();
        await updatedAlbum.locator('.dropdown-toggle').click();
        await updatedAlbum.locator('a[data-target="#delete"]').click();
        await page.waitForSelector('#delete:visible', { state: 'visible' });
        
        await Promise.all([
            page.waitForResponse(res => res.url().includes('/delete')),
            page.locator('#delete button.delete, #delete button.btn-danger').first().click()
        ]);
        
        await page.waitForTimeout(1000);
        await expect(page.locator(`.thumb-gallery-details:has-text("${updatedGallery}")`)).toHaveCount(0);
    });

    test('Academic Corner Downloads CRUD', async ({ page }) => {
        await page.goto('/admin/download/academic_corner');
        
        await openAddModal(page);
        const testAcademic = `Playwright Academic ${Date.now()}`;
        await page.fill('input[name="description"]', testAcademic);
        await page.evaluate(() => {
            if (window.$ && $('select[name="category"]').length) {
                const opt = $('select[name="category"] option').eq(1).val() || $('select[name="category"] option').last().val();
                if (opt) $('select[name="category"]').val(opt).trigger('change');
            }
            const urlRadio = document.querySelector('input[name="upload_type"][value="url"]');
            if (urlRadio) {
                urlRadio.checked = true;
                urlRadio.dispatchEvent(new Event('change', { bubbles: true }));
                const label = urlRadio.closest('label');
                if (label) label.classList.add('active');
            }
            const pathInput = document.querySelector('input[name="path"]');
            if (pathInput) pathInput.removeAttribute('disabled');
        });
        await page.fill('input[name="path"]', 'http://example.com/academic.pdf');
        await submitForm(page);
        
        await verifyInTable(page, testAcademic);
        
        await editItem(page, testAcademic);
        const updatedAcademic = `${testAcademic} - Edited`;
        await page.fill('input[name="description"]', updatedAcademic);
        await submitForm(page);
        
        await verifyInTable(page, updatedAcademic);
        
        await deleteItem(page, updatedAcademic);
        await expect(page.locator(`tbody tr:has-text("${updatedAcademic}")`)).toHaveCount(0);
    });

    test('Melakal Dynamic Category CRUD', async ({ page }) => {
        await page.goto('/admin/melakal');
        
        await openAddModal(page);
        const testMelakal = `Playwright Melakal ${Date.now()}`;
        const newCategoryName = `Cat ${Date.now()}`;
        
        // Dynamically create category using Select2 tags
        await page.locator('.select2-category + .select2-container').click();
        await page.locator('.select2-container--open .select2-search__field').fill(newCategoryName);
        await page.keyboard.press('Enter');
        
        await page.fill('input[name="description"]', testMelakal);
        await page.evaluate(() => {
            const urlRadio = document.querySelector('input[name="upload_type"][value="url"]');
            if (urlRadio) {
                urlRadio.checked = true;
                urlRadio.dispatchEvent(new Event('change', { bubbles: true }));
                const label = urlRadio.closest('label');
                if (label) label.classList.add('active');
            }
            const pathInput = document.querySelector('input[name="path"]');
            if (pathInput) pathInput.removeAttribute('disabled');
        });
        await page.fill('input[name="path"]', 'http://example.com/melakal.pdf');
        await submitForm(page);
        
        await verifyInTable(page, testMelakal);
        
        await deleteItem(page, testMelakal);
        await expect(page.locator(`tbody tr:has-text("${testMelakal}")`)).toHaveCount(0);
    });

    test('Melakal PDF File Upload via Browse button', async ({ page }) => {
        await page.goto('/admin/melakal');
        
        await openAddModal(page);
        const testMelakalFile = `Playwright Melakal File ${Date.now()}`;
        
        await page.fill('input[name="description"]', testMelakalFile);
        // Upload file directly via input[name="file"]
        await page.setInputFiles('input[name="file"]', 'tests/utils/dummy.jpg');
        
        // Verify hidden pdfName input is populated after AJAX upload
        await expect(page.locator('input[name="pdfName"]')).not.toHaveValue('');
        
        await submitForm(page);
        await verifyInTable(page, testMelakalFile);
        
        await deleteItem(page, testMelakalFile);
        await expect(page.locator(`tbody tr:has-text("${testMelakalFile}")`)).toHaveCount(0);
    });
});
