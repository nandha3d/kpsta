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
        // Usually downloads need a file or URL
        await page.click('label.btn:has-text("URL")');
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
        
        await verifyInTable(page, testGallery);
        
        await editItem(page, testGallery);
        const updatedGallery = `${testGallery} - Edited`;
        await page.fill('input[name="name"]', updatedGallery);
        await submitForm(page);
        
        await verifyInTable(page, updatedGallery);
        
        await deleteItem(page, updatedGallery);
        await expect(page.locator(`tbody tr:has-text("${updatedGallery}")`)).toHaveCount(0);
    });

    test('Forms RAR CRUD', async ({ page }) => {
        await page.goto('/admin/forms');
        
        await openAddModal(page);
        const testFormsrar = `Playwright Formsrar ${Date.now()}`;
        await page.fill('input[name="description"]', testFormsrar);
        // Assuming there is a category dropdown. If not, this might fail, but let's try to select the second option.
        await page.selectOption('select[name="category"]', { index: 1 });
        await page.click('label.btn:has-text("URL")');
        await page.fill('input[name="path"]', 'http://example.com/forms.rar');
        await submitForm(page);
        
        await verifyInTable(page, testFormsrar);
        
        await editItem(page, testFormsrar);
        const updatedFormsrar = `${testFormsrar} - Edited`;
        await page.fill('input[name="description"]', updatedFormsrar);
        await submitForm(page);
        
        await verifyInTable(page, updatedFormsrar);
        
        await deleteItem(page, updatedFormsrar);
        await expect(page.locator(`tbody tr:has-text("${updatedFormsrar}")`)).toHaveCount(0);
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
        await page.click('label.btn:has-text("URL")');
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
