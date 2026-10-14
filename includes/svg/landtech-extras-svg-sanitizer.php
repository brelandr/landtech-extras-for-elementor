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
		'style',
	);

	/**
	 * SVG namespace. Elements in any other namespace are removed.
	 *
	 * @var string
	 */
	const SVG_NAMESPACE = 'http://www.w3.org/2000/svg';

	/**
	 * XLink namespace. Only the href attribute is kept, and only for fragments.
	 *
	 * @var string
	 */
	const XLINK_NAMESPACE = 'http://www.w3.org/1999/xlink';

	/**
	 * XML namespace (xml:space, xml:lang).
	 *
	 * @var string
	 */
	const XML_NAMESPACE = 'http://www.w3.org/XML/1998/namespace';

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
			$tag = strtolower( $this->node_local_name( $node ) );

			if ( in_array( $tag, self::$blocked_tags, true ) || $this->element_namespace_is_disallowed( $node ) ) {
				if ( $node->parentNode ) {
					$node->parentNode->removeChild( $node );
				}
				return;
			}

			if ( $node->hasAttributes() ) {
				$remove = array();

				foreach ( $node->attributes as $attribute ) {
					if ( $this->should_remove_attribute_node( $attribute ) ) {
						$remove[] = $attribute;
					}
				}

				foreach ( $remove as $attribute ) {
					$this->remove_attribute_node( $node, $attribute );
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
	 * Local name without a namespace prefix.
	 *
	 * nodeName for a prefixed tag is "svg:script", which misses the "script" blocklist.
	 *
	 * @param DOMNode $node Element or attribute.
	 * @return string
	 */
	private function node_local_name( $node ) {
		if ( isset( $node->localName ) && is_string( $node->localName ) && '' !== $node->localName ) {
			return $node->localName;
		}

		$name  = isset( $node->nodeName ) ? (string) $node->nodeName : '';
		$colon = strrpos( $name, ':' );

		if ( false !== $colon ) {
			return substr( $name, $colon + 1 );
		}

		return $name;
	}

	/**
	 * Drop elements that are not in the SVG namespace.
	 *
	 * An empty namespace is kept so legacy SVG without xmlns still sanitizes.
	 *
	 * @param DOMNode $node Element.
	 * @return bool
	 */
	private function element_namespace_is_disallowed( $node ) {
		$namespace = isset( $node->namespaceURI ) ? (string) $node->namespaceURI : '';

		if ( '' === $namespace ) {
			return false;
		}

		return self::SVG_NAMESPACE !== $namespace;
	}

	/**
	 * Whether a parsed attribute should be stripped.
	 *
	 * XLink is limited to href. Any other foreign namespace is removed.
	 *
	 * @param DOMAttr $attribute Attribute node.
	 * @return bool
	 */
	private function should_remove_attribute_node( $attribute ) {
		$namespace = isset( $attribute->namespaceURI ) ? (string) $attribute->namespaceURI : '';
		$local     = strtolower( $this->node_local_name( $attribute ) );

		if ( self::XLINK_NAMESPACE === $namespace ) {
			if ( 'href' !== $local ) {
				return true;
			}

			return $this->is_unsafe_url_value( (string) $attribute->nodeValue );
		}

		if ( '' !== $namespace && self::SVG_NAMESPACE !== $namespace && self::XML_NAMESPACE !== $namespace ) {
			return true;
		}

		return $this->should_remove_attribute( $attribute->nodeName, (string) $attribute->nodeValue );
	}

	/**
	 * Remove an attribute, including namespaced ones.
	 *
	 * @param DOMElement $node      Element that owns the attribute.
	 * @param DOMAttr    $attribute Attribute to remove.
	 * @return void
	 */
	private function remove_attribute_node( $node, $attribute ) {
		$namespace = isset( $attribute->namespaceURI ) ? (string) $attribute->namespaceURI : '';

		if ( '' !== $namespace ) {
			$node->removeAttributeNS( $namespace, $this->node_local_name( $attribute ) );
			return;
		}

		$node->removeAttribute( $attribute->nodeName );
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
		$local      = $name_lower;
		$colon      = strrpos( $local, ':' );

		if ( false !== $colon ) {
			$local = substr( $local, $colon + 1 );
		}

		foreach ( self::$blocked_attribute_prefixes as $prefix ) {
			if ( 0 === strpos( $local, $prefix ) ) {
				return true;
			}
		}

		if ( 'href' === $local || in_array( $name_lower, array( 'href', 'xlink:href' ), true ) ) {
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
