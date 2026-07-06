# Admin POST & AJAX handlers — security checklist

This plugin does **not** register custom REST routes (`register_rest_route`). Mutating entry points live under **`admin_post_*`** and **`wp_ajax_*`**.

## Required pattern per handler

| Step | Requirement |
|------|-------------|
| 1 | **Nonce** verified first (`wp_verify_nonce` / `check_ajax_referer`). On failure: `wp_die( 403 )` or `wp_send_json_error( …, 403 )`. |
| 2 | **Capability**: `current_user_can( 'manage_options' )` (or narrower capability documented per route). |
| 3 | **Input**: read `$_POST` / `$_GET` **after** checks; wrap with `wp_unslash()`, then sanitize (`sanitize_text_field`, `sanitize_key`, `absint`, `esc_url_raw` as appropriate). |
| 4 | **Output redirects**: use `wp_safe_redirect()` + `wp_validate_redirect()` + **`esc_url_raw()`** on user-supplied referer before validation. |
| 5 | **AJAX**: use `wp_send_json_error()` / `wp_send_json_success()` — never bare `exit` without status. |

## Registered routes (audit target)

| Hook | Handler | Capability | Nonce / auth |
|------|---------|------------|--------------|
| `wp_ajax_dismiss_admin_notice` | `Dismiss_Notice::dismiss_admin_notice()` | `manage_options` | `dismissible-notice` (`check_ajax_referer`) |

_(WordPress.org / free build: LMFWC / license `admin_post_*` handlers were removed; the premium codebase may reintroduce them.)_

## Notes

- Dismiss AJAX nonce is localized in `Dismiss_Notice::enqueue_script()`; keep it aligned with **`check_ajax_referer`**.
