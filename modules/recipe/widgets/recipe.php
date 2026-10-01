<?php
/**
 * Recipe widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Recipe\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\Recipe\Recipe_Schema;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __DIR__ ) . '/recipe-schema.php';

/**
 * @since 3.1.0
 */
class Recipe extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-recipe';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Recipe', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-nerd';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'recipe', 'schema', 'ingredients', 'cooking' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-recipe' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-recipe' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_recipe',
			array(
				'label' => __( 'Recipe', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'recipe_name',
			array(
				'label'   => __( 'Name', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Classic Chocolate Chip Cookies', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'recipe_description',
			array(
				'label'   => __( 'Description', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Crisp edges, chewy centers, and plenty of chocolate.', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'recipe_author',
			array(
				'label'   => __( 'Author', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'LandTech Kitchen', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'recipe_image',
			array(
				'label' => __( 'Image', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'prep_time',
			array(
				'label'   => __( 'Prep time', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 15,
				'min'     => 0,
			)
		);

		$this->add_control(
			'cook_time',
			array(
				'label'   => __( 'Cook time', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 0,
			)
		);

		$this->add_control(
			'total_time',
			array(
				'label'   => __( 'Total time', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 30,
				'min'     => 0,
			)
		);

		$this->add_control(
			'time_unit',
			array(
				'label'   => __( 'Time unit', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'minutes',
				'options' => array(
					'minutes' => __( 'Minutes', 'landtech-extras-for-elementor' ),
					'hours'   => __( 'Hours', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'servings',
			array(
				'label'   => __( 'Servings', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 24,
			)
		);

		$this->add_control(
			'serving_size',
			array(
				'label'   => __( 'Serving size', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '1 cookie', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'recipe_category',
			array(
				'label'   => __( 'Category', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Dessert', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'recipe_cuisine',
			array(
				'label'   => __( 'Cuisine', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'American', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'recipe_keywords',
			array(
				'label'   => __( 'Keywords', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'cookies, chocolate, baking',
			)
		);

		$this->add_control(
			'rating',
			array(
				'label'   => __( 'Star rating', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4.5,
				'min'     => 0,
				'max'     => 5,
				'step'    => 0.5,
			)
		);

		$this->add_control(
			'show_print',
			array(
				'label'        => __( 'Print button', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'modern',
				'options' => array(
					'classic' => __( 'Classic', 'landtech-extras-for-elementor' ),
					'modern'  => __( 'Modern Card', 'landtech-extras-for-elementor' ),
					'minimal' => __( 'Minimal', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->end_controls_section();

		$ing = new Repeater();
		$ing->add_control(
			'amount',
			array(
				'label'   => __( 'Amount', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '2',
			)
		);
		$ing->add_control(
			'unit',
			array(
				'label'   => __( 'Unit', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'cups', 'landtech-extras-for-elementor' ),
			)
		);
		$ing->add_control(
			'ingredient',
			array(
				'label'   => __( 'Ingredient', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'flour', 'landtech-extras-for-elementor' ),
			)
		);

		$this->start_controls_section(
			'section_ingredients',
			array(
				'label' => __( 'Ingredients', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'ingredients',
			array(
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $ing->get_controls(),
				'default' => array(
					array(
						'amount'     => '2 1/4',
						'unit'       => __( 'cups', 'landtech-extras-for-elementor' ),
						'ingredient' => __( 'all-purpose flour', 'landtech-extras-for-elementor' ),
					),
					array(
						'amount'     => '1',
						'unit'       => __( 'tsp', 'landtech-extras-for-elementor' ),
						'ingredient' => __( 'baking soda', 'landtech-extras-for-elementor' ),
					),
					array(
						'amount'     => '1',
						'unit'       => __( 'cup', 'landtech-extras-for-elementor' ),
						'ingredient' => __( 'butter, softened', 'landtech-extras-for-elementor' ),
					),
					array(
						'amount'     => '2',
						'unit'       => __( 'cups', 'landtech-extras-for-elementor' ),
						'ingredient' => __( 'chocolate chips', 'landtech-extras-for-elementor' ),
					),
				),
				'title_field' => '{{{ ingredient }}}',
			)
		);

		$this->end_controls_section();

		$step = new Repeater();
		$step->add_control(
			'step_text',
			array(
				'label'   => __( 'Step', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Mix the dry ingredients.', 'landtech-extras-for-elementor' ),
			)
		);
		$step->add_control(
			'step_image',
			array(
				'label' => __( 'Step image', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->start_controls_section(
			'section_steps',
			array(
				'label' => __( 'Instructions', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'instructions',
			array(
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $step->get_controls(),
				'default' => array(
					array( 'step_text' => __( 'Heat oven to 375°F (190°C).', 'landtech-extras-for-elementor' ) ),
					array( 'step_text' => __( 'Cream butter and sugars, then beat in eggs and vanilla.', 'landtech-extras-for-elementor' ) ),
					array( 'step_text' => __( 'Stir in flour mixture and chocolate chips. Scoop onto a sheet.', 'landtech-extras-for-elementor' ) ),
					array( 'step_text' => __( 'Bake 9–12 minutes until golden. Cool on the pan for 2 minutes.', 'landtech-extras-for-elementor' ) ),
				),
				'title_field' => '{{{ step_text }}}',
			)
		);

		$this->end_controls_section();

		$nut = new Repeater();
		$nut->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Calories', 'landtech-extras-for-elementor' ),
			)
		);
		$nut->add_control(
			'value',
			array(
				'label'   => __( 'Value', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '180',
			)
		);
		$nut->add_control(
			'unit',
			array(
				'label'   => __( 'Unit', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'kcal',
			)
		);

		$this->start_controls_section(
			'section_nutrition',
			array(
				'label' => __( 'Nutrition', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'nutrition',
			array(
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $nut->get_controls(),
				'default' => array(
					array(
						'label' => __( 'Calories', 'landtech-extras-for-elementor' ),
						'value' => '180',
						'unit'  => 'kcal',
					),
					array(
						'label' => __( 'Protein', 'landtech-extras-for-elementor' ),
						'value' => '2',
						'unit'  => 'g',
					),
					array(
						'label' => __( 'Fat', 'landtech-extras-for-elementor' ),
						'value' => '9',
						'unit'  => 'g',
					),
					array(
						'label' => __( 'Carbs', 'landtech-extras-for-elementor' ),
						'value' => '24',
						'unit'  => 'g',
					),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_recipe',
			array(
				'label' => __( 'Card', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_color',
			array(
				'label'     => __( 'Text color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-recipe' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-recipe' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'selector' => '{{WRAPPER}} .ltxe-recipe__title',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$s        = $this->get_settings_for_display();
		$layout   = isset( $s['layout'] ) ? sanitize_key( (string) $s['layout'] ) : 'modern';
		$name     = isset( $s['recipe_name'] ) ? (string) $s['recipe_name'] : '';
		$desc     = isset( $s['recipe_description'] ) ? (string) $s['recipe_description'] : '';
		$author   = isset( $s['recipe_author'] ) ? (string) $s['recipe_author'] : '';
		$unit     = isset( $s['time_unit'] ) ? sanitize_key( (string) $s['time_unit'] ) : 'minutes';
		$prep     = isset( $s['prep_time'] ) ? absint( $s['prep_time'] ) : 0;
		$cook     = isset( $s['cook_time'] ) ? absint( $s['cook_time'] ) : 0;
		$total    = isset( $s['total_time'] ) ? absint( $s['total_time'] ) : 0;
		$servings = isset( $s['servings'] ) ? (string) $s['servings'] : '';
		$size     = isset( $s['serving_size'] ) ? (string) $s['serving_size'] : '';
		$image    = ( isset( $s['recipe_image']['url'] ) && is_string( $s['recipe_image']['url'] ) ) ? $s['recipe_image']['url'] : '';
		$rating   = isset( $s['rating'] ) ? (float) $s['rating'] : 0;

		$ingredients = array();
		$ing_schema  = array();
		if ( ! empty( $s['ingredients'] ) && is_array( $s['ingredients'] ) ) {
			foreach ( $s['ingredients'] as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$line = trim( ( isset( $row['amount'] ) ? $row['amount'] : '' ) . ' ' . ( isset( $row['unit'] ) ? $row['unit'] : '' ) . ' ' . ( isset( $row['ingredient'] ) ? $row['ingredient'] : '' ) );
				if ( '' === $line ) {
					continue;
				}
				$ingredients[] = $line;
				$ing_schema[]  = $line;
			}
		}

		$steps        = array();
		$steps_schema = array();
		if ( ! empty( $s['instructions'] ) && is_array( $s['instructions'] ) ) {
			$n = 1;
			foreach ( $s['instructions'] as $row ) {
				if ( ! is_array( $row ) || empty( $row['step_text'] ) ) {
					continue;
				}
				$img = ( isset( $row['step_image']['url'] ) && is_string( $row['step_image']['url'] ) ) ? $row['step_image']['url'] : '';
				$steps[] = array(
					'text'  => (string) $row['step_text'],
					'image' => $img,
				);
				$how = array(
					'@type' => 'HowToStep',
					'name'  => sprintf(
						/* translators: %d: step number */
						__( 'Step %d', 'landtech-extras-for-elementor' ),
						$n
					),
					'text'  => (string) $row['step_text'],
				);
				if ( '' !== $img ) {
					$how['image'] = $img;
				}
				$steps_schema[] = $how;
				++$n;
			}
		}

		$nutrition        = array();
		$nutrition_schema = array();
		if ( ! empty( $s['nutrition'] ) && is_array( $s['nutrition'] ) ) {
			foreach ( $s['nutrition'] as $row ) {
				if ( ! is_array( $row ) || empty( $row['label'] ) ) {
					continue;
				}
				$label = (string) $row['label'];
				$val   = isset( $row['value'] ) ? (string) $row['value'] : '';
				$u     = isset( $row['unit'] ) ? (string) $row['unit'] : '';
				$nutrition[] = array(
					'label' => $label,
					'value' => $val,
					'unit'  => $u,
				);
				$key = strtolower( preg_replace( '/[^a-z]+/i', '', $label ) );
				$map = array(
					'calories' => 'calories',
					'protein'  => 'proteinContent',
					'fat'      => 'fatContent',
					'carbs'    => 'carbohydrateContent',
					'carbohydrate' => 'carbohydrateContent',
				);
				if ( isset( $map[ $key ] ) ) {
					$nutrition_schema[ $map[ $key ] ] = trim( $val . ' ' . $u );
				}
			}
		}

		$yield = trim( $servings . ( '' !== $size ? ' (' . $size . ')' : '' ) );
		$graph = Recipe_Schema::build(
			array(
				'name'                => $name,
				'description'         => $desc,
				'author'              => $author,
				'image'               => $image,
				'prepTime'            => Recipe_Schema::iso_duration( $prep, $unit ),
				'cookTime'            => Recipe_Schema::iso_duration( $cook, $unit ),
				'totalTime'           => Recipe_Schema::iso_duration( $total, $unit ),
				'recipeYield'         => $yield,
				'recipeCategory'      => isset( $s['recipe_category'] ) ? (string) $s['recipe_category'] : '',
				'recipeCuisine'       => isset( $s['recipe_cuisine'] ) ? (string) $s['recipe_cuisine'] : '',
				'keywords'            => isset( $s['recipe_keywords'] ) ? (string) $s['recipe_keywords'] : '',
				'recipeIngredient'    => $ing_schema,
				'recipeInstructions'  => $steps_schema,
				'nutrition'           => $nutrition_schema,
				'rating'              => $rating,
			)
		);

		$json = wp_json_encode( $graph );
		if ( ! is_string( $json ) ) {
			$json = '{}';
		}

		echo '<article class="ltxe-recipe ltxe-recipe--' . esc_attr( $layout ) . '" data-ltxe-recipe="1">';
		if ( isset( $s['show_print'] ) && 'yes' === $s['show_print'] ) {
			echo '<button type="button" class="ltxe-recipe__print">' . esc_html__( 'Print recipe', 'landtech-extras-for-elementor' ) . '</button>';
		}
		if ( '' !== $image ) {
			echo '<img class="ltxe-recipe__image" src="' . esc_url( $image ) . '" alt="' . esc_attr( $name ) . '" />';
		}
		echo '<h3 class="ltxe-recipe__title">' . esc_html( $name ) . '</h3>';
		if ( '' !== $desc ) {
			echo '<p class="ltxe-recipe__desc">' . esc_html( $desc ) . '</p>';
		}
		echo '<ul class="ltxe-recipe__meta">';
		echo '<li>' . esc_html__( 'Prep', 'landtech-extras-for-elementor' ) . ': ' . esc_html( (string) $prep . ' ' . $unit ) . '</li>';
		echo '<li>' . esc_html__( 'Cook', 'landtech-extras-for-elementor' ) . ': ' . esc_html( (string) $cook . ' ' . $unit ) . '</li>';
		echo '<li>' . esc_html__( 'Total', 'landtech-extras-for-elementor' ) . ': ' . esc_html( (string) $total . ' ' . $unit ) . '</li>';
		if ( '' !== $yield ) {
			echo '<li>' . esc_html__( 'Yield', 'landtech-extras-for-elementor' ) . ': ' . esc_html( $yield ) . '</li>';
		}
		if ( $rating > 0 ) {
			echo '<li class="ltxe-recipe__rating">' . esc_html( (string) $rating ) . ' / 5</li>';
		}
		echo '</ul>';

		if ( ! empty( $ingredients ) ) {
			echo '<h4 class="ltxe-recipe__heading">' . esc_html__( 'Ingredients', 'landtech-extras-for-elementor' ) . '</h4>';
			echo '<ul class="ltxe-recipe__ingredients">';
			foreach ( $ingredients as $line ) {
				echo '<li>' . esc_html( $line ) . '</li>';
			}
			echo '</ul>';
		}

		if ( ! empty( $steps ) ) {
			echo '<h4 class="ltxe-recipe__heading">' . esc_html__( 'Instructions', 'landtech-extras-for-elementor' ) . '</h4>';
			echo '<ol class="ltxe-recipe__steps">';
			foreach ( $steps as $step ) {
				echo '<li><span>' . esc_html( $step['text'] ) . '</span>';
				if ( '' !== $step['image'] ) {
					echo '<img src="' . esc_url( $step['image'] ) . '" alt="" />';
				}
				echo '</li>';
			}
			echo '</ol>';
		}

		if ( ! empty( $nutrition ) ) {
			echo '<h4 class="ltxe-recipe__heading">' . esc_html__( 'Nutrition', 'landtech-extras-for-elementor' ) . '</h4>';
			echo '<ul class="ltxe-recipe__nutrition">';
			foreach ( $nutrition as $row ) {
				echo '<li><span>' . esc_html( $row['label'] ) . '</span> <strong>' . esc_html( trim( $row['value'] . ' ' . $row['unit'] ) ) . '</strong></li>';
			}
			echo '</ul>';
		}

		wp_print_inline_script_tag(
			$json,
			array(
				'type' => 'application/ld+json',
				'id'   => 'ltxe-recipe-schema-' . sanitize_key( (string) $this->get_id() ),
			)
		);
		echo '</article>';
	}
}
