const { test, expect } = require('@playwright/test');
const { adminLogin } = require('../utils/auth');
const { openAddModal, submitForm, verifyInTable, editItem, deleteItem } = require('../utils/crudHelper');

test.describe('Admin: Users & Members Modules', () => {
    
    test.beforeEach(async ({ page }) => {
        await adminLogin(page);
    });

    test('Membership CRUD', async ({ page }) => {
        await page.goto('/admin/membership');
        
        await openAddModal(page);
        const testMembership = `Playwright Membership ${Date.now()}`;
        await page.fill('input[name="description"]', testMembership);
        await page.click('label.btn:has-text("URL")');
        await page.fill('input[name="path"]', 'http://example.com/membership.pdf');
        await submitForm(page);
        
        await verifyInTable(page, testMembership);
        
        await editItem(page, testMembership);
        const updatedMembership = `${testMembership} - Edited`;
        await page.fill('input[name="description"]', updatedMembership);
        await submitForm(page);
        
        await verifyInTable(page, updatedMembership);
        
        await deleteItem(page, updatedMembership);
        await expect(page.locator(`tbody tr:has-text("${updatedMembership}")`)).toHaveCount(0);
    });

    test('Users CRUD', async ({ page }) => {
        // Warning: The aauthusers might use a different URL structure or require specific group IDs
        await page.goto('/admin/aauthusers');
        
        await openAddModal(page);
        const testUser = `playwright_user_${Date.now()}`;
        await page.fill('input[name="username"]', testUser);
        await page.fill('input[name="email"]', `${testUser}@example.com`);
        await page.fill('input[name="password"]', 'Password123!');
        // Assuming there is a group select, let's select the first valid option
        await page.selectOption('select[name="group"]', { index: 1 });
        await submitForm(page);
        
        await verifyInTable(page, testUser);
        
        // Users might not use the standard modal for edit/delete in the same way, but let's try
        await editItem(page, testUser);
        const updatedEmail = `${testUser}_updated@example.com`;
        await page.fill('input[name="email"]', updatedEmail);
        await submitForm(page);
        
        await verifyInTable(page, updatedEmail);
    });
});
