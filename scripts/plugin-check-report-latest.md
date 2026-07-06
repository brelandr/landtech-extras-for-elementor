# Plugin Check / PHPCS security pass (latest)

- **Ruleset:** `phpcs.security.xml` (`WordPress.Security`)
- **Regenerate:** from the plugin root:
  ```bash
  vendor/bin/phpcs --standard=phpcs.security.xml --report=full
  ```
- **Optional CSV:** `vendor/bin/phpcs --standard=phpcs.security.xml --report=csv`
- **Last verified:** 2026-05-09 — **0 errors, 0 warnings** (summary)

Timestamped exports matching `scripts/landtech-extras-for-elementor-*-php-*.md` are gitignored; keep this file or a fresh export for CI comparison.
