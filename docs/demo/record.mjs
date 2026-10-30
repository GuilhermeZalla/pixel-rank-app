// Run from the repository root. Tool paths can point to tools installed elsewhere.
// PLAYWRIGHT_MODULE=/path/to/playwright/index.mjs FFMPEG_PATH=/path/to/ffmpeg node docs/demo/record.mjs
import { spawn, spawnSync } from 'node:child_process';
import { randomBytes } from 'node:crypto';
import { mkdtemp, mkdir, writeFile, rm } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const temporary = await mkdtemp(join(tmpdir(), 'pixelrank-demo-'));
const output = join(root, 'docs/demo');
const screenshots = join(root, 'docs/screenshots');
const php = process.env.PHP_BINARY || 'php';
const phpOptions = JSON.parse(process.env.PHP_ARGS || '[]');
const ffmpeg = process.env.FFMPEG_PATH || 'ffmpeg';
const modulePath = process.env.PLAYWRIGHT_MODULE;
const { chromium } = await import(modulePath ? pathToFileURL(resolve(modulePath)).href : 'playwright');
const port = Number(process.env.DEMO_PORT || 8137);
const origin = `http://127.0.0.1:${port}`;
const password = randomBytes(24).toString('base64url');
const database = join(temporary, 'demo.sqlite');
const env = {
    ...process.env, PHP_INI_SCAN_DIR: '', PIXELRANK_DEMO_DIR: temporary,
    APP_ENV: 'local', APP_DEBUG: 'false', APP_NAME: 'PixelRank', APP_URL: origin,
    APP_KEY: `base64:${randomBytes(32).toString('base64')}`, APP_LOCALE: 'pt_BR',
    APP_CONFIG_CACHE: join(temporary, 'config.php'),
    DB_CONNECTION: 'sqlite', DB_DATABASE: database, DB_URL: '',
    SESSION_DRIVER: 'database', SESSION_DOMAIN: '', SESSION_SECURE_COOKIE: 'false',
    SESSION_COOKIE: 'pixelrank_demo_session', CACHE_STORE: 'database',
    QUEUE_CONNECTION: 'sync', MAIL_MAILER: 'array', LOG_CHANNEL: 'stderr',
    TWITCH_CLIENT_ID: '', TWITCH_CLIENT_SECRET: '', GAME_API_KEY: '',
};
let server;
let browser;
const report = { viewports: { desktop: '1440x1000', mobile: '390x844' }, screenshots: [], gifs: [], checks: [], errors: [] };
let cleanupPromise;
function cleanup() {
    return cleanupPromise ??= (async () => {
        await browser?.close();
        if (server && server.exitCode === null) {
            await new Promise(resolve => { server.once('exit', resolve); server.kill(); });
        }
        await rm(temporary, { recursive: true, force: true });
    })();
}
process.once('SIGINT', () => { void cleanup().finally(() => process.exit(130)); });
process.once('SIGTERM', () => { void cleanup().finally(() => process.exit(143)); });

function runPhp(args, input) {
    const result = spawnSync(php, [...phpOptions, '-d', 'opcache.enable=0', ...args], { cwd: root, env, encoding: 'utf8', input });
    if (result.status !== 0) throw new Error(result.stderr || result.stdout);
    return result.stdout;
}
async function stable(page) {
    await page.waitForLoadState('networkidle');
    await page.evaluate(() => document.fonts.ready);
    await page.waitForTimeout(350);
}
async function goto(page, path) {
    const response = await page.goto(origin + path);
    if (response.status() !== 200) throw new Error(`${path}: HTTP ${response.status()}`);
    await stable(page);
    await page.evaluate(() => scrollTo(0, 0));
    if (await page.evaluate(() => getComputedStyle(document.body).display !== 'flex')) {
        throw new Error(`Application styles did not load: ${path}`);
    }
}
async function shot(page, name, fullPage = false) {
    await page.screenshot({ path: join(screenshots, `${name}.png`), fullPage });
    const overflow = await page.evaluate(() => document.documentElement.scrollWidth > innerWidth);
    if (overflow) throw new Error(`Horizontal overflow: ${name}`);
    report.screenshots.push(name);
    report.checks.push({ screen: name, brokenImages: await page.locator('img').evaluateAll(images => images.filter(image => !image.complete || !image.naturalWidth).map(image => image.src)) });
}
async function commentsTop(page) {
    await page.locator('#comments-panel').evaluate(element => element.scrollIntoView({ block: 'start' }));
    await page.waitForTimeout(250);
}
async function login(page, data) {
    await goto(page, '/login');
    await page.locator('#email').fill('marina@example.test');
    await page.locator('#password').fill(password);
    await Promise.all([page.waitForURL(origin + '/'), page.locator('form[action="/login"] button[type="submit"]').click()]);
    await stable(page);
    if (!await page.locator(`a[href="/users/${data.userId}/menu1/edit"]`).count()) throw new Error('Login failed');
}
async function record(page, name, mobile, actions) {
    const frames = join(temporary, name);
    await mkdir(frames);
    let index = 0;
    let recording = true;
    let captureError;
    const capture = (async () => {
        while (recording) {
            const started = performance.now();
            try {
                await page.screenshot({ path: join(frames, `${String(index++).padStart(5, '0')}.png`) });
            } catch (error) { if (recording) captureError = error; }
            await new Promise(resolve => setTimeout(resolve, Math.max(0, 250 - (performance.now() - started))));
        }
    })();
    try { await actions(); await page.waitForTimeout(1200); }
    finally { recording = false; await capture; }
    if (captureError) throw captureError;
    const filter = `fps=4,scale=${mobile ? 390 : 960}:-1:flags=lanczos,split[s0][s1];[s0]palettegen=max_colors=128:stats_mode=diff[p];[s1][p]paletteuse=dither=bayer:bayer_scale=3:diff_mode=rectangle`;
    const result = spawnSync(ffmpeg, ['-hide_banner', '-loglevel', 'error', '-y', '-framerate', '4', '-i', join(frames, '%05d.png'), '-filter_complex', filter, '-loop', '0', join(output, `${name}.gif`)], { encoding: 'utf8' });
    if (result.status !== 0) throw new Error(result.stderr);
    report.gifs.push({ name, frames: index, seconds: index / 4 });
    console.log(`Recorded ${name}: ${index} frames`);
}

try {
    await mkdir(output, { recursive: true });
    await mkdir(screenshots, { recursive: true });
    await writeFile(database, '');
    const seedOutput = runPhp(['docs/demo/seed.php'], JSON.stringify({ password }));
    let data;
    try { data = JSON.parse(seedOutput); }
    catch { throw new Error(`Fixture initialization failed: ${seedOutput}`); }
    console.log('Seeded isolated SQLite fixtures.');
    const router = join(root, 'vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php');
    server = spawn(php, [...phpOptions, '-d', 'opcache.enable=0', '-d', 'opcache.enable_cli=0', '-S', `127.0.0.1:${port}`, router], { cwd: join(root, 'public'), env, stdio: ['ignore', 'ignore', 'pipe'], windowsHide: true });
    let serverErrors = '';
    server.stderr.on('data', chunk => { serverErrors = (serverErrors + chunk).slice(-12000); });
    for (let attempt = 0; attempt < 60; attempt++) {
        if (server.exitCode !== null) throw new Error(serverErrors);
        try { if ((await fetch(origin)).ok) break; } catch {}
        await new Promise(resolve => setTimeout(resolve, 250));
        if (attempt === 59) throw new Error(serverErrors || 'Server failed to start');
    }
    browser = await chromium.launch({ headless: true, ...(process.env.BROWSER_PATH ? { executablePath: process.env.BROWSER_PATH } : {}) });
    const context = await browser.newContext({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1, locale: 'pt-BR', colorScheme: 'dark', reducedMotion: 'reduce' });
    const page = await context.newPage();
    page.on('pageerror', error => { report.errors.push(error.message); });
    await goto(page, '/');
    await shot(page, 'home-desktop');
    if (report.checks[0].brokenImages.length) throw new Error('Images failed to load; check network access before recording.');
    await record(page, 'comunidade-desktop', false, async () => {
        await page.waitForTimeout(1800);
        await page.evaluate(() => scrollTo({ top: 560, behavior: 'smooth' }));
        await page.waitForTimeout(1800);
        await goto(page, '/highest-rated');
        await page.evaluate(() => scrollTo({ top: 550, behavior: 'smooth' }));
        await page.waitForTimeout(1800);
        await goto(page, `/reviews/${data.reviewId}`);
        await page.waitForTimeout(1500);
        await page.evaluate(() => scrollTo({ top: 630, behavior: 'smooth' }));
    });
    await goto(page, `/reviews/${data.reviewId}`);
    await shot(page, 'avaliacao-desktop');
    await goto(page, '/login'); await shot(page, 'login-desktop');
    await goto(page, '/register'); await shot(page, 'cadastro-desktop');
    await login(page, data);
    await goto(page, `/reviews/${data.reviewId}`);
    await commentsTop(page);
    await shot(page, 'comentarios-desktop');
    await record(page, 'interacoes-desktop', false, async () => {
        await page.waitForTimeout(1800);
        await page.locator('#comment').fill('A exploração foi o ponto alto para mim. Cada região trouxe uma descoberta diferente!');
        await page.waitForTimeout(900);
        await page.locator('#comment-submit').click();
        await page.locator('#comment-feedback').filter({ hasText: /.+/ }).waitFor({ state: 'visible' });
        const card = page.locator(`#comment-${data.parentId}`);
        await card.scrollIntoViewIfNeeded();
        await page.waitForTimeout(1000);
        await card.locator('.comment-like').click();
        await card.locator('.comment-like[aria-pressed="true"]').waitFor();
        await page.waitForTimeout(1000);
        await card.locator('.reply-comment').click();
        await card.locator('.reply-comment-input').fill('Sim! Voltei para explorar lugares que tinha deixado para depois.');
        await page.waitForTimeout(1000);
        await card.locator('.reply-comment-submit').click();
        await page.waitForTimeout(1400);
        await commentsTop(page);
    });
    await goto(page, `/users/${data.userId}/menu1/edit`); await shot(page, 'dashboard-perfil-desktop');
    await record(page, 'painel-desktop', false, async () => {
        await page.waitForTimeout(1800);
        for (const [menu, screenshot] of [['menu2', 'avaliacoes'], ['menu3', 'comentarios'], ['menu4', 'configuracoes']]) {
            await page.locator(`#tab-${menu}`).click();
            await page.waitForTimeout(1300);
            await shot(page, `dashboard-${screenshot}-desktop`);
        }
        await goto(page, '/notifications'); await shot(page, 'notificacoes-desktop');
        await page.waitForTimeout(1800);
    });
    await goto(page, '/reviews/create'); await shot(page, 'criar-avaliacao-desktop');
    const mobileContext = await browser.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 1, isMobile: true, hasTouch: true, locale: 'pt-BR', colorScheme: 'dark', reducedMotion: 'reduce' });
    const mobile = await mobileContext.newPage();
    mobile.on('pageerror', error => { report.errors.push(error.message); });
    await goto(mobile, '/'); await shot(mobile, 'home-mobile');
    await record(mobile, 'comunidade-mobile', true, async () => {
        await mobile.waitForTimeout(1600);
        for (const top of [420, 850, 1250]) {
            await mobile.evaluate(top => scrollTo({ top, behavior: 'smooth' }), top);
            await mobile.waitForTimeout(1300);
        }
        await goto(mobile, `/reviews/${data.reviewId}`);
        await mobile.waitForTimeout(1500);
        await mobile.evaluate(() => scrollTo({ top: 680, behavior: 'smooth' }));
        await mobile.waitForTimeout(1500);
    });
    await goto(mobile, `/reviews/${data.reviewId}`); await shot(mobile, 'avaliacao-mobile');
    await login(mobile, data);
    await goto(mobile, `/reviews/${data.reviewId}`);
    await commentsTop(mobile);
    await shot(mobile, 'comentarios-mobile');
    if (report.errors.length) throw new Error(`Browser errors: ${report.errors.join('; ')}`);
    await writeFile(join(output, 'capture-report.json'), JSON.stringify(report, null, 2) + '\n');
    console.log(JSON.stringify({ screenshots: report.screenshots.length, gifs: report.gifs, imageIssues: report.checks.filter(check => check.brokenImages.length) }));
} finally {
    await cleanup();
}
