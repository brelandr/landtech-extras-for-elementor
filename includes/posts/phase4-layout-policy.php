<?php
/**
 * Posts Extra — Phase 4 layout / Isotope policy (shared hooks for org + Premium migration).
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Elementor has hydrated `Controls_Stack::$data['settings']` as an array.
 *
 * Calling `get_settings()` before that runs triggers PHP 8+ warnings (`$this->data` null / `settings` null)
 * inside `Controls_Stack::get_data()`, which are not converted to `Throwable` and bypass try/catch.
 *
 * @since 2.2.70
 *
 * @param \Elementor\Controls_Stack $stack Widget or other controls stack.
 * @return bool
 */
function landtech_extras_posts_extra_controls_stack_has_hydrated_settings( $stack ) {
	if ( ! $stack instanceof \Elementor\Controls_Stack ) {
		return false;
	}

	try {
		$ref = new \ReflectionClass( $stack );
		if ( ! $ref->hasProperty( 'data' ) ) {
			return false;
		}

		$prop = $ref->getProperty( 'data' );
		$prop->setAccessible( true );
		$data = $prop->getValue( $stack );

		if ( ! is_array( $data ) ) {
			return false;
		}

		return isset( $data['settings'] ) && is_array( $data['settings'] );
	} catch ( \Throwable $e ) {
		return false;
	}
}

/**
 * Read a control value when the widget may not be hydrated yet (e.g. `get_script_depends()` during preview bootstrap).
 *
 * Elementor may have null `settings` until render; `get_settings()` then triggers a fatal TypeError on strict `sanitize_settings( array )`.
 *
 * @since 2.2.70
 *
 * @param \Elementor\Widget_Base $widget Widget instance.
 * @param string                 $key    Setting key.
 * @return mixed|null Sanitized read or null on failure / empty key.
 */
function landtech_extras_posts_extra_widget_try_get_settings( $widget, $key ) {
	if ( ! $widget instanceof \Elementor\Widget_Base ) {
		return null;
	}

	if ( ! is_string( $key ) || '' === $key ) {
		return null;
	}

	if ( ! landtech_extras_posts_extra_controls_stack_has_hydrated_settings( $widget ) ) {
		return null;
	}

	try {
		return $widget->get_settings( $key );
	} catch ( \Throwable $e ) {
		return null;
	}
}

/**
 * Read parsed display settings when the widget may not be hydrated yet.
 *
 * @since 2.2.70
 *
 * @param \Elementor\Widget_Base $widget Widget instance.
 * @param string|null            $key    Optional single key; null = full settings array.
 * @return array|mixed Full settings array, single value, or empty array / null on failure.
 */
function landtech_extras_posts_extra_widget_try_get_settings_for_display( $widget, $key = null ) {
	if ( ! $widget instanceof \Elementor\Widget_Base ) {
		return null === $key ? array() : null;
	}

	if ( ! landtech_extras_posts_extra_controls_stack_has_hydrated_settings( $widget ) ) {
		return null === $key ? array() : null;
	}

	try {
		if ( null === $key ) {
			$s = $widget->get_settings_for_display();
			return is_array( $s ) ? $s : array();
		}

		return $widget->get_settings_for_display( $key );
	} catch ( \Throwable $e ) {
		return null === $key ? array() : null;
	}
}

/**
 * Skin slug for a Posts Extra widget instance (defaults to classic).
 *
 * @since 2.2.55
 *
 * @param \Elementor\Widget_Base $widget Widget instance.
 * @return string
 */
function landtech_extras_posts_extra_widget_get_skin_slug( $widget ) {
	if ( ! $widget instanceof \Elementor\Widget_Base ) {
		return 'classic';
	}

	$s = landtech_extras_posts_extra_widget_try_get_settings_for_display( $widget );
	if ( isset( $s['_skin'] ) && '' !== $s['_skin'] ) {
		return sanitize_key( (string) $s['_skin'] );
	}

	return 'classic';
}

/**
 * Setting key for the skin's grid layout control ({skin}_layout), empty when Isotope is never used for that skin.
 *
 * @since 2.2.55
 *
 * @param string $skin_slug Skin slug.
 * @return string Layout setting key or empty string.
 */
function landtech_extras_posts_extra_widget_layout_setting_key( $skin_slug ) {
	$skin_slug = sanitize_key( (string) $skin_slug );

	if ( '' === $skin_slug || 'carousel' === $skin_slug ) {
		return '';
	}

	return $skin_slug . '_layout';
}

/**
 * Maximum column count across responsive column controls (mirrors frontend need for multi-column layouts).
 *
 * @since 2.2.55
 *
 * @param \Elementor\Widget_Base $widget Widget instance.
 * @return int
 */
function landtech_extras_posts_extra_widget_max_column_count( $widget ) {
	if ( ! $widget instanceof \Elementor\Widget_Base ) {
		return 1;
	}

	$max = 1;
	foreach ( array( 'columns', 'columns_tablet', 'columns_mobile' ) as $key ) {
		$raw = landtech_extras_posts_extra_widget_try_get_settings( $widget, $key );
		if ( '' === $raw || null === $raw ) {
			continue;
		}
		$n = (int) $raw;
		if ( $n > $max ) {
			$max = $n;
		}
	}

	return $max;
}

/**
 * Grid layout mode from skin-prefixed layout control (e.g. classic_layout → masonry).
 *
 * @since 2.2.55
 *
 * @param \Elementor\Widget_Base $widget Widget instance.
 * @return string
 */
function landtech_extras_posts_extra_widget_get_grid_layout_mode( $widget ) {
	if ( ! $widget instanceof \Elementor\Widget_Base ) {
		return 'default';
	}

	$key = landtech_extras_posts_extra_widget_layout_setting_key(
		landtech_extras_posts_extra_widget_get_skin_slug( $widget )
	);

	if ( '' === $key ) {
		return 'default';
	}

	$mode = landtech_extras_posts_extra_widget_try_get_settings_for_display( $widget, $key );
	if ( ! is_string( $mode ) || '' === $mode ) {
		return 'default';
	}

	return $mode;
}

/**
 * Whether this Posts Extra instance would use the Isotope-based grid path, from hydrated widget settings only.
 *
 * Use this for render-time policy (e.g. masonry downgrade). For script dependency hints during Elementor bootstrap,
 * {@see landtech_extras_posts_extra_widget_needs_isotope_assets()} also considers saved document data when the
 * widget prototype has not loaded settings yet (avoids fatal errors and missing Isotope on preview).
 *
 * @since 2.2.55
 * @since 2.2.70 Renamed semantics: instance-only; document fallback moved to {@see landtech_extras_posts_extra_widget_needs_isotope_assets()}.
 *
 * @param \Elementor\Widget_Base $widget Widget instance.
 * @return bool
 */
function landtech_extras_posts_extra_widget_needs_isotope_assets_from_instance( $widget ) {
	if ( ! $widget instanceof \Elementor\Widget_Base ) {
		return false;
	}

	if ( 'posts-extra' !== $widget->get_name() ) {
		return false;
	}

	if ( landtech_extras_posts_extra_widget_max_column_count( $widget ) < 2 ) {
		return false;
	}

	$mode = landtech_extras_posts_extra_widget_get_grid_layout_mode( $widget );

	return 'default' !== $mode && '' !== $mode;
}

/**
 * Whether Posts Extra should load Isotope Packery-related scripts for this request (instance + saved document fallback).
 *
 * @since 2.2.55
 *
 * @param \Elementor\Widget_Base $widget Widget instance.
 * @return bool
 */
function landtech_extras_posts_extra_widget_needs_isotope_assets( $widget ) {
	if ( landtech_extras_posts_extra_widget_needs_isotope_assets_from_instance( $widget ) ) {
		return true;
	}

	$hint = landtech_extras_posts_extra_get_cached_document_isotope_hint();

	return ! empty( $hint['needs_isotope'] );
}

/**
 * Whether masonry may run under strict Phase-4 policy (filtered by Premium grandfather / feature flag).
 *
 * @since 2.2.55
 *
 * @param \Elementor\Widget_Base $widget Posts Extra widget.
 * @return bool
 */
function landtech_extras_posts_extra_phase4_masonry_runtime_permitted( $widget ) {
	$allowed = true;

	/**
	 * When {@see 'landtech_extras/posts/phase4_strict_isotope_opt_in'} is true, return false to block masonry
	 * for this widget (paired with on-render settings downgrade).
	 *
	 * @since 2.2.55
	 *
	 * @param bool                   $allowed Whether masonry may run.
	 * @param \Elementor\Widget_Base $widget  Posts Extra widget.
	 */
	return (bool) apply_filters( 'landtech_extras/posts/phase4_masonry_runtime_permitted', $allowed, $widget );
}

/**
 * Frontend script enqueue: load Isotope when the layout needs it and strict policy allows it.
 *
 * @since 2.2.55
 *
 * @param \Elementor\Widget_Base $widget Widget instance.
 * @return bool
 */
function landtech_extras_posts_extra_should_enqueue_frontend_isotope( $widget ) {
	if ( ! landtech_extras_posts_extra_widget_needs_isotope_assets( $widget ) ) {
		return false;
	}

	/**
	 * Opt in to deferred / conditional Isotope loading (Premium Phase 4). Default false preserves org-only behavior.
	 *
	 * @since 2.2.55
	 *
	 * @param bool $strict Whether strict conditional loading applies.
	 */
	if ( true !== apply_filters( 'landtech_extras/posts/phase4_strict_isotope_opt_in', false ) ) {
		return true;
	}

	return landtech_extras_posts_extra_phase4_masonry_runtime_permitted( $widget );
}

/**
 * Layout slugs that use the packaged Isotope engine (masonry layout mode, metro as fitRows, or Packery metro tiles).
 *
 * @since 2.2.58
 *
 * @param string $slug Layout control value.
 * @return bool
 */
function landtech_extras_posts_extra_isotope_packaged_layout_slug( $slug ) {
	$slug = sanitize_key( (string) $slug );

	return ( 'masonry' === $slug || 'metro' === $slug || 'packery' === $slug );
}

/**
 * Walk stored Elementor elements data for Posts Extra instances that need packaged Isotope layouts + multi-column.
 *
 * @since 2.2.70
 *
 * @param array $elements Elementor elements tree.
 * @return array {
 *     @type bool $needs_isotope Masonry, metro, or packery layout with more than one column.
 *     @type bool $needs_packery At least one matching widget uses packery layout mode.
 * }
 */
function landtech_extras_posts_extra_parse_document_isotope_needs_from_elements( $elements ) {
	$result = array(
		'needs_isotope' => false,
		'needs_packery' => false,
	);

	if ( ! is_array( $elements ) ) {
		return $result;
	}

	foreach ( $elements as $el ) {
		if ( ! is_array( $el ) ) {
			continue;
		}

		if ( ! empty( $el['widgetType'] ) && 'posts-extra' === $el['widgetType'] ) {
			$settings = isset( $el['settings'] ) && is_array( $el['settings'] ) ? $el['settings'] : array();
			$skin     = isset( $settings['_skin'] ) ? sanitize_key( (string) $settings['_skin'] ) : 'classic';
			if ( 'carousel' === $skin ) {
				continue;
			}
			$key = landtech_extras_posts_extra_widget_layout_setting_key( $skin );
			if ( '' === $key ) {
				continue;
			}
			$layout = isset( $settings[ $key ] ) ? (string) $settings[ $key ] : 'default';
			if ( ! landtech_extras_posts_extra_isotope_packaged_layout_slug( $layout ) ) {
				continue;
			}
			$max_cols = 1;
			foreach ( array( 'columns', 'columns_tablet', 'columns_mobile' ) as $ck ) {
				if ( isset( $settings[ $ck ] ) && '' !== $settings[ $ck ] ) {
					$max_cols = max( $max_cols, (int) $settings[ $ck ] );
				}
			}
			if ( $max_cols > 1 ) {
				$result['needs_isotope'] = true;
				if ( 'packery' === sanitize_key( $layout ) ) {
					$result['needs_packery'] = true;
				}
			}
		}

		if ( ! empty( $el['elements'] ) ) {
			$child = landtech_extras_posts_extra_parse_document_isotope_needs_from_elements( $el['elements'] );
			if ( ! empty( $child['needs_isotope'] ) ) {
				$result['needs_isotope'] = true;
			}
			if ( ! empty( $child['needs_packery'] ) ) {
				$result['needs_packery'] = true;
			}
		}
	}

	return $result;
}

/**
 * Resolve the post whose `_elementor_data` should drive frontend script dependency hints.
 *
 * @since 2.2.70
 *
 * @return int Post ID or 0.
 */
function landtech_extras_posts_extra_resolve_enqueue_context_post_id() {
	if ( ! class_exists( '\Elementor\Plugin', false ) || ! \Elementor\Plugin::$instance ) {
		return 0;
	}

	$queried = get_the_ID();
	if ( $queried > 0 ) {
		return (int) $queried;
	}

	$plugin = \Elementor\Plugin::$instance;

	if ( $plugin->editor && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode() && method_exists( $plugin->editor, 'get_post_id' ) ) {
		$ed_id = (int) $plugin->editor->get_post_id();
		if ( $ed_id > 0 ) {
			return $ed_id;
		}
	}

	if ( $plugin->preview && method_exists( $plugin->preview, 'get_post_id' ) ) {
		$pr_id = (int) $plugin->preview->get_post_id();
		if ( $pr_id > 0 ) {
			return $pr_id;
		}
	}

	return 0;
}

/**
 * Cached Isotope/Packery needs from saved document JSON (widget prototypes may lack hydrated settings during `get_script_depends()`).
 *
 * @since 2.2.70
 *
 * @return array {
 *     @type bool $needs_isotope Whether Isotope stack should enqueue.
 *     @type bool $needs_packery Whether Packery extension scripts should enqueue.
 * }
 */
function landtech_extras_posts_extra_get_cached_document_isotope_hint() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$cache = array(
		'needs_isotope' => false,
		'needs_packery' => false,
	);

	$post_id = landtech_extras_posts_extra_resolve_enqueue_context_post_id();
	if ( $post_id < 1 ) {
		return $cache;
	}

	$raw = get_post_meta( $post_id, '_elementor_data', true );
	if ( ! is_string( $raw ) || '' === $raw ) {
		return $cache;
	}

	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return $cache;
	}

	$cache = landtech_extras_posts_extra_parse_document_isotope_needs_from_elements( $data );

	return $cache;
}

/**
 * Whether the grid layout uses Isotope’s Packery engine (local `isotope-packery-mode` script).
 *
 * @since 2.2.60
 *
 * @param \Elementor\Widget_Base $widget Widget instance.
 * @return bool
 */
function landtech_extras_posts_extra_widget_uses_packery_layout( $widget ) {
	if ( ! $widget instanceof \Elementor\Widget_Base ) {
		return false;
	}

	if ( 'packery' === sanitize_key( landtech_extras_posts_extra_widget_get_grid_layout_mode( $widget ) ) ) {
		return true;
	}

	$hint = landtech_extras_posts_extra_get_cached_document_isotope_hint();

	return ! empty( $hint['needs_packery'] );
}

/**
 * Walk Elementor elements JSON for Posts Extra + Isotope layouts (masonry, metro, or packery) + multi-column (legacy / grandfather detection).
 *
 * @since 2.2.55
 *
 * @param array $elements Elementor elements tree.
 * @return bool
 */
function landtech_extras_posts_extra_elementor_data_contains_masonry_posts( $elements ) {
	$parsed = landtech_extras_posts_extra_parse_document_isotope_needs_from_elements( $elements );

	return ! empty( $parsed['needs_isotope'] );
}

/**
 * Downgrade disallowed masonry to default grid when strict mode blocks runtime (keeps markup/JS consistent).
 *
 * @since 2.2.55
 *
 * @param \Elementor\Widget_Base $element Widget instance.
 * @return void
 */
function landtech_extras_posts_extra_phase4_maybe_downgrade_masonry_layout( $element ) {
	if ( ! $element instanceof \Elementor\Widget_Base ) {
		return;
	}

	if ( 'posts-extra' !== $element->get_name() ) {
		return;
	}

	if ( ! landtech_extras_posts_extra_widget_needs_isotope_assets_from_instance( $element ) ) {
		return;
	}

	if ( true !== apply_filters( 'landtech_extras/posts/phase4_strict_isotope_opt_in', false ) ) {
		return;
	}

	if ( landtech_extras_posts_extra_phase4_masonry_runtime_permitted( $element ) ) {
		return;
	}

	$key = landtech_extras_posts_extra_widget_layout_setting_key(
		landtech_extras_posts_extra_widget_get_skin_slug( $element )
	);

	if ( '' === $key ) {
		return;
	}

	$element->set_settings( $key, 'default' );
}

add_action( 'elementor/widget/before_render_content', 'landtech_extras_posts_extra_phase4_maybe_downgrade_masonry_layout', 5, 1 );
