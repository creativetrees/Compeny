import { test, expect } from '@playwright/test';

const PAGES = ['/', '/work', '/services', '/pricing', '/process', '/products', '/team', '/about', '/contact', '/start'];

for (const path of PAGES) {
    test(`${path || '/'} — loads with no console errors, on mobile and desktop`, async ({ page }) => {
        const errors = [];
        page.on('console', (msg) => {
            if (msg.type() === 'error') errors.push(msg.text());
        });
        page.on('pageerror', (err) => errors.push(err.message));

        for (const viewport of [{ width: 375, height: 667 }, { width: 1440, height: 900 }]) {
            await page.setViewportSize(viewport);
            const response = await page.goto(path);
            expect(response.status()).toBeLessThan(400);
            await expect(page.locator('h1')).toBeVisible();
        }

        expect(errors, `console errors on ${path}: ${errors.join(' | ')}`).toEqual([]);
    });
}

test('footer CTA body is never blank on any page', async ({ page }) => {
    await page.goto('/');
    // The footer CTA copy block must contain real text, not render empty
    // (regression guard for the <p></p> fallback-bypass bug).
    const footerCtaText = await page.locator('footer .mb-7.max-w-sm').first().innerText();
    expect(footerCtaText.trim().length).toBeGreaterThan(0);
});

test('no link anywhere on the homepage uses an unsafe URL scheme', async ({ page }) => {
    await page.goto('/');
    const hrefs = await page.locator('a[href]').evaluateAll((els) => els.map((el) => el.getAttribute('href')));
    const unsafe = hrefs.filter((href) => /^\s*(javascript|data|vbscript):/i.test(href || ''));
    expect(unsafe).toEqual([]);
});
