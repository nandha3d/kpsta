const { test, expect } = require('@playwright/test');
const { adminLogin } = require('../utils/auth');
const { openAddModal, submitForm, verifyInTable, editItem, deleteItem } = require('../utils/crudHelper');

test.describe('Admin: News & Circulars Modules', () => {
    
    test.beforeEach(async ({ page }) => {
        await adminLogin(page);
    });

    test('Flash News - KPSTA CRUD', async ({ page }) => {
        await page.goto('/admin/flash_news/kpsta');
        
        // Create
        await openAddModal(page);
        const testNews = `Playwright KPSTA News ${Date.now()}`;
        await page.fill('input[name="description"]', testNews);
        await submitForm(page);
        
        // Verify
        await verifyInTable(page, testNews);
        
        // Edit
        await editItem(page, testNews);
        const updatedNews = `${testNews} - Edited`;
        await page.fill('input[name="description"]', updatedNews);
        await submitForm(page);
        
        // Verify Edit
        await verifyInTable(page, updatedNews);
        
        // Delete
        await deleteItem(page, updatedNews);
        
        // Verify Deletion
        await expect(page.locator(`tbody tr:has-text("${updatedNews}")`)).toHaveCount(0);
    });

    test('Order & Circular - General CRUD', async ({ page }) => {
        await page.goto('/admin/order-circular/general');
        
        // Create
        await openAddModal(page);
        const testOrder = `Playwright General Order ${Date.now()}`;
        await page.fill('input[name="date"]', '01-01-2027');
        await page.keyboard.press('Escape'); // close datepicker
        await page.fill('input[name="description"]', testOrder);
        await page.fill('input[name="date"]', '01-01-2027');
        await page.keyboard.press('Escape'); // close datepicker
        // Force check the URL radio
        await page.check('input[name="upload_type"][value="URL"]', { force: true });
        // The URL field is now required, make sure to fill it
        await page.fill('input[name="path"]', 'http://example.com');
        
        // Remove HTML5 validation temporarily for playwright to allow testing the JS submit
        await page.evaluate(() => document.querySelector('#modal form').setAttribute('novalidate', 'true'));
        await submitForm(page);
        
        // Verify
        await verifyInTable(page, testOrder);
        
        // Edit
        await editItem(page, testOrder);
        const updatedOrder = `${testOrder} - Edited`;
        await page.fill('input[name="description"]', updatedOrder);
        // Remove HTML5 validation temporarily for playwright
        await page.evaluate(() => document.querySelector('#modal form').setAttribute('novalidate', 'true'));
        await submitForm(page);
        await verifyInTable(page, updatedOrder);
        
        // Delete
        await deleteItem(page, updatedOrder);
        await expect(page.locator(`tbody tr:has-text("${updatedOrder}")`)).toHaveCount(0);
    });

    test('News Module CRUD', async ({ page }) => {
        await page.goto('/admin/news');
        
        // Create
        await openAddModal(page);
        const testTitle = `Playwright Main News ${Date.now()}`;
        await page.fill('input[name="heading"]', testTitle);
        // Fill summernote editable area properly via jQuery
        await page.evaluate(() => {
            if ($('.summernote').length) {
                $('.summernote').summernote('code', 'Test description content for news module');
            } else {
                document.querySelector('textarea[name="content"]').value = 'Test description content for news module';
            }
        });
        // Force check Publish Yes
        await page.check('input[name="publish"][value="1"]', { force: true });
        
        // Remove HTML5 validation temporarily for playwright
        await page.evaluate(() => document.querySelector('#modal form').setAttribute('novalidate', 'true'));
        await submitForm(page);
        
        // Verify
        await verifyInTable(page, testTitle);
        
        // Edit
        await editItem(page, testTitle);
        const updatedTitle = `${testTitle} - Edited`;
        await page.fill('input[name="heading"]', updatedTitle);
        // Force check Publish Yes
        await page.check('input[name="publish"][value="1"]', { force: true });
        // Remove HTML5 validation temporarily for playwright
        await page.evaluate(() => document.querySelector('#modal form').setAttribute('novalidate', 'true'));
        await submitForm(page);
        await verifyInTable(page, updatedTitle);
        
        // Delete
        await deleteItem(page, updatedTitle);
        await expect(page.locator(`tbody tr:has-text("${updatedTitle}")`)).toHaveCount(0);
    });
});
