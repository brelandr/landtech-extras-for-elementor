import { defineConfig, devices } from '@playwright/test';

export default defineConfig( {
	testDir: './tests',
	timeout: 60_000,
	fullyParallel: false,
	workers: 1,
	retries: 1,
	use: {
		// Prefer 127.0.0.1 — Chromium can refuse `localhost` when Docker is IPv4-only.
		baseURL: process.env.LTXE_E2E_BASE_URL || process.env.BASE_URL || 'http://127.0.0.1:8888',
		trace: 'on-first-retry',
	},
	projects: [
		{
			name: 'chromium',
			use: { ...devices['Desktop Chrome'] },
		},
	],
} );
