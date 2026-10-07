<?php
/**
 * Review nudge notice.
 *
 * Shows a dismissible admin notice after 7 days of active use,
 * asking the user to leave a review on WordPress.org.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Review_Nudge
 */
class Review_Nudge {

	const ACTIVATED_OPTION  = 'ltxe_activated_time';
	const DISMISSED_META    = 'ltxe_review_nudge_dismissed';
	const NONCE_ACTION      = 'ltxe_dismiss_review_nudge';
	const AJAX_ACTION       = 'ltxe_dismiss_review_nudge';
	const REVIEW_URL        = 'https://wordpress.org/support/plugin/landtech-extras-for-elementor/reviews/#new-post';
	const DAYS_BEFORE_NUDGE = 7;

	/**
	 * Boot hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_record_activated' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'admin_notices', array( __CLASS__, 'maybe_show' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( __CLASS__, 'handle_dismiss' ) );
	}

	/**
	 * Record activation timestamp (call from plugin activation hook).
	 *
	 * @return void
	 */
	public static function on_activate() {
		if ( ! get_option( self::ACTIVATED_OPTION ) ) {
			add_option( self::ACTIVATED_OPTION, time(), '', 'no' );
		}
	}

	/**
	 * Start the 7-day clock for sites that updated without a fresh activation.
	 *
	 * @return void
	 */
	public static function maybe_record_activated() {
		self::on_activate();
	}

	/**
	 * Whether the current user should see the notice on this screen.
	 *
	 * @return bool
	 */
	private static function should_show() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$user_id = get_current_user_id();

		if ( get_user_meta( $user_id, self::DISMISSED_META, true ) ) {
			return false;
		}

		$activated = (int) get_option( self::ACTIVATED_OPTION, 0 );
		if ( ! $activated || ( time() - $activated ) < ( self::DAYS_BEFORE_NUDGE * DAY_IN_SECONDS ) ) {
			return false;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return false;
		}

		$allowed_screens = array( 'dashboard', 'elementor_page_elementor', 'plugins' );
		if ( ! in_array( $screen->id, $allowed_screens, true ) && false === strpos( $screen->id, 'landtech' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Enqueue the dismiss script only when the notice will render.
	 *
	 * @return void
	 */
	public static function enqueue() {
		if ( ! self::should_show() ) {
			return;
		}

		$handle = 'landtech-extras-review-nudge';
		wp_enqueue_script(
			$handle,
			plugins_url( 'assets/js/review-nudge.js', LANDTECH_EXTRAS__FILE__ ),
			array(),
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_localize_script(
			$handle,
			'landtechExtrasReviewNudge',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => self::AJAX_ACTION,
				'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
			)
		);
	}

	/**
	 * Maybe render the nudge notice.
	 *
	 * @return void
	 */
	public static function maybe_show() {
		if ( ! self::should_show() ) {
			return;
		}

		?>
		<div class="notice notice-success is-dismissible ltxe-review-nudge">
			<p>
				<strong><?php esc_html_e( 'Enjoying LandTech Extras for Elementor?', 'landtech-extras-for-elementor' ); ?></strong>
				<?php esc_html_e( 'A quick review on WordPress.org helps other Elementor users find the plugin — and it means a lot to us.', 'landtech-extras-for-elementor' ); ?>
				&nbsp;
				<a href="<?php echo esc_url( self::REVIEW_URL ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Leave a review', 'landtech-extras-for-elementor' ); ?>
				</a>
				&nbsp;&nbsp;
				<a href="#" class="ltxe-dismiss-review-nudge" style="color:#888;font-size:0.9em;">
					<?php esc_html_e( 'I already did / No thanks', 'landtech-extras-for-elementor' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * AJAX handler: mark notice dismissed for current user.
	 *
	 * @return void
	 */
	public static function handle_dismiss() {
		check_ajax_referer( self::NONCE_ACTION );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( '', '', array( 'response' => 403 ) );
		}

		update_user_meta( get_current_user_id(), self::DISMISSED_META, '1' );
		wp_die();
	}
}
