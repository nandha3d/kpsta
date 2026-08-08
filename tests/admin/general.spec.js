const { test, expect } = require('@playwright/test');
const { adminLogin } = require('../utils/auth');
const { openAddModal, submitForm, verifyInTable, editItem, deleteItem } = require('../utils/crudHelper');

test.describe('Admin: General Modules', () => {
    
    test.beforeEach(async ({ page }) => {
        await adminLogin(page);
    });

    test('Quicklinks CRUD', async ({ page }) => {
        await page.goto('/admin/quicklink');
        
        await openAddModal(page);
        const testLink = `Playwright Link ${Date.now()}`;
        await page.fill('input[name="description"]', testLink);
        await page.fill('input[name="path"]', 'http://example.com');
        await submitForm(page);
        
        await verifyInTable(page, testLink);
        
        await editItem(page, testLink);
        const updatedLink = `${testLink} - Edited`;
        await page.fill('input[name="description"]', updatedLink);
        await submitForm(page);
        
        await verifyInTable(page, updatedLink);
        
        await deleteItem(page, updatedLink);
        await expect(page.locator(`tbody tr:has-text("${updatedLink}")`)).toHaveCount(0);
    });

    test('Melakal CRUD', async ({ page }) => {
        await page.goto('/admin/melakal');
        
        await openAddModal(page);
        const testMelakal = `Playwright Melakal ${Date.now()}`;
        await page.fill('input[name="description"]', testMelakal);
        await page.fill('input[name="path"]', 'http://example.com/melakal');
        await submitForm(page);
        
        await verifyInTable(page, testMelakal);
        
        await editItem(page, testMelakal);
        const updatedMelakal = `${testMelakal} - Edited`;
        await page.fill('input[name="description"]', updatedMelakal);
        await submitForm(page);
        
        await verifyInTable(page, updatedMelakal);
        
        await deleteItem(page, updatedMelakal);
        await expect(page.locator(`tbody tr:has-text("${updatedMelakal}")`)).toHaveCount(0);
    });
    test('Slider CRUD', async ({ page }) => {
        await page.goto('/admin/slider');
        
        await openAddModal(page);
        const testSlider = `Playwright Slider ${Date.now()}`;
        await page.fill('input[name="description"]', testSlider);
        await page.setInputFiles('input[name="image"]', 'tests/utils/dummy.jpg');
        await submitForm(page);
        
        await verifyInTable(page, testSlider);
        
        await editItem(page, testSlider);
        const updatedSlider = `${testSlider} - Edited`;
        await page.fill('input[name="description"]', updatedSlider);
        await submitForm(page);
        
        await verifyInTable(page, updatedSlider);
        
        await deleteItem(page, updatedSlider);
        await expect(page.locator(`tbody tr:has-text("${updatedSlider}")`)).toHaveCount(0);
    });
});
