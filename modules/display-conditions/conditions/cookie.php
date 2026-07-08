<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cookie display condition.
 *
 * @since 2.2.102
 */
class Cookie extends Var_Base {

	/**
	 * @inheritDoc
	 */
	public function get_group() {
		return 'visitor';
	}

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'cookie';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Cookie', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function check( $operator, $value, $name = null ) {
		$show = false;
		$key  = $this->sanitize_request_var_key( $name );

		if ( '' === $key || ! isset( $_COOKIE[ $key ] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Key sanitized; value sanitized below.
			return $this->compare( $show, true, $operator );
		}

		$incoming = sanitize_text_field( wp_unslash( $_COOKIE[ $key ] ) );
		$value_in = sanitize_text_field( (string) $value );

		if ( '' === trim( (string) $value ) ) {
			$show = true;
		} elseif ( $value_in === $incoming ) {
			$show = true;
		}

		return $this->compare( $show, true, $operator );
	}
}
