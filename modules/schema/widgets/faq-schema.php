<?php
/**
 * FAQ Schema widget — FAQPage JSON-LD + accessible FAQ markup.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Schema\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Schema\Schema_Builder;
use LandTechExtras\Schema\Schema_Validator;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.3.0
 */
class Faq_Schema extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ee-faq-schema';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'FAQ Schema', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-help-o';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'faq', 'schema', 'json-ld', 'seo', 'structured data' );
	}

	/**
	 * @inheritDoc
	 */
	protected static function ltxe_allows_element_html_cache(): bool {
		return false;
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_faq',
			array(
				'label' => __( 'FAQ Items', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'question',
			array(
				'label'       => __( 'Question', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'What is your return policy?', 'landtech-extras-for-elementor' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'answer',
			array(
				'label'       => __( 'Answer', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'We offer a 30-day return policy on all items.', 'landtech-extras-for-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'faq_items',
			array(
				'label'       => __( 'Questions', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'question' => __( 'What is your return policy?', 'landtech-extras-for-elementor' ),
						'answer'   => __( 'We offer a 30-day return policy on all items.', 'landtech-extras-for-elementor' ),
					),
				),
				'title_field' => '{{{ question }}}',
			)
		);

		$this->add_control(
			'output_json_ld',
			array(
				'label'        => __( 'Output FAQPage JSON-LD', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = isset( $settings['faq_items'] ) && is_array( $settings['faq_items'] ) ? $settings['faq_items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'ee-faq-schema' );

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$question = isset( $item['question'] ) ? (string) $item['question'] : '';
				$answer   = isset( $item['answer'] ) ? (string) $item['answer'] : '';
				if ( '' === $question ) {
					continue;
				}
				$q_id = 'ee-faq-schema-q-' . $this->get_id() . '-' . absint( $index );
				?>
				<div class="ee-faq-schema__item">
					<h3 class="ee-faq-schema__question" id="<?php echo esc_attr( $q_id ); ?>">
						<?php echo esc_html( $question ); ?>
					</h3>
					<div class="ee-faq-schema__answer" aria-labelledby="<?php echo esc_attr( $q_id ); ?>">
						<?php echo wp_kses_post( $answer ); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php

		if ( 'yes' !== ( $settings['output_json_ld'] ?? '' ) ) {
			return;
		}

		$schema = Schema_Builder::build_faq_page( $items );
		$result = Schema_Validator::validate( 'FAQPage', $schema );

		if ( ! empty( $result['valid'] ) ) {
			Schema_Builder::print_json_ld(
				$schema,
				'ee-faq-schema-jsonld-' . sanitize_html_class( (string) $this->get_id() )
			);
		}
	}
}
