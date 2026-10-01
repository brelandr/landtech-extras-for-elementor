<?php
/**
 * Media Gallery widget — photos and videos in one filterable grid.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\MediaGallery\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\MediaGallery\Media_Gallery_Urls;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __DIR__ ) . '/class-media-gallery-urls.php';

/**
 * @since 3.0.0
 */
class Media_Gallery extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-media-gallery';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Media Gallery', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'gallery', 'video', 'photo', 'lightbox', 'youtube', 'vimeo' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-media-gallery', 'landtech-extras-glightbox' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-media-gallery' );
	}

	/**
	 * Mixed remote media is not safe to cache as static HTML.
	 *
	 * @return bool
	 */
	protected static function ltxe_allows_element_html_cache(): bool {
		return false;
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'Items', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'media_type',
			array(
				'label'   => __( 'Type', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'photo',
				'options' => array(
					'photo' => __( 'Photo', 'landtech-extras-for-elementor' ),
					'video' => __( 'Video', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$repeater->add_control(
			'image',
			array(
				'label'     => __( 'Photo', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'media_type' => 'photo' ),
			)
		);
		$repeater->add_control(
			'video_source',
			array(
				'label'     => __( 'Video source', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'youtube',
				'options'   => array(
					'youtube' => __( 'YouTube URL', 'landtech-extras-for-elementor' ),
					'vimeo'   => __( 'Vimeo URL', 'landtech-extras-for-elementor' ),
					'file'    => __( 'Self-hosted MP4', 'landtech-extras-for-elementor' ),
				),
				'condition' => array( 'media_type' => 'video' ),
			)
		);
		$repeater->add_control(
			'video_url',
			array(
				'label'       => __( 'Video URL', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://www.youtube.com/watch?v=',
				'condition'   => array(
					'media_type'   => 'video',
					'video_source' => array( 'youtube', 'vimeo' ),
				),
			)
		);
		$repeater->add_control(
			'video_file',
			array(
				'label'      => __( 'MP4 file', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'media_type' => 'video',
				'condition'  => array(
					'media_type'   => 'video',
					'video_source' => 'file',
				),
			)
		);
		$repeater->add_control(
			'title',
			array(
				'label' => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'description',
			array(
				'label' => __( 'Description', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'tags',
			array(
				'label'       => __( 'Tags', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Comma-separated. Used by the filter bar.', 'landtech-extras-for-elementor' ),
			)
		);
		$repeater->add_control(
			'duration',
			array(
				'label'     => __( 'Duration', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array( 'media_type' => 'video' ),
			)
		);
		$repeater->add_control(
			'thumb',
			array(
				'label' => __( 'Custom thumbnail', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
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
			'layout',
			array(
				'label'   => __( 'Layout', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'      => __( 'Grid', 'landtech-extras-for-elementor' ),
					'masonry'   => __( 'Masonry', 'landtech-extras-for-elementor' ),
					'justified' => __( 'Justified', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_responsive_control(
			'columns',
			array(
				'label'     => __( 'Columns', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .ltxe-mg' => '--ltxe-mg-cols: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'default'    => array(
					'size' => 12,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-mg' => '--ltxe-mg-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'aspect_ratio',
			array(
				'label'       => __( 'Aspect ratio', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '4-3',
				'description' => __( 'Grid and Justified only. Masonry keeps each image at its own height.', 'landtech-extras-for-elementor' ),
				'options'     => array(
					'1-1'  => __( '1:1', 'landtech-extras-for-elementor' ),
					'4-3'  => __( '4:3', 'landtech-extras-for-elementor' ),
					'16-9' => __( '16:9', 'landtech-extras-for-elementor' ),
					'auto' => __( 'Auto', 'landtech-extras-for-elementor' ),
				),
				'condition'   => array(
					'layout!' => 'masonry',
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter',
			array(
				'label' => __( 'Filtering', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'show_filter',
			array(
				'label'        => __( 'Filter bar', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'all_label',
			array(
				'label'     => __( 'All label', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All', 'landtech-extras-for-elementor' ),
				'condition' => array( 'show_filter' => 'yes' ),
			)
		);
		$this->add_control(
			'filter_by',
			array(
				'label'     => __( 'Filter by', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'both',
				'options'   => array(
					'tags' => __( 'Tags', 'landtech-extras-for-elementor' ),
					'type' => __( 'Photos and videos', 'landtech-extras-for-elementor' ),
					'both' => __( 'Both', 'landtech-extras-for-elementor' ),
				),
				'condition' => array( 'show_filter' => 'yes' ),
			)
		);
		$this->add_control(
			'filter_align',
			array(
				'label'     => __( 'Filter alignment', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'   => __( 'Left', 'landtech-extras-for-elementor' ),
					'center' => __( 'Center', 'landtech-extras-for-elementor' ),
					'right'  => __( 'Right', 'landtech-extras-for-elementor' ),
				),
				'condition' => array( 'show_filter' => 'yes' ),
			)
		);
		$this->add_control(
			'filter_style',
			array(
				'label'     => __( 'Active filter style', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'pill',
				'options'   => array(
					'pill'      => __( 'Background pill', 'landtech-extras-for-elementor' ),
					'underline' => __( 'Underline', 'landtech-extras-for-elementor' ),
					'bold'      => __( 'Bold', 'landtech-extras-for-elementor' ),
				),
				'condition' => array( 'show_filter' => 'yes' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox',
			array(
				'label' => __( 'Lightbox', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'lightbox',
			array(
				'label'        => __( 'Lightbox', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'show_play',
			array(
				'label'        => __( 'Video play button', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'show_duration',
			array(
				'label'        => __( 'Duration badge', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'lazy',
			array(
				'label'        => __( 'Lazy load', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'hover',
			array(
				'label'   => __( 'Image hover', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none' => __( 'None', 'landtech-extras-for-elementor' ),
					'zoom' => __( 'Zoom', 'landtech-extras-for-elementor' ),
					'fade' => __( 'Fade overlay', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_control(
			'lb_theme',
			array(
				'label'     => __( 'Lightbox theme', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'dark',
				'options'   => array(
					'dark'  => __( 'Dark', 'landtech-extras-for-elementor' ),
					'light' => __( 'Light', 'landtech-extras-for-elementor' ),
				),
				'condition' => array( 'lightbox' => 'yes' ),
			)
		);
		$this->add_control(
			'lb_loop',
			array(
				'label'        => __( 'Loop', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'lightbox' => 'yes' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = isset( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		$items    = apply_filters( 'landtech_extras/media_gallery_items', $items, $settings );
		if ( ! is_array( $items ) ) {
			$items = array();
		}
		$layout   = isset( $settings['layout'] ) ? sanitize_key( (string) $settings['layout'] ) : 'grid';
		if ( ! in_array( $layout, array( 'grid', 'masonry', 'justified' ), true ) ) {
			$layout = 'grid';
		}
		$ratio = isset( $settings['aspect_ratio'] ) ? sanitize_key( (string) $settings['aspect_ratio'] ) : '4-3';
		if ( 'masonry' === $layout ) {
			$ratio = 'auto';
		}
		$align  = isset( $settings['filter_align'] ) ? sanitize_key( (string) $settings['filter_align'] ) : 'left';
		$fstyle = isset( $settings['filter_style'] ) ? sanitize_key( (string) $settings['filter_style'] ) : 'pill';
		$hover  = isset( $settings['hover'] ) ? sanitize_key( (string) $settings['hover'] ) : 'none';
		$theme  = isset( $settings['lb_theme'] ) ? sanitize_key( (string) $settings['lb_theme'] ) : 'dark';
		$gallery = 'ltxe-mg-' . $this->get_id();
		$filters = $this->filter_buttons( $settings, $items );
		$classes = 'ltxe-mg ltxe-mg--' . $layout . ' ltxe-mg--align-' . $align . ' ltxe-mg--active-' . $fstyle . ' ltxe-mg--hover-' . $hover . ' ltxe-mg--ratio-' . $ratio;
		$classes = apply_filters( 'landtech_extras/media_gallery_root_classes', $classes, $settings );
		$root_attrs = apply_filters( 'landtech_extras/media_gallery_root_attrs', array(), $settings );

		echo '<div class="' . esc_attr( $classes ) . '" data-layout="' . esc_attr( $layout ) . '" data-gallery="' . esc_attr( $gallery ) . '" data-lightbox="' . ( 'yes' === ( $settings['lightbox'] ?? '' ) ? '1' : '0' ) . '" data-lazy="' . ( 'yes' === ( $settings['lazy'] ?? '' ) ? '1' : '0' ) . '" data-loop="' . ( 'yes' === ( $settings['lb_loop'] ?? '' ) ? '1' : '0' ) . '" data-theme="' . esc_attr( $theme ) . '"';
		self::echo_data_attrs( $root_attrs );
		echo '>';
		if ( ! empty( $filters ) ) {
			echo '<div class="ltxe-mg__filters" role="toolbar">';
			foreach ( $filters as $filter ) {
				echo '<button type="button" class="ltxe-mg__filter' . ( ! empty( $filter['active'] ) ? ' is-active' : '' ) . '" data-filter="' . esc_attr( $filter['filter'] ) . '" aria-pressed="' . ( ! empty( $filter['active'] ) ? 'true' : 'false' ) . '">' . esc_html( $filter['label'] ) . '</button>';
			}
			echo '</div>';
		}
		echo '<div class="ltxe-mg__grid">';
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$this->render_item( $item, $gallery, $settings );
		}
		echo '</div></div>';
	}

	/**
	 * Print escaped data-* attributes.
	 *
	 * @param mixed $attrs Attribute map without the data- prefix.
	 * @return void
	 */
	private static function echo_data_attrs( $attrs ) {
		if ( ! is_array( $attrs ) ) {
			return;
		}
		foreach ( $attrs as $key => $value ) {
			$name = sanitize_key( (string) $key );
			if ( '' === $name || ! is_scalar( $value ) ) {
				continue;
			}
			echo ' data-' . esc_attr( $name ) . '="' . esc_attr( (string) $value ) . '"';
		}
	}

	/**
	 * Filter buttons for tags and media type.
	 *
	 * @param array<string, mixed>        $settings Widget settings.
	 * @param array<int, mixed>           $items    Repeater rows.
	 * @return array<int, array<string, string>>
	 */
	private function filter_buttons( $settings, $items ) {
		if ( 'yes' !== ( $settings['show_filter'] ?? '' ) ) {
			return array();
		}
		$mode  = isset( $settings['filter_by'] ) ? sanitize_key( (string) $settings['filter_by'] ) : 'both';
		$all   = isset( $settings['all_label'] ) ? (string) $settings['all_label'] : __( 'All', 'landtech-extras-for-elementor' );
		$out   = array(
			array(
				'label'  => $all,
				'filter' => '*',
				'active' => '1',
			),
		);
		if ( 'tags' !== $mode ) {
			$out[] = array(
				'label'  => __( 'Photos', 'landtech-extras-for-elementor' ),
				'filter' => '.type-photo',
				'active' => '',
			);
			$out[] = array(
				'label'  => __( 'Videos', 'landtech-extras-for-elementor' ),
				'filter' => '.type-video',
				'active' => '',
			);
		}
		if ( 'type' !== $mode ) {
			$seen = array();
			foreach ( $items as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}
				foreach ( $this->tag_slugs( isset( $item['tags'] ) ? (string) $item['tags'] : '' ) as $slug => $label ) {
					if ( isset( $seen[ $slug ] ) ) {
						continue;
					}
					$seen[ $slug ] = true;
					$out[]         = array(
						'label'  => $label,
						'filter' => '.tag-' . $slug,
						'active' => '',
					);
				}
			}
		}
		return $out;
	}

	/**
	 * @param string $raw Comma-separated tags.
	 * @return array<string, string> slug => label
	 */
	private function tag_slugs( $raw ) {
		$out = array();
		foreach ( explode( ',', $raw ) as $part ) {
			$label = trim( $part );
			$slug  = sanitize_title( $label );
			if ( '' === $slug ) {
				continue;
			}
			$out[ $slug ] = $label;
		}
		return $out;
	}

	/**
	 * @param array<string, mixed> $item     Repeater row.
	 * @param string               $gallery  Lightbox group.
	 * @param array<string, mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_item( $item, $gallery, $settings ) {
		$type = ( isset( $item['media_type'] ) && 'video' === $item['media_type'] ) ? 'video' : 'photo';
		$tags = $this->tag_slugs( isset( $item['tags'] ) ? (string) $item['tags'] : '' );
		$classes = 'ltxe-mg__item type-' . $type;
		foreach ( array_keys( $tags ) as $slug ) {
			$classes .= ' tag-' . $slug;
		}
		$href  = $this->item_href( $item, $type );
		$thumb = $this->item_thumb( $item, $type );
		$title = isset( $item['title'] ) ? (string) $item['title'] : '';
		$desc  = isset( $item['description'] ) ? (string) $item['description'] : '';
		if ( '' === $href ) {
			return;
		}

		$item_attrs = apply_filters( 'landtech_extras/media_gallery_item_attrs', array(), $item, $settings );
		echo '<div class="' . esc_attr( $classes ) . '"';
		self::echo_data_attrs( $item_attrs );
		echo '>';
		echo '<a class="ltxe-mg__link glightbox" href="' . esc_url( $href ) . '" data-gallery="' . esc_attr( $gallery ) . '" data-type="' . esc_attr( 'video' === $type ? 'video' : 'image' ) . '"';
		if ( '' !== $title ) {
			echo ' data-title="' . esc_attr( $title ) . '"';
		}
		if ( '' !== $desc ) {
			echo ' data-description="' . esc_attr( $desc ) . '"';
		}
		echo '>';
		if ( '' !== $thumb ) {
			$lazy = 'yes' === ( $settings['lazy'] ?? '' );
			if ( $lazy ) {
				echo '<img class="ltxe-mg__img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $title ) . '" />';
			} else {
				echo '<img class="ltxe-mg__img" src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $title ) . '" />';
			}
		} else {
			echo '<span class="ltxe-mg__poster" role="img" aria-label="' . esc_attr( $title ) . '"></span>';
		}
		if ( 'video' === $type && 'yes' === ( $settings['show_play'] ?? '' ) ) {
			echo '<span class="ltxe-mg__play" aria-hidden="true"></span>';
		}
		if ( 'video' === $type && 'yes' === ( $settings['show_duration'] ?? '' ) && ! empty( $item['duration'] ) ) {
			echo '<span class="ltxe-mg__duration">' . esc_html( (string) $item['duration'] ) . '</span>';
		}
		echo '</a></div>';
	}

	/**
	 * @param array<string, mixed> $item Repeater row.
	 * @param string               $type photo|video.
	 * @return string
	 */
	private function item_href( $item, $type ) {
		if ( 'photo' === $type ) {
			return $this->media_url( isset( $item['image'] ) ? $item['image'] : array() );
		}
		$source = isset( $item['video_source'] ) ? sanitize_key( (string) $item['video_source'] ) : 'youtube';
		if ( 'file' === $source ) {
			return $this->media_url( isset( $item['video_file'] ) ? $item['video_file'] : array() );
		}
		$url = '';
		if ( isset( $item['video_url']['url'] ) ) {
			$url = (string) $item['video_url']['url'];
		}
		return esc_url_raw( $url );
	}

	/**
	 * @param array<string, mixed> $item Repeater row.
	 * @param string               $type photo|video.
	 * @return string
	 */
	private function item_thumb( $item, $type ) {
		$custom = $this->media_url( isset( $item['thumb'] ) ? $item['thumb'] : array() );
		if ( '' !== $custom ) {
			return $custom;
		}
		if ( 'photo' === $type ) {
			return $this->media_url( isset( $item['image'] ) ? $item['image'] : array() );
		}
		$source = isset( $item['video_source'] ) ? sanitize_key( (string) $item['video_source'] ) : 'youtube';
		$url    = isset( $item['video_url']['url'] ) ? (string) $item['video_url']['url'] : '';
		if ( 'youtube' === $source ) {
			return Media_Gallery_Urls::youtube_thumb( $url );
		}
		if ( 'vimeo' === $source ) {
			return $this->vimeo_thumb( $url );
		}
		return '';
	}

	/**
	 * @param mixed $media Elementor media control value.
	 * @return string
	 */
	private function media_url( $media ) {
		if ( ! is_array( $media ) || empty( $media['url'] ) || ! is_string( $media['url'] ) ) {
			return '';
		}
		return esc_url_raw( $media['url'] );
	}

	/**
	 * Vimeo poster via oEmbed, cached for 7 days.
	 *
	 * @since 3.0.0
	 * @param string $url Vimeo URL.
	 * @return string
	 */
	private function vimeo_thumb( $url ) {
		$id = Media_Gallery_Urls::vimeo_id( $url );
		if ( '' === $id ) {
			return '';
		}
		$key    = 'ltxe_vimeo_thumb_' . $id;
		$cached = get_transient( $key );
		if ( is_string( $cached ) ) {
			return $cached;
		}
		$response = wp_remote_get(
			'https://vimeo.com/api/oembed.json?url=' . rawurlencode( 'https://vimeo.com/' . $id ),
			array(
				'timeout' => 5,
			)
		);
		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return '';
		}
		$data  = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		$thumb = ( is_array( $data ) && ! empty( $data['thumbnail_url'] ) ) ? esc_url_raw( (string) $data['thumbnail_url'] ) : '';
		if ( '' !== $thumb ) {
			set_transient( $key, $thumb, 7 * DAY_IN_SECONDS );
		}
		return $thumb;
	}
}
