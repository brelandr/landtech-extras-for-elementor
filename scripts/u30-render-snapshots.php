<?php
/**
 * Write front-end HTML snapshots from real widget render() output.
 *
 * Run inside wp-env, after Elementor and this plugin are active:
 * wp eval-file wp-content/plugins/landtech-extras-for-elementor/scripts/u30-render-snapshots.php
 *
 * Developer tool only. The zip excludes scripts/.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Elementor\Plugin' ) ) {
	fwrite( STDERR, "Elementor is not active.\n" );
	exit( 1 );
}

$plugin = \Elementor\Plugin::$instance;
if ( ! is_object( $plugin ) || ! isset( $plugin->elements_manager ) ) {
	fwrite( STDERR, "Elementor elements manager is not available.\n" );
	exit( 1 );
}

$out_dir = dirname( __DIR__ ) . '/tests/fixtures/u30-render';
if ( ! is_dir( $out_dir ) && ! mkdir( $out_dir, 0755, true ) && ! is_dir( $out_dir ) ) {
	fwrite( STDERR, "Could not create snapshot directory.\n" );
	exit( 1 );
}

$types = $plugin->widgets_manager->get_widget_types();
if ( ! is_array( $types ) ) {
	fwrite( STDERR, "No widget types registered.\n" );
	exit( 1 );
}

$written = 0;
$skipped = 0;

foreach ( $types as $name => $widget_type ) {
	if ( ! is_object( $widget_type ) ) {
		continue;
	}

	try {
		$reflect = new ReflectionClass( $widget_type );
	} catch ( ReflectionException $e ) {
		++$skipped;
		continue;
	}

	$file = (string) $reflect->getFileName();
	if ( false === strpos( $file, '/landtech-extras-for-elementor/' ) || false !== strpos( $file, '/landtech-extras-premium/' ) ) {
		continue;
	}

	$slug = preg_replace( '/[^a-z0-9\-]+/', '-', strtolower( (string) $name ) );
	$slug = trim( (string) $slug, '-' );
	if ( '' === $slug ) {
		++$skipped;
		continue;
	}

	$instance = $plugin->elements_manager->create_element_instance(
		array(
			'id'         => 'u30' . substr( md5( (string) $name ), 0, 6 ),
			'elType'     => 'widget',
			'widgetType' => (string) $name,
			'settings'   => array(),
		)
	);

	if ( ! is_object( $instance ) || ! method_exists( $instance, 'print_element' ) ) {
		fwrite( STDERR, "skip {$name}: no instance\n" );
		++$skipped;
		continue;
	}

	try {
		ob_start();
		$instance->print_element();
		$html = ob_get_clean();
	} catch ( Throwable $e ) {
		if ( ob_get_level() > 0 ) {
			ob_end_clean();
		}
		fwrite( STDERR, "skip {$name}: " . $e->getMessage() . "\n" );
		++$skipped;
		continue;
	}

	if ( ! is_string( $html ) || '' === trim( $html ) ) {
		fwrite( STDERR, "skip {$name}: empty render\n" );
		++$skipped;
		continue;
	}

	$document  = "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n<meta charset=\"utf-8\">\n";
	$document .= '<title>' . htmlspecialchars( (string) $name, ENT_QUOTES, 'UTF-8' ) . "</title>\n";
	$document .= "<link rel=\"stylesheet\" href=\"../../../assets/css/frontend.css\">\n</head>\n<body>\n<main>\n";
	$document .= $html;
	$document .= "\n</main>\n</body>\n</html>\n";

	$path = $out_dir . '/' . $slug . '.html';
	if ( false === file_put_contents( $path, $document ) ) {
		fwrite( STDERR, "skip {$name}: could not write\n" );
		++$skipped;
		continue;
	}

	++$written;
	fwrite( STDOUT, "wrote {$slug}.html\n" );
}

fwrite( STDOUT, "snapshots written {$written}, skipped {$skipped}\n" );
