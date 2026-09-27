const { test, expect } = require('@playwright/test');
const { adminLogin } = require('../utils/auth');
const { openAddModal, submitForm, verifyInTable, editItem, deleteItem } = require('../utils/crudHelper');

test.describe('Admin: Organization Modules', () => {
    
    test.beforeEach(async ({ page }) => {
        await adminLogin(page);
    });

    test('State Office Bearers CRUD', async ({ page }) => {
        await page.goto('/admin/office_bearer');
        
        await openAddModal(page);
        const testName = `Playwright OB ${Date.now()}`;
        await page.fill('input[name="name"]', testName);
        // Select Vice President (value 6) which allows multiple bearers without is_single conflict
        await page.selectOption('select[name="designation"]', { value: '6' });
        await page.fill('input[name="phone"]', '9876543210');
        await page.fill('input[name="email"]', 'test@kpsta.test');
        await page.setInputFiles('input[name="image"]', 'tests/utils/dummy.jpg');
        await submitForm(page);
        
        await verifyInTable(page, testName);
        
        await editItem(page, testName);
        const updatedName = `${testName} - Edited`;
        await page.fill('input[name="name"]', updatedName);
        await submitForm(page);
        
        await verifyInTable(page, updatedName);
        
        await deleteItem(page, updatedName);
        await expect(page.locator(`tbody tr:has-text("${updatedName}")`)).toHaveCount(0);
    });

    test('Former Leaders CRUD', async ({ page }) => {
        await page.goto('/admin/office_bearer?is_former=1');
        
        await openAddModal(page);
        const testFormer = `Playwright Former ${Date.now()}`;
        await page.fill('input[name="name"]', testFormer);
        await page.selectOption('select[name="designation"]', { value: '6' });
        await page.click('label.btn:has-text("Former")');
        await page.fill('input[name="phone"]', '9876543211');
        await page.setInputFiles('input[name="image"]', 'tests/utils/dummy.jpg');
        await submitForm(page);
        
        await verifyInTable(page, testFormer);
        
        await editItem(page, testFormer);
        const updatedFormer = `${testFormer} - Edited`;
        await page.fill('input[name="name"]', updatedFormer);
        await submitForm(page);
        
        await verifyInTable(page, updatedFormer);
        
        await deleteItem(page, updatedFormer);
        await expect(page.locator(`tbody tr:has-text("${updatedFormer}")`)).toHaveCount(0);
    });

    test('Adayapaka Sabham CRUD', async ({ page }) => {
        await page.goto('/admin/adayapaka_sabham');
        
        await openAddModal(page);
        const testSabham = `Playwright Sabham ${Date.now()}`;
        await page.fill('input[name="description"]', testSabham);
        await page.click('label.btn:has-text("URL")');
        await page.fill('input[name="path"]', 'http://example.com');
        await page.setInputFiles('input[name="image"]', 'tests/utils/dummy.jpg');
        await submitForm(page);
        
        await verifyInTable(page, testSabham);
        
        await editItem(page, testSabham);
        const updatedSabham = `${testSabham} - Edited`;
        await page.fill('input[name="description"]', updatedSabham);
        await submitForm(page);
        
        await verifyInTable(page, updatedSabham);
        
        await deleteItem(page, updatedSabham);
        await expect(page.locator(`tbody tr:has-text("${updatedSabham}")`)).toHaveCount(0);
    });

    test('Districts & District Office Bearers CRUD', async ({ page }) => {
        // 1. Create a new District
        await page.goto('/admin/district');
        await openAddModal(page);
        const testDistrict = `Test Dist ${Date.now()}`;
        await page.fill('input[name="district"]', testDistrict);
        await page.fill('input[name="website_url"]', 'http://example.com/dist');
        await submitForm(page);
        
        await verifyInTable(page, testDistrict);
        
        // 2. Open this new district's office bearers page
        const row = page.locator(`tbody tr:has-text("${testDistrict}")`).first();
        const distUrl = await row.locator('a[href*="/admin/district/"]').first().getAttribute('href');
        await page.goto(distUrl);
        
        // 3. Add a District Office Bearer
        await openAddModal(page);
        const testOB = `Playwright DOB ${Date.now()}`;
        await page.fill('input[name="name"]', testOB);
        await page.selectOption('select[name="designation"]', { index: 1 });
        await page.setInputFiles('input[name="image"]', 'tests/utils/dummy.jpg');
        await submitForm(page);
        
        await verifyInTable(page, testOB);
        
        // 4. Edit District Office Bearer
        await editItem(page, testOB);
        const updatedOB = `${testOB} - Edited`;
        await page.fill('input[name="name"]', updatedOB);
        await submitForm(page);
        await verifyInTable(page, updatedOB);
        
        // 5. Delete District Office Bearer
        await deleteItem(page, updatedOB);
        await expect(page.locator(`tbody tr:has-text("${updatedOB}")`)).toHaveCount(0);
        
        // 6. Clean up: Delete the test district
        await page.goto('/admin/district');
        await verifyInTable(page, testDistrict);
        await deleteItem(page, testDistrict);
        await expect(page.locator(`tbody tr:has-text("${testDistrict}")`)).toHaveCount(0);
    });
});
