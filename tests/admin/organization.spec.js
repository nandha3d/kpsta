const { test, expect } = require('@playwright/test');
const { adminLogin } = require('../utils/auth');
const { openAddModal, submitForm, verifyInTable, editItem, deleteItem } = require('../utils/crudHelper');

test.describe('Admin: Organization Modules', () => {
    
    test.beforeEach(async ({ page }) => {
        await adminLogin(page);
    });

    test('Office Bearers CRUD', async ({ page }) => {
        await page.goto('/admin/office_bearer');
        
        await openAddModal(page);
        const testName = `Playwright OB ${Date.now()}`;
        await page.fill('input[name="name"]', testName);
        await page.selectOption('select[name="designation"]', { index: 1 });
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

    test('Adayapaka Sabham CRUD', async ({ page }) => {
        await page.goto('/admin/adayapaka_sabham');
        
        await openAddModal(page);
        const testSabham = `Playwright Sabham ${Date.now()}`;
        await page.fill('input[name="description"]', testSabham);
        await page.click('label.btn:has-text("URL")'); // from 'url' value
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
    test('District Office Bearers CRUD', async ({ page }) => {
        // We'll test district 1
        await page.goto('/admin/district/1');
        
        await openAddModal(page);
        const testDistrictOB = `Playwright Dist OB ${Date.now()}`;
        await page.fill('input[name="name"]', testDistrictOB);
        await page.selectOption('select[name="designation"]', { index: 1 });
        await page.setInputFiles('input[name="image"]', 'tests/utils/dummy.jpg');
        await submitForm(page);
        
        await verifyInTable(page, testDistrictOB);
        
        await editItem(page, testDistrictOB);
        const updatedDistrictOB = `${testDistrictOB} - Edited`;
        await page.fill('input[name="name"]', updatedDistrictOB);
        await submitForm(page);
        
        await verifyInTable(page, updatedDistrictOB);
        
        await deleteItem(page, updatedDistrictOB);
        await expect(page.locator(`tbody tr:has-text("${updatedDistrictOB}")`)).toHaveCount(0);
    });
});
