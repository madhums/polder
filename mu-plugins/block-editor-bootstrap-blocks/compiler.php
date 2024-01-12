<?php

defined('ABSPATH') || exit;

// reload settings
$this->load_settings();

require_once 'vendor/autoload.php';
require_once 'CustomWebkit.php';
require_once 'CustomMozilla.php';

use ScssPhp\ScssPhp\Compiler;
use Padaliyajay\PHPAutoprefixer\Autoprefixer;

$bootstrap = '@import "mixins/banner";';
$bootstrap .= '@include bsBanner("");';

foreach( $this->config_options as $key => $value ){
	$bootstrap .= '$' . esc_attr( $key ) . ': ' . ( intval( $value ) ? 'true' : 'false' ) . ';';
}

$bootstrap .= '$grid-breakpoints: (';
foreach( $this->config_breakpoints as $breakpoint => $data ){
	$breakpoint = intval( $breakpoint ) ? intval( $breakpoint ) . 'px' : '0';
	$bootstrap .= esc_attr( $data['label'] ) . ': ' . $breakpoint . ',';
}
$bootstrap .= ');';

$bootstrap .= '$container-max-widths: (';
foreach( $this->config_container as $prefix => $size ){
	$bootstrap .= esc_attr( $prefix ) . ': ' . intval( $size ) . 'px,';
}
$bootstrap .= ');';

$bootstrap .= '$grid-gutter-width: ' . esc_attr( $this->config_gutter ) . ';';

$bootstrap .= esc_html( $this->config_scss_overrides );

foreach( $this->config_imports as $key => $value ){
	if( intval( $value ) ){
		$bootstrap .= '@import "' . esc_attr( $key ) . '";';
	}
}

// snapping
$bootstrap .= '
	:root{
		--scrollbar-half: 8.5px;
	}
	@media not all and (min-resolution: .001dpcm){
		body{
			--scrollbar-half: 7.5px;
		}
	}
	@media (hover: none){
		:root{
			--scrollbar-half: 0px;
		}
	}

	@each $breakpoint, $container-max-width in $container-max-widths {
		@include media-breakpoint-up($breakpoint, $grid-breakpoints) {
			:root{
				--halfInnerContainer: calc( ( #{$container-max-width} - #{$grid-gutter-width} ) / 2 );
			}
		}
	}

	:root{
		--bs-offset: #{$grid-gutter-width / 2};
		--snap: -#{$grid-gutter-width / 2};
	}
	@media (min-width: 576px){
		:root{
			--bs-offset: calc( 50vw - var(--halfInnerContainer) - var(--scrollbar-half) );
			--snap: calc( -1 * var(--bs-offset) );
		}
	}

	@each $breakpoint in map-keys($grid-breakpoints) {
		@include media-breakpoint-up($breakpoint) {
			$infix: breakpoint-infix($breakpoint, $grid-breakpoints);
			.container .bs-snap#{$infix}-left{
				margin-left: var(--snap);
				&.bs-snap-wo-inner{
					padding-left: var(--bs-offset);
				}
			}
			.container .bs-snap#{$infix}-right{
				margin-right: var(--snap);
				&.bs-snap-wo-inner{
					padding-right: var(--bs-offset);
				}
			}
			.container .bs-snap#{$infix}-none{
				margin-left: 0;
				margin-right: 0;
				&.bs-snap-wo-inner{
					padding-left: 0;
					padding-right: 0;
				}
			}
		}
	}
';

$scss = new Compiler();
$scss->setImportPaths( __DIR__ . '/vendor/twbs/bootstrap/scss/' );

$result = $scss->compileString( $bootstrap );
$unprefixed_css = $result->getCss();
$autoprefixer = new Autoprefixer( $unprefixed_css );

$autoprefixer->setVendors(array(
	\Padaliyajay\PHPAutoprefixer\Vendor\CustomWebkit::class,
	\Padaliyajay\PHPAutoprefixer\Vendor\CustomMozilla::class,
));

$prefixed_css = $autoprefixer->compile( false ); // minified
// $prefixed_css = $autoprefixer->compile(); // non-minified

$prefixed_css = str_replace(
	array(
		' 0.',
		'[type="date"]',
		'[type="datetime-local"]',
		'[type="month"]',
		'[type="week"]',
		'[type="time"]',
		'[role="button"]',
		'[type="button"]',
		'[type="reset"]',
		'[type="submit"]',
		'[type="search"]',
		'[type="file"]',
		'[type="checkbox"]',
		'[type="radio"]',
		'="top"',
		'="right"',
		'="bottom"',
		'="left"',
		"\'",
	),
	array(
		' .',
		'[type=date]',
		'[type=datetime-local]',
		'[type=month]',
		'[type=week]',
		'[type=time]',
		'[role=button]',
		'[type=button]',
		'[type=reset]',
		'[type=submit]',
		'[type=search]',
		'[type=file]',
		'[type=checkbox]',
		'[type=radio]',
		'=top',
		'=right',
		'=bottom',
		'=left',
		"'",
	),
	$prefixed_css
);

$uploads = wp_get_upload_dir();
if( ! file_exists( $uploads['basedir'] . '/bootstrap' ) ){
	mkdir( $uploads['basedir'] . '/bootstrap', 0777, true );
}
file_put_contents( $uploads['basedir'] . '/bootstrap/bootstrap.min.css', $prefixed_css );