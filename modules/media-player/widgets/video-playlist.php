<?php
namespace LandTechExtras\Modules\MediaPlayer\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * YouTube / Vimeo / MP4 playlist.
 *
 * @since 2.8.0
 */
class Video_Playlist extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-video-playlist';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Video Playlist', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-video-playlist';
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-video-playlist' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-video-playlist' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_videos',
			array(
				'label' => __( 'Videos', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Video', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => array(
					'youtube' => __( 'YouTube', 'landtech-extras-for-elementor' ),
					'vimeo'   => __( 'Vimeo', 'landtech-extras-for-elementor' ),
					'mp4'     => __( 'Self-hosted MP4', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$repeater->add_control(
			'url',
			array(
				'label' => __( 'Video URL', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$repeater->add_control(
			'thumbnail',
			array(
				'label'   => __( 'Thumbnail', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'duration',
			array(
				'label'   => __( 'Duration', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '3:42',
			)
		);

		$this->add_control(
			'videos',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title' => __( 'Sample video', 'landtech-extras-for-elementor' ),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'player_position',
			array(
				'label'   => __( 'Player Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'   => __( 'Top', 'landtech-extras-for-elementor' ),
					'left'  => __( 'Left', 'landtech-extras-for-elementor' ),
					'right' => __( 'Right', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'auto_advance',
			array(
				'label'        => __( 'Auto-advance', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_counter',
			array(
				'label'        => __( 'Show Counter', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => __( 'Active Item Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-vp__item.is-active' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typo',
				'selector' => '{{WRAPPER}} .ltxe-vp__item-title',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$videos   = isset( $settings['videos'] ) && is_array( $settings['videos'] ) ? $settings['videos'] : array();
		if ( empty( $videos ) ) {
			return;
		}

		$items = array();
		foreach ( $videos as $row ) {
			$url = ! empty( $row['url']['url'] ) ? esc_url_raw( $row['url']['url'] ) : '';
			if ( '' === $url ) {
				continue;
			}
			$items[] = array(
				'title'     => isset( $row['title'] ) ? (string) $row['title'] : '',
				'type'      => isset( $row['source'] ) ? sanitize_key( $row['source'] ) : 'youtube',
				'url'       => $url,
				'thumb'     => ! empty( $row['thumbnail']['url'] ) ? esc_url_raw( $row['thumbnail']['url'] ) : '',
				'duration'  => isset( $row['duration'] ) ? (string) $row['duration'] : '',
			);
		}

		if ( empty( $items ) ) {
			return;
		}

		$json = wp_json_encode(
			array(
				'items'         => $items,
				'auto_advance'  => ( isset( $settings['auto_advance'] ) && 'yes' === $settings['auto_advance'] ),
			)
		);
		if ( ! is_string( $json ) ) {
			$json = '{}';
		}

		$pos = isset( $settings['player_position'] ) ? sanitize_key( $settings['player_position'] ) : 'top';
		if ( ! in_array( $pos, array( 'top', 'left', 'right' ), true ) ) {
			$pos = 'top';
		}

		echo '<div class="ltxe-vp ltxe-vp--' . esc_attr( $pos ) . '" data-ltxe-playlist="' . esc_attr( $json ) . '">';
		echo '<div class="ltxe-vp__player" role="region" aria-label="' . esc_attr__( 'Video player', 'landtech-extras-for-elementor' ) . '"></div>';
		echo '<div class="ltxe-vp__list">';
		if ( isset( $settings['show_counter'] ) && 'yes' === $settings['show_counter'] ) {
			echo '<div class="ltxe-vp__counter">1 / ' . esc_html( (string) count( $items ) ) . '</div>';
		}
		foreach ( $items as $index => $item ) {
			$active = 0 === $index ? ' is-active' : '';
			echo '<button type="button" class="ltxe-vp__item' . esc_attr( $active ) . '" data-index="' . esc_attr( (string) $index ) . '">';
			if ( '' !== $item['thumb'] ) {
				echo '<img class="ltxe-vp__thumb" src="' . esc_url( $item['thumb'] ) . '" alt="">';
			}
			echo '<span class="ltxe-vp__item-title">' . esc_html( $item['title'] ) . '</span>';
			if ( '' !== $item['duration'] ) {
				echo '<span class="ltxe-vp__duration">' . esc_html( $item['duration'] ) . '</span>';
			}
			echo '</button>';
		}
		echo '</div></div>';
	}
}
