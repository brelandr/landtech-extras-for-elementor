<?php
namespace LandTechExtras\Modules\TeamMembers\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Team members grid.
 *
 * @since 2.9.0
 */
class Team_Members extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-team-members';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Team Members', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-person';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'team', 'staff', 'members', 'people' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-team-members' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-team-members' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_members',
			array(
				'label' => __( 'Members', 'landtech-extras-for-elementor' ),
			)
		);

		$social = new Repeater();
		$social->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fab fa-linkedin-in',
					'library' => 'fa-brands',
				),
			)
		);
		$social->add_control(
			'url',
			array(
				'label'       => __( 'URL', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);
		$social->add_control(
			'label',
			array(
				'label'       => __( 'Accessible Label', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'LinkedIn', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'photo',
			array(
				'label' => __( 'Photo', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'name',
			array(
				'label'   => __( 'Name', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);
		$repeater->add_control(
			'role',
			array(
				'label'   => __( 'Role / Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);
		$repeater->add_control(
			'bio',
			array(
				'label'   => __( 'Bio', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
			)
		);
		$repeater->add_control(
			'link',
			array(
				'label'       => __( 'Profile Link', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);
		$repeater->add_control(
			'socials',
			array(
				'label'       => __( 'Social Links', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $social->get_controls(),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->add_control(
			'members',
			array(
				'label'       => __( 'Team Members', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->get_default_members(),
				'title_field' => '{{{ name }}}',
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
			'card_style',
			array(
				'label'   => __( 'Card Style', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => array(
					'classic'    => __( 'Classic (photo above, info below)', 'landtech-extras-for-elementor' ),
					'overlay'    => __( 'Overlay (info on hover over photo)', 'landtech-extras-for-elementor' ),
					'flip'       => __( 'Flip Card (front = photo, back = bio + social)', 'landtech-extras-for-elementor' ),
					'horizontal' => __( 'Horizontal (photo left, info right)', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'landtech-extras-for-elementor' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors'      => array(
					'{{WRAPPER}} .ltxe-team' => '--ltxe-team-cols: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_shape',
			array(
				'label'   => __( 'Image Shape', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => array(
					'square'  => __( 'Square', 'landtech-extras-for-elementor' ),
					'rounded' => __( 'Rounded', 'landtech-extras-for-elementor' ),
					'circle'  => __( 'Circle', 'landtech-extras-for-elementor' ),
					'hexagon' => __( 'Hexagon', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'photo',
				'default' => 'medium',
			)
		);

		$this->add_control(
			'show_role',
			array(
				'label'        => __( 'Show Role', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_bio',
			array(
				'label'        => __( 'Show Bio', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_social',
			array(
				'label'        => __( 'Show Social Links', 'landtech-extras-for-elementor' ),
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
			'card_bg',
			array(
				'label'     => __( 'Card Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-team-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-team-card' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_gap',
			array(
				'label'      => __( 'Gap', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 64,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-team' => '--ltxe-team-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typo',
				'selector' => '{{WRAPPER}} .ltxe-team-card__name',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .ltxe-team-card',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return array<int,array<string,mixed>>
	 */
	private function get_default_members() {
		return array(
			array(
				'name' => __( 'Alex Rivera', 'landtech-extras-for-elementor' ),
				'role' => __( 'Creative director', 'landtech-extras-for-elementor' ),
				'bio'  => __( 'Leads brand systems and keeps every page on voice.', 'landtech-extras-for-elementor' ),
			),
			array(
				'name' => __( 'Sam Okonkwo', 'landtech-extras-for-elementor' ),
				'role' => __( 'Lead developer', 'landtech-extras-for-elementor' ),
				'bio'  => __( 'Builds the Elementor stack clients can actually maintain.', 'landtech-extras-for-elementor' ),
			),
			array(
				'name' => __( 'Riley Cho', 'landtech-extras-for-elementor' ),
				'role' => __( 'Producer', 'landtech-extras-for-elementor' ),
				'bio'  => __( 'Keeps launches on time and reviews in one place.', 'landtech-extras-for-elementor' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$members  = isset( $settings['members'] ) && is_array( $settings['members'] ) ? $settings['members'] : array();
		if ( empty( $members ) ) {
			echo '<p class="ltxe-team__empty">' . esc_html__( 'Add people to the Team Members widget.', 'landtech-extras-for-elementor' ) . '</p>';
			return;
		}

		$style = isset( $settings['card_style'] ) ? sanitize_key( (string) $settings['card_style'] ) : 'classic';
		if ( ! in_array( $style, array( 'classic', 'overlay', 'flip', 'horizontal' ), true ) ) {
			$style = 'classic';
		}
		$shape = isset( $settings['image_shape'] ) ? sanitize_key( (string) $settings['image_shape'] ) : 'rounded';
		if ( ! in_array( $shape, array( 'square', 'rounded', 'circle', 'hexagon' ), true ) ) {
			$shape = 'rounded';
		}

		$show_role   = isset( $settings['show_role'] ) && 'yes' === $settings['show_role'];
		$show_bio    = isset( $settings['show_bio'] ) && 'yes' === $settings['show_bio'];
		$show_social = isset( $settings['show_social'] ) && 'yes' === $settings['show_social'];

		echo '<div class="ltxe-team ltxe-team--' . esc_attr( $style ) . ' ltxe-team--shape-' . esc_attr( $shape ) . '" role="list">';
		foreach ( $members as $member ) {
			if ( ! is_array( $member ) ) {
				continue;
			}
			$this->render_member( $member, $settings, $style, $show_role, $show_bio, $show_social );
		}
		echo '</div>';
	}

	/**
	 * @param array<string,mixed> $member      Member row.
	 * @param array<string,mixed> $settings    Widget settings.
	 * @param string              $style       Card style.
	 * @param bool                $show_role   Role.
	 * @param bool                $show_bio    Bio.
	 * @param bool                $show_social Social.
	 * @return void
	 */
	private function render_member( $member, $settings, $style, $show_role, $show_bio, $show_social ) {
		$name = isset( $member['name'] ) ? sanitize_text_field( (string) $member['name'] ) : '';
		$role = isset( $member['role'] ) ? sanitize_text_field( (string) $member['role'] ) : '';
		$bio  = isset( $member['bio'] ) ? (string) $member['bio'] : '';
		$link = $this->get_link_url( isset( $member['link'] ) ? $member['link'] : array() );

		echo '<article class="ltxe-team-card" role="listitem">';
		if ( 'flip' === $style ) {
			echo '<div class="ltxe-team-card__inner">';
			echo '<div class="ltxe-team-card__face ltxe-team-card__front">';
			$this->render_photo( $member, $settings, $name );
			$this->render_name_block( $name, $role, $show_role, $link );
			echo '</div>';
			echo '<div class="ltxe-team-card__face ltxe-team-card__back">';
			$this->render_name_block( $name, $role, $show_role, $link );
			if ( $show_bio && '' !== $bio ) {
				echo '<p class="ltxe-team-card__bio">' . esc_html( $bio ) . '</p>';
			}
			if ( $show_social ) {
				$this->render_socials( isset( $member['socials'] ) ? $member['socials'] : array() );
			}
			echo '</div>';
			echo '</div>';
			echo '<button type="button" class="ltxe-team-card__flip-btn" data-show="' . esc_attr__( 'Show details', 'landtech-extras-for-elementor' ) . '" data-hide="' . esc_attr__( 'Hide details', 'landtech-extras-for-elementor' ) . '">' . esc_html__( 'Show details', 'landtech-extras-for-elementor' ) . '</button>';
		} else {
			echo '<div class="ltxe-team-card__media">';
			$this->render_photo( $member, $settings, $name );
			echo '</div>';
			echo '<div class="ltxe-team-card__body">';
			$this->render_name_block( $name, $role, $show_role, $link );
			if ( $show_bio && '' !== $bio ) {
				echo '<p class="ltxe-team-card__bio">' . esc_html( $bio ) . '</p>';
			}
			if ( $show_social ) {
				$this->render_socials( isset( $member['socials'] ) ? $member['socials'] : array() );
			}
			echo '</div>';
		}
		echo '</article>';
	}

	/**
	 * @param array<string,mixed> $member   Member.
	 * @param array<string,mixed> $settings Settings.
	 * @param string              $name     Name.
	 * @return void
	 */
	private function render_photo( $member, $settings, $name ) {
		$id = 0;
		if ( ! empty( $member['photo']['id'] ) ) {
			$id = absint( $member['photo']['id'] );
		}
		echo '<div class="ltxe-team-card__photo">';
		if ( $id ) {
			$size = isset( $settings['photo_size'] ) ? sanitize_key( (string) $settings['photo_size'] ) : 'medium';
			$img  = wp_get_attachment_image( $id, $size, false, array( 'alt' => $name ) );
			if ( is_string( $img ) && '' !== $img ) {
				echo wp_kses_post( $img );
				echo '</div>';
				return;
			}
		}
		if ( ! empty( $member['photo']['url'] ) ) {
			echo '<img src="' . esc_url( $member['photo']['url'] ) . '" alt="' . esc_attr( $name ) . '" />';
			echo '</div>';
			return;
		}
		echo '<span class="ltxe-team-card__photo--placeholder" aria-hidden="true">' . esc_html( $this->initials( $name ) ) . '</span>';
		echo '</div>';
	}

	/**
	 * @param string $name Name.
	 * @return string
	 */
	private function initials( $name ) {
		$parts = preg_split( '/\s+/', trim( $name ) );
		if ( ! is_array( $parts ) || empty( $parts[0] ) ) {
			return '?';
		}
		$out = substr( $parts[0], 0, 1 );
		if ( isset( $parts[1] ) && '' !== $parts[1] ) {
			$out .= substr( $parts[1], 0, 1 );
		}
		return strtoupper( $out );
	}

	/**
	 * @param string $name      Name.
	 * @param string $role      Role.
	 * @param bool   $show_role Show role.
	 * @param string $link      URL.
	 * @return void
	 */
	private function render_name_block( $name, $role, $show_role, $link ) {
		echo '<div class="ltxe-team-card__who">';
		if ( '' !== $name ) {
			if ( '' !== $link ) {
				echo '<a class="ltxe-team-card__name" href="' . esc_url( $link ) . '">' . esc_html( $name ) . '</a>';
			} else {
				echo '<h3 class="ltxe-team-card__name">' . esc_html( $name ) . '</h3>';
			}
		}
		if ( $show_role && '' !== $role ) {
			echo '<p class="ltxe-team-card__role">' . esc_html( $role ) . '</p>';
		}
		echo '</div>';
	}

	/**
	 * @param mixed $socials Social rows.
	 * @return void
	 */
	private function render_socials( $socials ) {
		if ( ! is_array( $socials ) || empty( $socials ) ) {
			return;
		}
		echo '<ul class="ltxe-team-card__social">';
		foreach ( $socials as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$url = $this->get_link_url( isset( $row['url'] ) ? $row['url'] : array() );
			if ( '' === $url ) {
				continue;
			}
			$label = isset( $row['label'] ) ? sanitize_text_field( (string) $row['label'] ) : '';
			if ( '' === $label ) {
				$label = __( 'Social profile', 'landtech-extras-for-elementor' );
			}
			echo '<li><a class="ltxe-team-card__social-link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">';
			echo '<span class="screen-reader-text">' . esc_html( $label ) . '</span>';
			if ( ! empty( $row['icon']['value'] ) ) {
				Icons_Manager::render_icon( $row['icon'], array( 'aria-hidden' => 'true' ) );
			} else {
				echo '<span aria-hidden="true">↗</span>';
			}
			echo '</a></li>';
		}
		echo '</ul>';
	}

	/**
	 * @param mixed $link Elementor URL control.
	 * @return string
	 */
	private function get_link_url( $link ) {
		if ( is_array( $link ) && ! empty( $link['url'] ) ) {
			return esc_url_raw( (string) $link['url'] );
		}
		if ( is_string( $link ) ) {
			return esc_url_raw( $link );
		}
		return '';
	}
}
