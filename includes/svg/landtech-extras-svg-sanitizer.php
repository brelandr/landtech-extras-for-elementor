<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize SVG markup before it is stored in the Media Library.
 *
 * @since 2.2.96
 */
class Landtech_Extras_Svg_Sanitizer {

	/**
	 * Tags that must never appear in uploaded SVG files.
	 *
	 * @var string[]
	 */
	private static $blocked_tags = array(
		'script',
		'iframe',
		'foreignobject',
		'embed',
		'object',
		'link',
		'audio',
		'video',
	);

	/**
	 * Attribute prefixes that must be removed (event handlers, xlink:href kept when safe).
	 *
	 * @var string[]
	 */
	private static $blocked_attribute_prefixes = array(
		'on',
	);

	/**
	 * Whether DOM extension requirements are available.
	 *
	 * @return bool
	 */
	public static function can_sanitize() {
		return class_exists( 'DOMDocument' );
	}

	/**
	 * Sanitize SVG file contents on disk.
	 *
	 * @param string $filepath Absolute path to the uploaded temp file.
	 * @return bool True when sanitized content was written; false on failure.
	 */
	public function sanitize_file( $filepath ) {
		if ( ! self::can_sanitize() || ! is_string( $filepath ) || '' === $filepath ) {
			return false;
		}

		$wp_filesystem = $this->get_wp_filesystem();

		if ( ! $wp_filesystem || ! $wp_filesystem->exists( $filepath ) ) {
			return false;
		}

		$content = $wp_filesystem->get_contents( $filepath );

		if ( false === $content || '' === $content ) {
			return false;
		}

		$sanitized = $this->sanitize( $content );

		if ( false === $sanitized || '' === $sanitized ) {
			return false;
		}

		return (bool) $wp_filesystem->put_contents( $filepath, $sanitized, FS_CHMOD_FILE );
	}

	/**
	 * Sanitize SVG markup string.
	 *
	 * @param string $content Raw SVG file contents.
	 * @return string|false Sanitized SVG markup or false on failure.
	 */
	public function sanitize( $content ) {
		if ( ! self::can_sanitize() || ! is_string( $content ) || '' === $content ) {
			return false;
		}

		$content = $this->strip_php_tags( $content );
		$content = preg_replace( '/<!--.*?-->/s', '', $content );

		$start = stripos( $content, '<svg' );
		$end   = strripos( $content, '</svg>' );

		if ( false === $start || false === $end ) {
			return false;
		}

		$content = substr( $content, $start, ( $end - $start + 6 ) );

		$php_version_under_eight = version_compare( PHP_VERSION, '8.0.0', '<' );
		if ( $php_version_under_eight && function_exists( 'libxml_disable_entity_loader' ) ) {
			libxml_disable_entity_loader( true ); // phpcs:ignore Generic.PHP.DeprecatedFunctions.Deprecated
		}

		$previous_errors = libxml_use_internal_errors( true );

		$dom = new DOMDocument(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		$dom->formatOutput           = false;
		$dom->preserveWhiteSpace     = false;
		$dom->strictErrorChecking    = false;

		if ( ! $dom->loadXML( $content, LIBXML_NONET | LIBXML_NOCDATA ) ) {
			libxml_use_internal_errors( $previous_errors );
			return false;
		}

		$this->sanitize_dom_node( $dom->documentElement );

		$sanitized = $dom->saveXML( $dom->documentElement, LIBXML_NOEMPTYTAG );

		libxml_use_internal_errors( $previous_errors );

		if ( $php_version_under_eight && function_exists( 'libxml_disable_entity_loader' ) ) {
			libxml_disable_entity_loader( false ); // phpcs:ignore Generic.PHP.DeprecatedFunctions.Deprecated
		}

		if ( ! is_string( $sanitized ) || '' === $sanitized ) {
			return false;
		}

		return $sanitized;
	}

	/**
	 * Recursively remove dangerous nodes and attributes.
	 *
	 * @param DOMNode|null $node Current DOM node.
	 * @return void
	 */
	private function sanitize_dom_node( $node ) {
		if ( ! $node instanceof DOMNode ) {
			return;
		}

		if ( XML_ELEMENT_NODE === $node->nodeType ) {
			$tag = strtolower( $node->nodeName );

			if ( in_array( $tag, self::$blocked_tags, true ) ) {
				if ( $node->parentNode ) {
					$node->parentNode->removeChild( $node );
				}
				return;
			}

			if ( $node->hasAttributes() ) {
				$remove = array();

				foreach ( $node->attributes as $attribute ) {
					if ( $this->should_remove_attribute( $attribute->nodeName, $attribute->nodeValue ) ) {
						$remove[] = $attribute->nodeName;
					}
				}

				foreach ( $remove as $attribute_name ) {
					$node->removeAttribute( $attribute_name );
				}
			}
		}

		if ( ! $node->hasChildNodes() ) {
			return;
		}

		$children = array();
		foreach ( $node->childNodes as $child ) {
			$children[] = $child;
		}

		foreach ( $children as $child ) {
			$this->sanitize_dom_node( $child );
		}
	}

	/**
	 * Decide whether an attribute should be stripped.
	 *
	 * @param string $name  Attribute name.
	 * @param string $value Attribute value.
	 * @return bool
	 */
	private function should_remove_attribute( $name, $value ) {
		$name_lower = strtolower( $name );

		foreach ( self::$blocked_attribute_prefixes as $prefix ) {
			if ( 0 === strpos( $name_lower, $prefix ) ) {
				return true;
			}
		}

		if ( in_array( $name_lower, array( 'href', 'xlink:href' ), true ) ) {
			return $this->is_unsafe_url_value( $value );
		}

		if ( $this->contains_script_payload( $value ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Detect javascript/data/base64 payloads in attribute values.
	 *
	 * @param string $value Attribute value.
	 * @return bool
	 */
	private function contains_script_payload( $value ) {
		if ( ! is_string( $value ) || '' === $value ) {
			return false;
		}

		return (bool) preg_match( '/(?:\w+script|data:|base64|javascript:|vbscript:)/i', $value );
	}

	/**
	 * Block external or script-like href values.
	 *
	 * @param string $value Attribute value.
	 * @return bool
	 */
	private function is_unsafe_url_value( $value ) {
		if ( ! is_string( $value ) || '' === $value ) {
			return false;
		}

		if ( $this->contains_script_payload( $value ) ) {
			return true;
		}

		$value = trim( $value );

		if ( 0 === strpos( $value, '#' ) ) {
			return false;
		}

		return (bool) preg_match( '/^(https?:|ftp:|file:|\/\/)/i', $value );
	}

	/**
	 * Remove PHP open/close tags from SVG content.
	 *
	 * @param string $content Raw SVG contents.
	 * @return string
	 */
	private function strip_php_tags( $content ) {
		$content = str_replace( array( '<?php', '?>' ), '', $content );
		return preg_replace( '/<\?(?:php|=)?[\s\S]*?\?>/i', '', $content );
	}

	/**
	 * @return WP_Filesystem_Base|false
	 */
	private function get_wp_filesystem() {
		global $wp_filesystem;

		if ( ! empty( $wp_filesystem ) ) {
			return $wp_filesystem;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';

		if ( ! WP_Filesystem() ) {
			return false;
		}

		return $wp_filesystem;
	}
}
