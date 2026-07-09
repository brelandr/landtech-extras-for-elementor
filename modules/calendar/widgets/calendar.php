<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Calendar\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Utils;
use LandTechExtras\Group_Control_Transition;
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\Calendar\Module as Module;
use LandTechExtras\Modules\CustomFields\Module as CustomFieldsModule;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Calendar
 *
 * @since 2.0.0
 */
class Calendar extends Extras_Widget {

	/**
	 * _events
	 *
	 * @since  2.0.0
	 * @var    array
	 */
	protected $_events;

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-calendar';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Calendar', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-post-calendar';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_script_depends() {
		return array(
			'landtech-extras-schedule-x-calendar',
			'landtech-extras-calendar-schedule-x',
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array(
			'landtech-extras-schedule-x-theme',
		);
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_sources',
			[
				'label' 	=> __( 'Events', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'source',
				[
					'label'			=> __( 'Source', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'manual',
					'options'		=> [
						'manual' 	=> __( 'Manual', 'landtech-extras-for-elementor' ),
						'posts' 	=> __( 'Posts', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'title',
				[
					'label'		=> __( 'Title', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Conference', 'landtech-extras-for-elementor' ),
				]
			);

			$repeater->add_control(
				'link',
				[
					'label' 	=> __( 'Link', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::URL,
					'placeholder' => __( 'https://your-link.com', 'landtech-extras-for-elementor' ),
					'default' => [
						'url' => '',
					],
				]
			);

			$repeater->add_control(
				'start',
				[
					'label'		=> __( 'Start Date', 'landtech-extras-for-elementor' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
					],
					'default' 	=> wp_date( 'Y-m-d H:i', strtotime( '+1 day', current_time( 'timestamp' ) ) ),
				]
			);

			$repeater->add_control(
				'end',
				[
					'label'		=> __( 'End Date', 'landtech-extras-for-elementor' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
					],
					'default' 	=> wp_date( 'Y-m-d H:i', strtotime( '+3 day', current_time( 'timestamp' ) ) ),
				]
			);

			$this->add_control(
				'events',
				[
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[],
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> '{{{ title }}}',
					'condition'		=> [
						'source'	=> 'manual',
					],
				]
			);

			$this->add_control(
				'post_type',
				[	
					'label'		=> __( 'Post Type', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'post',
					'condition'		=> [
						'source'	=> 'posts',
					],
					'options'	=> Utils::get_public_post_types_options( true ),
				]
			);

			$customfields = new CustomFieldsModule();

			$this->add_control(
				'post_dates_field_type',
				[	
					'label'		=> __( 'Fetch Dates From', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'post_date',
					'condition'		=> [
						'source'	=> 'posts',
					],
					'options'	=> array_merge(
						[ 'post_date' => __( 'Post Date', 'landtech-extras-for-elementor' ), ],
						$customfields->get_field_types()
					),
				]
			);

			foreach ( Utils::get_public_post_types_options() as $post_type => $label ) {
				$post_type_label = $label;
				foreach ( $customfields->get_field_types() as $field_type => $label ) {

					$fields_options = [
						'placeholder'	=> sprintf(
							/* translators: %s: Post type label (e.g. Posts, Pages). */
							__( 'Search %s Date Fields', 'landtech-extras-for-elementor' ),
							$post_type_label
						),
						'description'	=> sprintf(
							/* translators: %s: Custom field type name (e.g. ACF). */
							__( 'Search %s fields by label or name', 'landtech-extras-for-elementor' ),
							strtolower( $field_type )
						),
						'type' 			=> 'ee-query',
						'options' 		=> [],
						'label_block' 	=> false,
						'multiple' 		=> false,
						'post_type' 	=> $post_type,
						'query_type' 	=> $field_type,
						'query_options' => [
							'field_type'	=> [
								'date',
							],
							'show_group' => true,
						],
						'condition'		=> [
							'source'				=> 'posts',
							'post_dates_field_type' => $field_type,
							'post_type' 			=> $post_type,
						],
					];

					$this->add_control(
						'post_start_date_' . $field_type . '_' . $post_type,
						array_merge( $fields_options, [ 'label' => __( 'Start Date', 'landtech-extras-for-elementor' ), ] )
					);

					$this->add_control(
						'post_end_date_' . $field_type . '_' . $post_type,
						array_merge( $fields_options, [ 'label' => __( 'End Date', 'landtech-extras-for-elementor' ), ] )
					);
				}
			}

			$this->add_control(
				'no_events',
				[	
					'label'		=> __( 'Handle No Events', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options'		=> [
						''			=> __( 'Show Calendar', 'landtech-extras-for-elementor' ),
						'hide'		=> __( 'Hide Calendar', 'landtech-extras-for-elementor' ),
						'message'	=> __( 'Show Message', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$this->add_control(
				'no_events_message',
				[
					'label'		=> __( 'Message', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'There are currently no available events.', 'landtech-extras-for-elementor' ),
					'condition' => [
						'no_events' => 'message',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_calendar',
			[
				'label' 	=> __( 'Calendar', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'display_heading',
				[
					'label'		=> __( 'Display', 'landtech-extras-for-elementor' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'skin',
				[
					'label' 		=> __( 'Skin', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'default',
					'options'	=> [
						'default' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
						'compact' 	=> __( 'Compact', 'landtech-extras-for-elementor' ),
					],
					'prefix_class' 	=> 'ee-calendar-skin--',
				]
			);

			$this->add_control(
				'first_day',
				[	
					'label'		=> __( 'First Day', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '1',
					'options'	=> [
						'0' => __( 'Sunday', 'landtech-extras-for-elementor' ),
						'1' => __( 'Monday', 'landtech-extras-for-elementor' ),
						'2' => __( 'Tuesday', 'landtech-extras-for-elementor' ),
						'3' => __( 'Wednesday', 'landtech-extras-for-elementor' ),
						'4' => __( 'Thursday', 'landtech-extras-for-elementor' ),
						'5' => __( 'Friday', 'landtech-extras-for-elementor' ),
						'6' => __( 'Saturday', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'constrain_start',
				[
					'label'		=> __( 'Earliest Month', 'landtech-extras-for-elementor' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
						'dateFormat' => 'Y-m',
					],
					'label_block' => false,
					'default' 	=> '',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'constrain_end',
				[
					'label'		=> __( 'Latest Month', 'landtech-extras-for-elementor' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
						'dateFormat' => 'Y-m',
					],
					'label_block' => false,
					'default' 	=> '',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'default_current_month',
				[
					'label' 		=> __( 'Default to Current Month', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'default_month',
				[
					'label'		=> __( 'Default Month', 'landtech-extras-for-elementor' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
						'dateFormat' => 'Y-m',
					],
					'condition'	=> [
						'default_current_month' => ''
					],
					'label_block' => false,
					'default' 	=> '',
					'frontend_available' => true,
				]
			);




			$this->add_control(
				'links_heading',
				[
					'label'		=> __( 'Links', 'landtech-extras-for-elementor' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'	=> [
						'source' => 'posts',
					],
				]
			);

			$this->add_control(
				'link',
				[
					'label' 		=> __( 'Enable Links', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition'	=> [
						'source' => 'posts',
					],
				]
			);

			$this->add_control(
				'link_is_external',
				[
					'label' 		=> __( 'Open in new window', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition'	=> [
						'source' => 'posts',
					],
				]
			);

			$this->add_control(
				'link_no_follow',
				[
					'label' 		=> __( 'Add nofollow', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition'	=> [
						'source' => 'posts',
					],
				]
			);









			$this->add_control(
				'link_archive',
				[
					'label' 		=> __( 'Link to Archive', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'condition'		=> [
						'source'				=> 'posts',
						'post_dates_field_type' => 'post_date',
					],
					'frontend_available' => true,
				]
			);



















		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_calendar',
			[
				'label' => __( 'Calendar', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'calendar_width',
				[
					'label' 	=> __( 'Max. Width', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 300,
							'max' 	=> 1000,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar' => 'max-width: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'calendar_padding',
				[
					'label' 	=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar' => 'padding: {{SIZE}}px;',
					],
				]
			);



			$this->add_responsive_control(
				'calendar_align',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
					'options' 		=> [
						'left' 			=> [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'calendar_background_color',
				[
					'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__mount,
					 {{WRAPPER}} .sx__calendar-wrapper' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'calendar_border_radius',
				[
					'label' 	=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 20,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__mount,
						 {{WRAPPER}} .ee-calendar' => 'border-radius: {{SIZE}}px;',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'calendar_border',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} .ee-calendar',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'calendar_box_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-calendar',
				]
			);



		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_no_events',
			[
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'label' 	=> __( 'No Events', 'landtech-extras-for-elementor' ),
				'condition' => [
					'no_events' => 'message',
				],
			]
		);

			$this->add_control(
				'no_events_message_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__no-events' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'no_events_message_typography',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-calendar__no-events',
				]
			);

			$this->add_responsive_control(
				'no_events_message_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-calendar__no-events' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		switch ( $settings['source'] ) {
			case 'manual' :
				$this->setup_manual();
				break;

			case 'posts' :
				$this->get_posts_data();
				break;

			default :
				$this->setup_manual();
		}

		$this->render_data();
	}

	/**
	 * Get Post Data
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function get_posts_data() {
		$settings 		= $this->get_settings_for_display();
		$events 		= [];
		$args 			= [
			'post_type' 		=> $settings['post_type'],
			'post_status' 		=> 'publish',
			'posts_per_page' 	=> -1,
			'numberposts'		=> -1,
			'suppress_filters'  => false,
			'order'				=> $settings['event_order'],
		];

		if ( 'post_date' !== $settings['post_dates_field_type'] ) {
			$data_type = 'field';
		} else {
			$data_type = 'post';
		}

		$args = call_user_func_array( [ $this, 'parse_' . $data_type . '_query_args' ], [ $args, $settings['post_dates_field_type'] ] );

		/**
		 * Posts Args Filter
		 *
		 * Filters the query args when fetching posts
		 *
		 * @since 2.2.0
		 * @param array 			$args 		The query args
		 * @param array 			$settings 	The widget settings
		 */
		$args = apply_filters( 'landtech_extras/widgets/calendar/events/query/args', $args, $settings );

		$posts = get_posts( $args );

		foreach ( $posts as $post ) {

			$field_data = call_user_func_array( [ $this, 'get_' . $data_type . '_dates_args' ], [ $post ] );

			if ( ! $field_data || ! $field_data['start_date'] ) {
				continue;
			}

			$event_post_id 		= $post->ID;
			$event_title 		= $post->post_title;
			$event_link 		= '' !== $settings['link'] ? get_permalink( $post->ID ) : '';
			$event_target 		= '' !== $settings['link_is_external'] ? '_blank' : '';
			$event_rel 			= '' !== $settings['link_no_follow'] ? 'nofollow' : '';
			$event_start_date 	= $field_data['start_date'];
			$event_end_date 	= $field_data['end_date'];
			$event_archive 		= $field_data['archive'];

			$event = [
				'post_id' 	=> $event_post_id,
				'title' 	=> $event_title,
				'start' 	=> $event_start_date,
				'end' 		=> $event_end_date,
				'link'		=> $event_link,
				'target' 	=> $event_target,
				'rel'		=> $event_rel,
				'archive' 	=> $event_archive,
			];

			/**
			 * Posts Events Filter
			 *
			 * Provides access to events setup from posts
			 *
			 * @since 2.2.0
			 * @param array 			$event 		The event settings
			 * @param WP_Post 			$post 		The event post object
			 */
			$events[] = apply_filters( 'landtech_extras/widgets/calendar/events/event', $event, $post );
		}

		/**
		 * Posts Events Filter
		 *
		 * Provides access to events setup from posts
		 *
		 * @since 2.2.0
		 * @param array 			$events 	The array of events
		 * @param array 			$settings 	The widget settings
		 */
		$this->_events = apply_filters( 'landtech_extras/widgets/calendar/events', $events, $settings );
	}

	/**
	 * Parse query args for posts
	 *
	 * @since  2.2.42
	 * @return array
	 */
	protected function parse_post_query_args( $args, $type ) {
		return $args;
	}

	/**
	 * Parse query args for custom fields
	 *
	 * @since  2.2.42
	 * @return array
	 */
	protected function parse_field_query_args( $args, $type ) {

		if ( 'acf' !== $type ) {
			return $args;
		}

		$settings = $this->get_settings();

		$start_date_key = $settings['post_start_date_' . $settings['post_dates_field_type'] . '_' . $settings['post_type'] ];

		if ( $start_date_key ) {
			$field_object = get_field_object( $start_date_key );

			$args['orderby'] = 'meta_value';
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- ACF calendar ordering requires meta_key from selected date field.
			$args['meta_key'] = $field_object['name'];
		}

		return $args;
	}

	/**
	 * Get Post Dates Args
	 *
	 * @since  2.2.42
	 * @return array
	 */
	protected function get_post_dates_args( $post ) {
		$data = [
			'start_date' 	=> false,
			'end_date' 		=> false,
			'archive' 		=> false,
		];

		if ( $post ) {
			$data['start_date'] = $end_date = $post->post_date;
			$data['archive'] = get_day_link( get_the_date( 'Y', $post ), get_the_date( 'm', $post ), get_the_date( 'd', $post ) );
		}

		return $data;
	}

	/**
	 * Get Fields Dates Args
	 *
	 * @since  2.2.42
	 * @return array|bool
	 */
	protected function get_field_dates_args( $post ) {
		$settings 		= $this->get_settings();
		$customfields 	= new CustomFieldsModule();
		$data 			= [
			'start_date' 	=> false,
			'end_date' 		=> false,
			'archive' 		=> false,
		];

		if ( $post ) {
			$field = $customfields->get_component( $settings['post_dates_field_type'] );

			if ( ! $field ) {
				return false;
			}

			$start_date_key 	= $settings['post_start_date_' . $settings['post_dates_field_type'] . '_' . $settings['post_type'] ];
			$end_date_key 		= $settings['post_end_date_' . $settings['post_dates_field_type'] . '_' . $settings['post_type'] ];

			$data['start_date'] = $field->get_field_value( $post->ID, $start_date_key );
			$data['end_date'] 	= $field->get_field_value( $post->ID, $end_date_key );
			$data['archive'] 	= '';
		}

		return $data;
	}

	/**
	 * Setup Manual
	 *
	 * Sets up events data
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function setup_manual() {
		$settings = $this->get_settings_for_display();
		$events = [];

		if ( empty( $settings['events'] ) )
			return;

		foreach ( $settings['events'] as $index => $event ) {
			$events[] = [
				'title' 	=> $event['title'],
				'start' 	=> $event['start'],
				'end' 		=> $event['end'],
				'link'		=> ( '' !== $event['link']['url'] ) ? $event['link']['url'] : '',
				'target' 	=> $event['link']['is_external'] ? '_blank' : '_self',
				'rel'		=> ! empty( $event['link']['nofollow'] ) ? 'nofollow' : '',
				'archive' 	=> false,
			];
		}

		/**
		 * Manual Events Filter
		 *
		 * Provides access to manually set events date
		 *
		 * @since 2.2.0
		 * @param string 			$events 	The array of events
		 * @param string 			$settings 	The widget settings
		 */
		$this->_events = apply_filters( 'landtech_extras/widgets/calendar/events/manual', $events, $settings );
	}

	/**
	 * Render data
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_data() {
		$settings = $this->get_settings_for_display();

		if ( empty( $this->_events ) ) {

			if ( 'message' !== $settings['no_events'] ) {
				$this->render_placeholder( [
					'body' => __( 'You have no events in your calendar. Check the settings and make sure the source fields for the dates of the events are setup correctly.', 'landtech-extras-for-elementor' ),
				] );
			}

			if ( 'hide' === $settings['no_events'] ) {
				return;
			}

			if ( 'message' === $settings['no_events'] ) {
				$this->add_render_attribute('no-events', 'class', 'ee-calendar__no-events');

				?><div <?php $this->print_render_attribute_string( 'no-events' ); ?>><?php
					echo wp_kses_post( $settings['no_events_message'] );
				?></div><?php

				return;
			}
		}

		$this->add_render_attribute( [
			'calendar' => [
				'class' => [
					'ee-calendar',
				],
			],
		] );

		?>
		<div <?php $this->print_render_attribute_string( 'calendar' ); ?>>
			<div class="ee-calendar__mount"></div>
			<?php foreach ( $this->_events as $index => $event ) {

				if ( ! $event['start'] )
					continue;
				
				$title 		= $event['title'];
				$start 		= $event['start'];
				$end 		= ( ! empty( $event['end'] ) ) ? $event['end'] : $event['start'];
				$link 		= $event['link'];
				$target 	= $event['target'];
				$rel 		= $event['rel'];
				$archive 	= $event['archive'];

				$event_key 	= $this->get_repeater_setting_key( 'event', 'events', $index );

				$this->add_render_attribute( $event_key, [
					'class' 			=> 'ee-calendar-event',
					'data-archive' 		=> $archive,
					'data-target' 		=> $target,
					'data-rel' 			=> $rel,
					'data-link' 		=> $link,
					'data-start' 		=> $start,
					'data-end' 			=> $end,
					'data-before'		=> $this->get_before_title( $event ),
					'data-after'		=> $this->get_after_title( $event ),
				] );
			?><div <?php $this->print_render_attribute_string( $event_key ); ?>><?php
				echo esc_html( $title );
			?></div><?php
			}
		?></div><?php
	}

	/**
	 * Output before title
	 *
	 * @since  2.2.0
	 * @return void
	 */
	public function get_before_title( $event ) {

		ob_start();

		/**
		 * Before title.
		 *
		 * Fires before printing the title of the event.
		 *
		 * @since 2.2.0
		 *
		 * @param array $event The event data.
		 */
		do_action( 'landtech_extras/widgets/calendar/event/before_title', $event );

		return ob_get_clean();
	}

	/**
	 * Output after title
	 *
	 * @since  2.2.0
	 * @return void
	 */
	public function get_after_title( $event ) {

		ob_start();

		/**
		 * Before title.
		 *
		 * Fires after printing the title of the event.
		 *
		 * @since 2.2.0
		 *
		 * @param array $event The event data.
		 */
		do_action( 'landtech_extras/widgets/calendar/event/after_title', $event );

		return ob_get_clean();
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function content_template() {}
}
