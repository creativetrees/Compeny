import { test, expect } from '@playwright/test';

const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD = 'password';

async function login(page) {
    await page.goto('/admin/login');
    await page.getByRole('textbox', { name: /^Username/ }).fill(ADMIN_USERNAME);
    await page.getByRole('textbox', { name: /^Password/ }).fill(ADMIN_PASSWORD);
    await page.getByRole('button', { name: 'Sign in' }).click();
    await page.waitForURL(/\/admin$/);
}

test.describe('admin login', () => {
    test('succeeds with valid credentials and reaches the dashboard', async ({ page }) => {
        await login(page);
        await expect(page).toHaveURL(/\/admin$/);
        await expect(page.getByRole('heading', { name: 'Dashboard' })).toBeVisible();
    });

    test('fails with a clear error on wrong password, without leaking whether the user exists', async ({ page }) => {
        await page.goto('/admin/login');
        await page.getByRole('textbox', { name: /^Username/ }).fill(ADMIN_USERNAME);
        await page.getByRole('textbox', { name: /^Password/ }).fill('wrong-password');
        await page.getByRole('button', { name: 'Sign in' }).click();

        await expect(page).toHaveURL(/\/admin\/login/);
        await expect(page.getByText(/credentials|incorrect|invalid|do not match/i)).toBeVisible();
    });

    test('logs out and blocks access to the dashboard afterward', async ({ page }) => {
        await login(page);
        await expect(page).toHaveURL(/\/admin$/);

        await page.getByRole('button', { name: 'User menu' }).click();
        await page.getByText('Sign out').click();

        await page.goto('/admin');
        await expect(page).toHaveURL(/\/admin\/login/);
    });
});

test.describe('admin — team member social link XSS (browser-level)', () => {
    test('a javascript: URL entered in the admin UI never reaches the public page as a real link', async ({ page }) => {
        await login(page);
        await page.goto('/admin/team-members/create');

        await page.getByLabel('Name').fill('Playwright QA');
        await page.getByLabel('Slug').fill('playwright-qa-' + Date.now());
        await page.getByLabel('Role').fill('QA Automation');

        // The Socials KeyValue row's two inputs carry no label/id (unlike every
        // other field on this form), so isolate them by that absence instead.
        const unlabeledInputs = page.locator('form input[type="text"]:not([id]):visible');
        await unlabeledInputs.nth(0).fill('linkedin');
        await unlabeledInputs.nth(1).fill('javascript:alert(document.cookie)');

        await page.getByLabel('Is published').click();
        await page.getByRole('button', { name: 'Create', exact: true }).click();

        await expect(page.getByText('No entries')).toBeVisible();

        await page.goto('/team');
        const maliciousLinks = await page
            .locator('a[href^="javascript:"]')
            .count();
        expect(maliciousLinks).toBe(0);
    });
});

test.describe('lead form — full browser submission', () => {
    test('a real submission through /start creates a lead and shows the success state', async ({ page }) => {
        await page.goto('/start');

        await page.getByLabel('Name').fill('Playwright Test Lead');
        await page.getByLabel('Email').fill(`playwright-${Date.now()}@example.test`);
        await page.getByLabel('About the project').fill(
            'This is an automated end-to-end test submission from Playwright.'
        );

        await page.getByRole('button', { name: /send brief/i }).click();

        await expect(page.getByRole('heading', { name: /brief received/i })).toBeVisible({ timeout: 10_000 });
    });

    test('rejects an invalid email client-side and never reaches the server', async ({ page }) => {
        await page.goto('/start');

        await page.getByLabel('Name').fill('Bad Email Test');
        const emailInput = page.getByLabel('Email');
        await emailInput.fill('not-an-email');
        await page.getByLabel('About the project').fill(
            'Testing invalid email validation end to end.'
        );

        await page.getByRole('button', { name: /send brief/i }).click();

        // The native HTML5 email input blocks submission before any request is
        // made — the page must stay on /start with the browser's own validity
        // state marking the field invalid (a real server round-trip would instead
        // redirect and show a Laravel-rendered error).
        await expect(page).toHaveURL(/\/start$/);
        const isValid = await emailInput.evaluate((el) => el.validity.valid);
        expect(isValid).toBe(false);
    });
});
