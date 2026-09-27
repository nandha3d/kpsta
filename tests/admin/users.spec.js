const { test, expect } = require('@playwright/test');
const { adminLogin } = require('../utils/auth');

test.describe('Admin: Users & System Settings Modules', () => {
    
    test.beforeEach(async ({ page }) => {
        await adminLogin(page);
    });

    test('Login Users (Aauth Users) CRUD', async ({ page }) => {
        await page.goto('/admin/aauth/users');
        await page.waitForLoadState('networkidle');

        // 1. Open Add User Modal
        const newBtn = page.locator('.page-header a[data-target="#modal"], .toolbar a[data-target="#modal"]');
        await expect(newBtn).toBeVisible();
        await newBtn.click();

        const modal = page.locator('#modal');
        await expect(modal).toBeVisible();

        // 2. Fill User form
        const testUser = `pw_user_${Date.now()}`;
        const testEmail = `${testUser}@example.com`;
        await page.fill('#modal input[name="username"]', testUser);
        await page.fill('#modal input[name="email"]', testEmail);
        await page.fill('#modal input[name="password"]', 'Secret123!');
        
        // Select Group (e.g. 1 for Admin or 2 for Public)
        await page.selectOption('#modal select[name="group"]', { index: 1 });
        // Select Status (0 for Active)
        await page.selectOption('#modal select[name="status"]', '0');
        
        // Submit
        await page.click('#addNewUserForm button[type="submit"]');

        // Wait for modal to hide
        await expect(modal).not.toBeVisible({ timeout: 10000 });

        // 3. Verify user in table
        const userRow = page.locator(`#usersManage tbody tr:has-text("${testUser}")`);
        await expect(userRow).toBeVisible();

        // 4. Edit user
        await userRow.locator('a#edit, a:has-text("Edit")').click();
        await expect(modal).toBeVisible();
        await page.waitForSelector('#editUserForm', { state: 'visible' });

        const updatedEmail = `${testUser}_updated@example.com`;
        await page.fill('#editUserForm input[name="email"]', updatedEmail);
        // Ensure status is active
        await page.selectOption('#editUserForm select[name="status"]', '0');

        await page.click('#editUserForm button[type="submit"]');
        await expect(modal).not.toBeVisible({ timeout: 10000 });

        // Verify updated email in table
        await expect(page.locator(`#usersManage tbody tr:has-text("${updatedEmail}")`)).toBeVisible();
    });

    test('User Groups (Aauth Groups) CRUD', async ({ page }) => {
        await page.goto('/admin/aauth/group');
        await page.waitForLoadState('networkidle');

        // 1. Open Add Group Modal
        const newBtn = page.locator('.page-header a[data-target="#modal"], .toolbar a[data-target="#modal"]');
        await expect(newBtn).toBeVisible();
        await newBtn.click();

        const modal = page.locator('#modal');
        await expect(modal).toBeVisible();

        // 2. Fill Group Form
        const testGroup = `PW Group ${Date.now()}`;
        const testDesc = `Description for ${testGroup}`;
        await page.fill('#modal input[name="name"]', testGroup);
        await page.fill('#modal input[name="definition"]', testDesc);

        // Submit
        await page.click('#modal button[type="submit"]');
        await expect(modal).not.toBeVisible({ timeout: 10000 });

        // 3. Verify in table
        const groupRow = page.locator(`#table-content tbody tr:has-text("${testGroup}")`);
        await expect(groupRow).toBeVisible();

        // 4. Edit Group
        await groupRow.locator('a.edit, a:has-text("Edit")').click();
        await expect(modal).toBeVisible();
        await page.waitForSelector('#modal input[name="definition"]', { state: 'visible' });

        const updatedDesc = `${testDesc} - Edited`;
        await page.fill('#modal input[name="definition"]', updatedDesc);
        await page.click('#modal button[type="submit"]');
        await expect(modal).not.toBeVisible({ timeout: 10000 });

        // Verify updated definition
        await expect(page.locator(`#table-content tbody tr:has-text("${updatedDesc}")`)).toBeVisible();
    });

    test('System Settings Configuration', async ({ page }) => {
        await page.goto('/admin/settings');
        await page.waitForLoadState('networkidle');

        await expect(page.locator('h3.box-title:has-text("Office Bearers Rollover Setting")')).toBeVisible();

        // Change rollover month to 3 (March)
        await page.selectOption('select[name="rollover_month"]', '3');
        await page.click('button[type="submit"]:has-text("Save Settings")');

        // Verify success alert
        await expect(page.locator('.alert-success')).toContainText('Settings updated successfully');
    });
});
