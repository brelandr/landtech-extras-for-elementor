<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Os
 *
 * @since  2.2.0
 */
class Os extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'visitor';
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'os';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Operating System', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_value_control() {
		return [
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'iphone',
			'label_block' 	=> true,
			'options' 		=> [
				'iphone'     => __( 'iPhone', 'landtech-extras-for-elementor' ),
				'windows'    => __( 'Windows', 'landtech-extras-for-elementor' ),
				'open_bsd'   => __( 'OpenBSD', 'landtech-extras-for-elementor' ),
				'sun_os'     => __( 'SunOS', 'landtech-extras-for-elementor' ),
				'linux'      => __( 'Linux', 'landtech-extras-for-elementor' ),
				'safari'     => __( 'Safari', 'landtech-extras-for-elementor' ),
				'mac_os'     => __( 'Mac OS', 'landtech-extras-for-elementor' ),
				'qnx'        => __( 'QNX', 'landtech-extras-for-elementor' ),
				'beos'       => __( 'BeOS', 'landtech-extras-for-elementor' ),
				'os2'        => __( 'OS/2', 'landtech-extras-for-elementor' ),
				'search_bot' => __( 'Search Bot', 'landtech-extras-for-elementor' ),
			],
		];
	}

	/**
	 * Check condition
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 *
	 * @param string  	$name  		The control name to check
	 * @param string 	$operator  	Comparison operator
	 * @param mixed  	$value  	The control value to check
	 */
	public function check( $operator, $value, $name = null ) {
		$oses = [
			'iphone'            => '(iPhone)',
			'windows' 			=> 'Win16|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|Windows ME',
			'open_bsd'          => 'OpenBSD',
			'sun_os'            => 'SunOS',
			'linux'             => '(Linux)|(X11)',
			'safari'            => '(Safari)',
			'mac_os'            => '(Mac_PowerPC)|(Macintosh)',
			'qnx'               => 'QNX',
			'beos'              => 'BeOS',
			'os2'              	=> 'OS/2',
			'search_bot'        => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
		];

		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		if ( '' === $user_agent || ! isset( $oses[ $value ] ) ) {
			return $this->compare( false, true, $operator );
		}

		return $this->compare( (bool) preg_match( '@' . $oses[ $value ] . '@', $user_agent ), true, $operator );
	}
}
