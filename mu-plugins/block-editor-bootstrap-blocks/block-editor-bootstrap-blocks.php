<?php
/*
	Plugin Name:		Block Editor Bootstrap Blocks
	Plugin URI:			https://www.paypal.me/jakubnovaksl
	Description:		Fully responsive Bootstrap 5 blocks, components and extends for Gutenberg
	Requires at least:	5.6
	Requires PHP:		5.6
	Version:			6.4.4
	Author:				KubiQ
	Author URI:			https://kubiq.sk
	License:			GPL-2.0-or-later
	License URI:		https://www.gnu.org/licenses/gpl-2.0.html
	Text Domain:		bootstrap
	Domain Path:		/languages
*/

defined('ABSPATH') || exit;

include_once 'bootstrap-template-loader.php';

class BootstrapBlocks{
	var $plugin_admin_page;
	var $settings;
	var $experiments;
	var $config_options;
	var $config_breakpoints;
	var $config_container;
	var $config_gutter;
	var $config_scss_overrides;
	var $config_imports;
	var $config_imports_helpers;
	var $tab;
	var $final_css = array();

	private static $instance = null;

	function is_valid_buffer( $content ){
		if( stripos( $content, '<html' ) === false || stripos( $content, '</head>' ) === false ){
			return false;
		}
		return true;
	}

	function maybe_inject_inline_css( $content ){
		if( $this->is_valid_buffer( $content ) ){
			$spacing_css = $this->get_extra_css();
			if( $spacing_css ){
				$content = str_replace( '</head>', $spacing_css . '</head>', $content );
			}
		}
		return $content;
	}

	public function __construct(){
		load_plugin_textdomain( 'bootstrap', FALSE, basename( __DIR__ ) . '/languages/' );

		add_action( 'init', function(){
			ob_start( array( $this, 'maybe_inject_inline_css' ) );
		}, -1 );
	}

	public static function instance(){
		if( is_null( self::$instance ) ){
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	public function init(){
		add_action( 'init', array( $this, 'register_bootstrap_blocks' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'plugin_menu_link' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), wp_theme_has_theme_json() ? PHP_INT_MIN : 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_override' ), PHP_INT_MAX );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ), 9 );
		add_action( 'wp_loaded', array( $this, 'fix_attributes_for_ServerSideRender' ), 999 );

		add_action( 'wp_head', array( $this, 'calculate_offset_helper' ), PHP_INT_MIN );

		add_filter( 'render_block', array( $this, 'render_block' ), 20, 2 );
	}

	function fix_attributes_for_ServerSideRender(){
		$registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
		foreach( $registered_blocks as $block ){
			$block->attributes['bsSpacing'] = array(
				'type' => 'object',
				'default' => array(
					'margin' => array(),
					'padding' => array(),
					'important' => false
				)
			);
			$block->attributes['bsSnappingWithoutInner'] = array(
				'type' => 'boolean',
				'default' => true
			);
			$block->attributes['bsDisplayPrint'] = array(
				'type' => 'string',
				'default' => ''
			);
			$block->attributes['bsHideForLoggedIn'] = array(
				'type' => 'boolean',
				'default' => false
			);
			$block->attributes['bsHideForLoggedOut'] = array(
				'type' => 'boolean',
				'default' => false
			);
			$block->attributes['bsClasses'] = array(
				'type' => 'string',
				'default' => ''
			);

			$responsiveObjects = [ 'bsSnapping', 'bsDisplay', 'bsAlignment', 'bsFlexDirection', 'bsFlexWrap', 'bsFlexJustifyContent', 'bsFlexAlignItems', 'bsFlexAlignContent', 'bsFlexGap', 'bsFlexGrow', 'bsFlexShrink', 'bsFlexBasis', 'bsFlexAlignSelf', 'bsFlexOrder', 'bsPosition', 'bsPositionZindex', 'bsPositionTop', 'bsPositionRight', 'bsPositionBottom', 'bsPositionLeft' ];
			foreach( $responsiveObjects as $responsiveObject ){
				$block->attributes[ $responsiveObject ] = array(
					'type' => 'object',
					'default' => array()
				);
			}
		}
	}

	function calculate_offset_helper(){
		echo '<script>document.addEventListener("readystatechange",function(){var e,t;"interactive"==document.readyState&&((e=document.createElement("div")).className="container",(t=document.createElement("div")).className="bs-offset-helper",e.appendChild(t),document.body.appendChild(e),setTimeout(function(){document.documentElement.style.setProperty("--bs-offset",document.querySelector(".bs-offset-helper").getBoundingClientRect().left+"px")},1))},!0);var bsOffsetTimer="";window.addEventListener("resize",function(){clearTimeout(bsOffsetTimer),bsOffsetTimer=setTimeout(function(){document.documentElement.style.setProperty("--bs-offset",document.querySelector(".bs-offset-helper").getBoundingClientRect().left+"px")},100)},!0);</script>';
	}

	function render_block( $block_content, $block ){
		global $wp_version;

		// maybe hide for logged in user
		if( isset( $block['attrs']['bsHideForLoggedIn'] ) && $block['attrs']['bsHideForLoggedIn'] && is_user_logged_in() ){
			return '';
		}

		// maybe hide for logged out visitor
		if( isset( $block['attrs']['bsHideForLoggedOut'] ) && $block['attrs']['bsHideForLoggedOut'] && ! is_user_logged_in() ){
			return '';
		}

		// maybe render extra CSS
		if( isset( $block['attrs'] ) && isset( $block['attrs']['tempID'] ) && $block['attrs']['tempID'] ){
			$extraCSSitems = [ 'bsSpacing', 'bsFlexGap', 'bsFlexBasis', 'bsPosition', 'bsPositionZindex', 'bsPositionTop', 'bsPositionRight', 'bsPositionBottom', 'bsPositionLeft' ];

			$block_extra_css = array();

			foreach( $extraCSSitems as $extraCSSitem ){
				if( isset( $block['attrs'][ $extraCSSitem ] ) ){
					$block_extra_css[ $extraCSSitem ] = $block['attrs'][ $extraCSSitem ];
				}
			}

			if( $block_extra_css ){
				$block_extra_css['tempID'] = $block['attrs']['tempID'];
				$this->final_css[] = $block_extra_css;
			}
		}
		
		// maybe fix inner containers
		if( $this->settings['container_class'] ){
			if( $block['blockName'] === 'core/group' && isset( $block['attrs']['align'] ) && $block['attrs']['align'] == 'full' ){
				if( ( defined('GUTENBERG_VERSION') && version_compare( GUTENBERG_VERSION, 12.9, '>=' ) ) || version_compare( $wp_version, 6.0, '>=' ) ){
					add_filter( 'render_block_core/group', array( $this, 'render_block_core_group' ), 20, 1 );
				}else{
					$pos = strpos( $block_content, 'wp-block-group__inner-container' );
					if( $pos !== false ){
						$block_content = substr_replace( $block_content, 'wp-block-group__inner-container container', $pos, strlen( 'wp-block-group__inner-container' ) );
					}
				}
			}elseif( $block['blockName'] === 'core/cover' && isset( $block['attrs']['align'] ) && $block['attrs']['align'] == 'full' ){
				$pos = strpos( $block_content, 'wp-block-cover__inner-container' );
				if( $pos !== false ){
					$block_content = substr_replace( $block_content, 'wp-block-cover__inner-container container', $pos, strlen( 'wp-block-cover__inner-container' ) );
				}
			}
		}

		// maybe remove .is-layout-constrained class
		if( $this->settings['remove_constrain_class'] ){
			if( $block['blockName'] === 'core/group' && isset( $block['attrs']['align'] ) && $block['attrs']['align'] == 'full' ){
				$pos = strpos( $block_content, 'is-layout-constrained' );
				if( $pos !== false ){
					$block_content = substr_replace( $block_content, '', $pos, strlen( 'is-layout-constrained' ) );
				}
			}
		}

		return $block_content;
	}

	function render_block_core_group( $block_content ){
		remove_filter( 'render_block_core/group', array( $this, 'render_block_core_group' ), 20 );
		$pos = strpos( $block_content, 'wp-block-group__inner-container' );
		if( $pos !== false ){
			$block_content = substr_replace( $block_content, 'wp-block-group__inner-container container', $pos, strlen( 'wp-block-group__inner-container' ) );
		}
		return $block_content;
	}

	function get_extra_css(){
		if( count( $this->final_css ) ){
			$breakpoints = $this->config_breakpoints;
			$breakpointsCSS = array();
			$alreadyAdded = array();
			foreach( $this->final_css as $block ){
				if( ! isset( $alreadyAdded[ $block['tempID'] ] ) ){
					$alreadyAdded[ $block['tempID'] ] = 1;
					foreach( $breakpoints as $breakpointSize => $breakpointData ){
						$breakpointSize = floatval( $breakpointSize );
						$tempCSS = '';

						if( isset( $block['bsSpacing'] ) ){
							if( isset( $block['bsSpacing']['margin'][ $breakpointData['prefix'] ] ) ){
								foreach( $block['bsSpacing']['margin'][ $breakpointData['prefix'] ] as $position => $value ){
									if( $value !== '' ){
										if( is_numeric( $value ) ){
											$value .= 'px';
										}
										$tempCSS .= 'margin-' . strtolower( $position ) . ':' . ( $value . ( $block['bsSpacing']['important'] ? ' !important' : '' ) ) . ';';
									}
								}
							}

							if( isset( $block['bsSpacing']['padding'][ $breakpointData['prefix'] ] ) ){
								foreach( $block['bsSpacing']['padding'][ $breakpointData['prefix'] ] as $position => $value ){
									if( $value !== '' ){
										if( is_numeric( $value ) ){
											$value .= 'px';
										}
										$tempCSS .= 'padding-' . strtolower( $position ) . ':' . ( $value . ( $block['bsSpacing']['important'] ? ' !important' : '' ) ) . ';';
									}
								}
							}
						}

						$basicProperties = [
							'bsFlexGap' => [ 'property' => 'gap', 'convertToPixels' => true ],
							'bsFlexBasis' => [ 'property' => 'flex-basis', 'convertToPixels' => false ],
							'bsPosition' => [ 'property' => 'position', 'convertToPixels' => false ],
							'bsPositionZindex' => [ 'property' => 'z-index', 'convertToPixels' => false ],
							'bsPositionTop' => [ 'property' => 'top', 'convertToPixels' => true ],
							'bsPositionRight' => [ 'property' => 'right', 'convertToPixels' => true ],
							'bsPositionBottom' => [ 'property' => 'bottom', 'convertToPixels' => true ],
							'bsPositionLeft' => [ 'property' => 'left', 'convertToPixels' => true ],
						];

						foreach( $basicProperties as $blockOption => $item ){
							if( isset( $block[ $blockOption ], $block[ $blockOption ][ $breakpointData['prefix'] ] ) ){
								$value = trim( $block[ $blockOption ][ $breakpointData['prefix'] ] );
								if( $value !== '' ){
									if( $item['convertToPixels'] && is_numeric( $value ) ){
										$value .= 'px';
									}
									$tempCSS .= $item['property'] . ':' . $value . ';';
								}
							}
						}

						if( $tempCSS ){
							if( ! isset( $breakpointsCSS[ $breakpointSize ] ) ){
								$breakpointsCSS[ $breakpointSize ] = '';
							}
							$breakpointsCSS[ $breakpointSize ] .= 'body .bs-' . $block['tempID'] . '{' . $tempCSS . '}';
						}
					}
				}
			}

			if( count( $breakpointsCSS ) ){
				$bsCSS = '';
				ksort( $breakpointsCSS );
				foreach( $breakpointsCSS as $breakpointSize => $breakpointCSS ){
					if( $breakpointSize ){
						$bsCSS .= '@media(min-width:' . $breakpointSize . 'px){';
					}
					$bsCSS .= $breakpointCSS;
					if( $breakpointSize ){
						$bsCSS .= '}';
					}
				}
				return '<style id="bs-extra-css">' . esc_html( $bsCSS ) . '</style>';
			}
		}
		return '';
	}

	function load_settings(){
		// default settings
		$this->settings = get_option( 'BootstrapBlocks_settings', array() );
		$this->settings = array_merge(
			array(
				'theme_css' => 1,
				'theme_js' => 1,
				'editor_css' => 1,
				'container_class' => 1,
				'block_max_width' => 1,
				'remove_constrain_class' => 1,
			),
			$this->settings
		);

		// default experiments
		$this->experiments = get_option( 'BootstrapBlocks_experiments', array() );
		$this->experiments = array_merge(
			array(
				'js_parts' => 0,
				'js-alert' => 0,
				'js-button' => 0,
				'js-carousel' => 0,
				'js-collapse' => 0,
				'js-dropdown' => 0,
				'js-modal' => 0,
				'js-offcanvas' => 0,
				'js-scrollspy' => 0,
				'js-tab' => 0,
				'js-toast' => 0,
				'js-tooltip' => 0,
				'js-popover' => 0,
			),
			$this->experiments
		);

		// default config
		$this->config_options = get_option( 'Bootstrap_config_options', array() );
		$this->config_options = array_merge(
			array(
				'enable-caret' => 1,
				'enable-rounded' => 1,
				'enable-shadows' => 0,
				'enable-gradients' => 0,
				'enable-transitions' => 1,
				'enable-reduced-motion' => 1,
				'enable-smooth-scroll' => 1,
				'enable-grid-classes' => 1,
				'enable-container-classes' => 1,
				'enable-cssgrid' => 0,
				'enable-button-pointers' => 1,
				'enable-rfs' => 1,
				'enable-validation-icons' => 1,
				'enable-negative-margins' => 0,
				'enable-deprecation-messages' => 1,
				'enable-important-utilities' => 1,
			),
			$this->config_options
		);

		$this->config_breakpoints = get_option(
			'Bootstrap_config_breakpoints',
			array(
				0 => array(
					'label' => 'xs',
					'prefix' => '',
					'default' => false
				),
				576 => array(
					'label' => 'sm',
					'prefix' => 'sm',
					'default' => false
				),
				768 => array(
					'label' => 'md',
					'prefix' => 'md',
					'default' => false
				),
				992 => array(
					'label' => 'lg',
					'prefix' => 'lg',
					'default' => true
				),
				1200 => array(
					'label' => 'xl',
					'prefix' => 'xl',
					'default' => false
				),
				1400 => array(
					'label' => 'xxl',
					'prefix' => 'xxl',
					'default' => false
				)
			)
		);

		$this->config_container = get_option(
			'Bootstrap_config_container',
			array(
				'sm' => 540,
				'md' => 720,
				'lg' => 960,
				'xl' => 1140,
				'xxl' => 1320,
			)
		);

		$this->config_gutter = get_option( 'Bootstrap_config_gutter', '1.5rem' );

		$this->config_scss_overrides = get_option( 'Bootstrap_config_scss_overrides', '' );

		$this->config_imports = get_option( 'Bootstrap_config_imports', array() );
		$this->config_imports = array_merge(
			array(
				'functions' => 1,
				'variables' => 1,
				'maps' => 1,
				'mixins' => 1,
				'utilities' => 1,
				'root' => 1,
				'reboot' => 1,
				'type' => 1,
				'containers' => 1,
				'grid' => 1,
				'transitions' => 1,
				'nav' => 1,
				'navbar' => 1,
				'images' => 1,
				'tables' => 1,
				'forms' => 1,
				'buttons' => 1,
				'dropdown' => 1,
				'button-group' => 1,
				'card' => 1,
				'accordion' => 1,
				'breadcrumb' => 1,
				'pagination' => 1,
				'badge' => 1,
				'alert' => 1,
				'progress' => 1,
				'list-group' => 1,
				'close' => 1,
				'toasts' => 1,
				'modal' => 1,
				'tooltip' => 1,
				'popover' => 1,
				'carousel' => 1,
				'spinners' => 1,
				'offcanvas' => 1,
				'placeholders' => 1,
				'helpers/clearfix' => 1,
				'helpers/color-bg' => 1,
				'helpers/colored-links' => 1,
				'helpers/ratio' => 1,
				'helpers/position' => 1,
				'helpers/stacks' => 1,
				'helpers/visually-hidden' => 1,
				'helpers/stretched-link' => 1,
				'helpers/text-truncation' => 1,
				'helpers/vr' => 1,
				'utilities/api' => 1,
			),
			$this->config_imports
		);

		$this->config_imports_helpers = [
			'functions' => __( 'just leave this active', 'bootstrap' ),
			'variables' => __( 'just leave this active', 'bootstrap' ),
			'maps' => __( 'just leave this active', 'bootstrap' ),
			'mixins' => __( 'just leave this active', 'bootstrap' ),
			'utilities' => __( 'just leave this active', 'bootstrap' ),

			'root' => __( ':root variables', 'bootstrap' ),
			'reboot' => __( 'css reset', 'bootstrap' ),
			
			'type' => __( '.h1, .h2, .h3, .h4, .h5, .h6, .lead, .display-*, .small, .mark, .list-unstyled, .list-inline, .list-inline-item, .initialism, .blockquote, .blockquote-footer', 'bootstrap' ),
			
			'transitions' => __( '.fade, .show, .collapse, .collapsing, .collapse-horizontal', 'bootstrap' ),

			'containers' => __( '.container, .container-*', 'bootstrap' ),
			'grid' => __( '.row, .col-*, .grid', 'bootstrap' ),
			
			'close' => __( '.btn-close', 'bootstrap' ),
			'images' => __( '.img-fluid, .img-thumbnail, .figure, .figure-img, .figure-caption', 'bootstrap' ),

			'accordion' => __( 'all accordion classes', 'bootstrap' ),
			'alert' => __( 'all alert classes', 'bootstrap' ),
			'badge' => __( 'all badge classes', 'bootstrap' ),
			'breadcrumb' => __( 'all breadcrumb classes', 'bootstrap' ),
			'button-group' => __( 'all btn-group classes', 'bootstrap' ),
			'buttons' => __( 'all btn classes', 'bootstrap' ),
			'card' => __( 'all card classes', 'bootstrap' ),
			'carousel' => __( 'all carousel classes', 'bootstrap' ),
			'dropdown' => __( 'all dropdown classes', 'bootstrap' ),
			'forms' => __( 'all form, label and input classes', 'bootstrap' ),
			'list-group' => __( 'all list-group classes', 'bootstrap' ),
			'modal' => __( 'all modal classes', 'bootstrap' ),
			'nav' => __( 'all nav classes', 'bootstrap' ),
			'navbar' => __( 'all navbar classes', 'bootstrap' ),
			'offcanvas' => __( 'all offcanvas classes', 'bootstrap' ),
			'pagination' => __( 'all pagination classes', 'bootstrap' ),
			'placeholders' => __( 'all placeholder classes', 'bootstrap' ),
			'popover' => __( 'all popover classes', 'bootstrap' ),
			'progress' => __( 'all progress classes', 'bootstrap' ),
			'spinners' => __( 'all spinner classes', 'bootstrap' ),
			'tables' => __( 'all table classes', 'bootstrap' ),
			'toasts' => __( 'all toast classes', 'bootstrap' ),
			'tooltip' => __( 'all tooltip classes', 'bootstrap' ),
			
			'helpers/clearfix' => __( '.clearfix', 'bootstrap' ),
			'helpers/color-bg' => __( '.text-bg-[color]', 'bootstrap' ),
			'helpers/colored-links' => __( '.link-[color]', 'bootstrap' ),
			'helpers/ratio' => __( '.ratio, .ratio-*', 'bootstrap' ),
			'helpers/position' => __( '.fixed-top, .fixed-bottom, .sticky-top, .sticky-bottom', 'bootstrap' ),
			'helpers/stacks' => __( '.hstack, .vstack', 'bootstrap' ),
			'helpers/visually-hidden' => __( '.visually-hidden, .visually-hidden-focusable', 'bootstrap' ),
			'helpers/stretched-link' => __( '.stretched-link', 'bootstrap' ),
			'helpers/text-truncation' => __( '.text-truncate', 'bootstrap' ),
			'helpers/vr' => __( '.vr', 'bootstrap' ),
			
			'utilities/api' => __( 'just leave this active', 'bootstrap' ),
		];
	}

	function register_bootstrap_blocks(){
		if( ! function_exists( 'register_block_type_from_metadata' ) ){
			return;
		}

		if( class_exists( 'WP_Block_Editor_Context' ) ){
			$filter_name = 'block_categories_all';
		}else{
			$filter_name = 'block_categories';
		}

		add_filter( $filter_name, function( $categories, $post ){
			return array_merge(
				$categories,
				array(
					array(
						'slug' => 'bootstrap',
						'title' => 'Bootstrap',
					)
				)
			);
		}, 10, 2 );

		// load settings
		$this->load_settings();

		// register blocks
		$blocks_path = plugin_dir_path( __FILE__ ) . 'src/blocks/';
		$blocks = scandir( $blocks_path, 1 );
		foreach( $blocks as $block ){
			if( substr( $block, 0, 1 ) != '.' && is_dir( $blocks_path . $block ) ){
				register_block_type_from_metadata( $blocks_path . $block );
			}
		}

		// maybe load CSS to limit blocks width
		if( $this->settings['block_max_width'] ){
			add_action( 'enqueue_block_editor_assets', function(){
				wp_enqueue_style( 'bootstrap-block_max_width', plugins_url( 'assets/css/block_max_width.css', __FILE__ ) );
			}, PHP_INT_MAX );
		}
	}

	function enqueue_block_editor_assets(){
		global $current_screen;

		$dependencies = array( 'wp-blocks', 'wp-block-editor', 'wp-i18n', 'wp-element', 'wp-plugins' );
		if( ! isset( $current_screen->id ) || $current_screen->id != 'widgets' ){
			$dependencies[] = 'wp-edit-post';
		}

		wp_enqueue_style(
			'bootstrap-blocks-editor',
			plugins_url( 'build/index.css', __FILE__ ),
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . '/build/index.css' )
		);

		wp_enqueue_script(
			'bootstrap-blocks',
			plugins_url( 'build/index.js', __FILE__ ),
			$dependencies,
			filemtime( plugin_dir_path( __FILE__ ) . '/build/index.js' )
		);

		wp_localize_script(
			'bootstrap-blocks',
			'bootstrapBlocks',
			array(
				'breakpoints' => $this->config_breakpoints,
				'useOnFocus' => is_wp_version_compatible('6.3')
			)
		);
	}

	function get_theme_traversal_path( $target_path ){
		$traversal_path = '.';

		$target_path = str_replace( '\\', '/', realpath( $target_path ) );

		$source_path = get_stylesheet_directory();
		$source_path = str_replace( '\\', '/', $source_path );

		while( strpos( $target_path, $source_path ) === false ){
			$traversal_path .= '/..';
			$source_path = dirname( $source_path );
		}

		return $traversal_path . str_replace( $source_path, '', $target_path );
	}

	function admin_init(){
		global $editor_styles;
		$editor_styles = (array)$editor_styles;

		if( is_admin() && get_option('BootstrapBlocksActivated') ){
			delete_option( 'BootstrapBlocksActivated' );
			wp_redirect( admin_url( 'options-general.php?page=' . basename( __FILE__ ) ) );
		}
		
		$snapping_enqueued = false;
		if( $this->settings['editor_css'] ){
			$uploads = wp_get_upload_dir();
			if( file_exists( $uploads['basedir'] . '/bootstrap/bootstrap.min.css' ) ){
				$snapping_enqueued = true;
				$bootstrap = $this->get_theme_traversal_path( $uploads['basedir'] . '/bootstrap/bootstrap.min.css' );
			}else{
				$bootstrap = $this->get_theme_traversal_path( __DIR__ . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css' );
			}
			if( ! in_array( $bootstrap, $editor_styles ) ){
				array_unshift( $editor_styles, $bootstrap );
			}
		}

		if( ! $snapping_enqueued ){
			$snapping = $this->get_theme_traversal_path( __DIR__ . '/assets/css/snapping.css' );
			if( ! in_array( $snapping, $editor_styles ) ){
				array_unshift( $editor_styles, $snapping );
			}
		}
	}

	function plugin_menu_link(){
		$this->plugin_admin_page = add_submenu_page(
			'options-general.php',
			'Bootstrap Blocks',
			'Bootstrap Blocks',
			'manage_options',
			basename( __FILE__ ),
			array( $this, 'admin_options_page' )
		);
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'filter_plugin_actions' ), 10, 2 );
	}

	function filter_plugin_actions( $links, $file ){
		array_unshift( $links, '<a href="options-general.php?page=' . basename( __FILE__ ) . '">' . __( 'Settings', 'bootstrap' ) . '</a>' );
		return $links;
	}

	function plugin_admin_tabs( $current = 'general' ){
		$tabs = array( 'general' => __( 'Settings', 'bootstrap' ), 'bootstrap_config' => __( 'Bootstrap configuration', 'bootstrap' ), 'experiments' => __( 'Experiments', 'bootstrap' ) ); ?>
		<h2 class="nav-tab-wrapper">
		<?php foreach( $tabs as $tab => $name ){ ?>
			<a class="nav-tab <?php echo $tab == $current ? 'nav-tab-active' : '' ?>" href="?page=<?php echo basename( __FILE__ ) ?>&amp;tab=<?php echo esc_attr( $tab ) ?>">
				<?php echo esc_html( $name ) ?>
			</a>
		<?php } ?>
		</h2><br><?php
	}

	function admin_options_page(){
		if( get_current_screen()->id != $this->plugin_admin_page ) return;
		
		$this->tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

		$show_update_notice = false;
		if( isset( $_POST['settings_nonce'] ) && check_admin_referer( 'save_these_settings', 'settings_nonce' ) ){
			if( $this->tab == 'general' ){
				$settings = ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ? array() : $_POST['settings'];
				foreach( $this->settings as $key => $value ){
					$this->settings[ $key ] = isset( $settings[ $key ] ) && intval( $settings[ $key ] ) ? 1 : 0;
				}
				update_option( 'BootstrapBlocks_settings', $this->settings );
				$show_update_notice = true;
			}elseif( $this->tab == 'bootstrap_config' ){
				if( isset( $_POST['reset_settings'] ) ){
					delete_option( 'Bootstrap_config_options' );
					delete_option( 'Bootstrap_config_breakpoints' );
					delete_option( 'Bootstrap_config_container' );
					delete_option( 'Bootstrap_config_gutter' );
					delete_option( 'Bootstrap_config_scss_overrides' );
					delete_option( 'Bootstrap_config_imports' );
				}else{
					// options
					$options = ! isset( $_POST['options'] ) || ! is_array( $_POST['options'] ) ? array() : $_POST['options'];
					foreach( $this->config_options as $key => $value ){
						$this->config_options[ $key ] = isset( $options[ $key ] ) && intval( $options[ $key ] ) ? 1 : 0;
					}
					update_option( 'Bootstrap_config_options', $this->config_options );
					// breakpoints
					$this->config_breakpoints = array();
					foreach( $_POST['breakpoint']['size'] as $key => $size ){
						$size = intval( $size );
						$label = sanitize_text_field( $_POST['breakpoint']['label'][ $key ] );
						$prefix = sanitize_text_field( $_POST['breakpoint']['prefix'][ $key ] );
						$default = $_POST['breakpoint']['default'] == $label ? true : false;
						$this->config_breakpoints[ $size ] = array(
							'label' => $label,
							'prefix' => $prefix,
							'default' => $default,
						);
					}
					update_option( 'Bootstrap_config_breakpoints', $this->config_breakpoints );
					// containers
					$this->config_container = array();
					foreach( $_POST['container']['prefix'] as $key => $prefix ){
						$prefix = sanitize_text_field( $prefix );
						$size = intval( $_POST['container']['size'][ $key ] );
						$this->config_container[ $prefix ] = $size;
					}
					update_option( 'Bootstrap_config_container', $this->config_container );
					// gutter
					$this->config_gutter = sanitize_text_field( $_POST['gutter'] );
					update_option( 'Bootstrap_config_gutter', $this->config_gutter );
					// SCSS overrides
					$this->config_scss_overrides = sanitize_textarea_field( $_POST['scss_overrides'] );
					update_option( 'Bootstrap_config_scss_overrides', $this->config_scss_overrides );
					// imports
					$imports = ! isset( $_POST['imports'] ) || ! is_array( $_POST['imports'] ) ? array() : $_POST['imports'];
					foreach( $this->config_imports as $key => $value ){
						$this->config_imports[ $key ] = isset( $imports[ $key ] ) && intval( $imports[ $key ] ) ? 1 : 0;
					}
					update_option( 'Bootstrap_config_imports', $this->config_imports );
				}

				$this->regenerate_bootstrap_css();

				$show_update_notice = true;
			}elseif( $this->tab == 'experiments' ){
				$experiments = ! isset( $_POST['experiments'] ) || ! is_array( $_POST['experiments'] ) ? array() : $_POST['experiments'];
				foreach( $this->experiments as $key => $value ){
					$this->experiments[ $key ] = isset( $experiments[ $key ] ) && intval( $experiments[ $key ] ) ? 1 : 0;
				}
				update_option( 'BootstrapBlocks_experiments', $this->experiments );
				$show_update_notice = true;
			}
		} ?>
		<div class="wrap">
			<h2>Bootstrap Blocks</h2>
			<?php if( $show_update_notice ) echo '<div class="below-h2 updated"><p>' . __( 'Settings saved.', 'bootstrap' ) . '</p></div>'; ?>
			<form method="post" action="<?php echo admin_url( 'options-general.php?page=' . basename( __FILE__ ) . '&tab=' . $this->tab ) ?>"><?php
				wp_nonce_field( 'save_these_settings', 'settings_nonce' );
				$this->plugin_admin_tabs( $this->tab );
				switch( $this->tab ):
					case 'general':
						$this->tab_general_options();
						break;
					case 'bootstrap_config':
						$this->tab_bootstrap_config();
						break;
					case 'experiments':
						$this->tab_experiments();
						break;
				endswitch; ?>
			</form>
		</div><?php
	}

	function tab_general_options(){ ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th colspan="2" style="padding:0">
						<h3 style="margin:0"><?php _e( 'Bootstrap source', 'bootstrap' ) ?></h3>
					</th>
				</tr>
				<tr>
					<th>
						<label for="theme_css"><?php _e( "If your theme doesn't contain Bootstrap 5 css, then you need to load it from this plugin", 'bootstrap' ) ?></label>
					</th>
					<td>
						<label for="theme_css">
							<input type="checkbox" name="settings[theme_css]" value="1" id="theme_css" <?php echo checked( $this->settings['theme_css'] ) ?>>
							<?php _e( 'Load Bootstrap 5 css from this plugin in theme', 'bootstrap' ) ?>
						</label>
					</td>
				</tr>
				<tr>
					<th>
						<label for="theme_js"><?php _e( "If your theme doesn't contain Bootstrap 5 js, then you need to load it from this plugin", 'bootstrap' ) ?></label>
					</th>
					<td>
						<label for="theme_js">
							<input type="checkbox" name="settings[theme_js]" value="1" id="theme_js" <?php echo checked( $this->settings['theme_js'] ) ?>>
							<?php _e( 'Load Bootstrap 5 js from this plugin in theme', 'bootstrap' ) ?>
						</label>
					</td>
				</tr>
				<tr>
					<th>
						<label for="editor_css"><?php _e( "If your theme doesn't load Bootstrap 5 css in admin editor, then you need to load it from this plugin", 'bootstrap' ) ?></label>
					</th>
					<td>
						<label for="editor_css">
							<input type="checkbox" name="settings[editor_css]" value="1" id="editor_css" <?php echo checked( $this->settings['editor_css'] ) ?>>
							<?php _e( 'Load Bootstrap 5 css from this plugin in editor', 'bootstrap' ) ?>
						</label>
					</td>
				</tr>

				<tr>
					<th colspan="2">
						<hr>
					</th>
				</tr>
				
				<tr>
					<th colspan="2" style="padding:0">
						<h3><?php _e( 'Block Editor enhancements', 'bootstrap' ) ?></h3>
						<p class="notice notice-error notice-large"><?php _e( 'If you used this plugin before these options appeared in here, then leave them checked, otherwise it can break your layouts!', 'bootstrap' ) ?></p>
						<?php if( wp_theme_has_theme_json() ): ?>
							<p class="notice notice-info notice-large"><?php _e( 'You are using theme with theme.json and you can achieve best editing experience by disabling all options below.', 'bootstrap' ) ?></p>
						<?php endif ?>
					</th>
				</tr>
				<tr>
					<th>
						<label for="block_max_width"><?php _e( "Set block max width in editor to be equal to the container max width", 'bootstrap' ) ?></label>
					</th>
					<td>
						<label for="block_max_width">
							<input type="checkbox" name="settings[block_max_width]" value="1" id="block_max_width" <?php echo checked( $this->settings['block_max_width'] ) ?>>
							<?php _e( 'My blocks are wrapped in .container on frontend', 'bootstrap' ) ?>
						</label>
					</td>
				</tr>
				<tr>
					<th>
						<label for="container_class"><?php _e( "Add .container class on inner-container div in group block and cover block", 'bootstrap' ) ?></label>
					</th>
					<td>
						<label for="container_class">
							<input type="checkbox" name="settings[container_class]" value="1" id="container_class" <?php echo checked( $this->settings['container_class'] ) ?>>
							<?php _e( 'Automatically add .container class', 'bootstrap' ) ?>
						</label>
					</td>
				</tr>
				<tr>
					<th>
						<label for="remove_constrain_class"><?php _e( "Remove .is-layout-constrained class from fullwidth group block", 'bootstrap' ) ?></label>
					</th>
					<td>
						<label for="remove_constrain_class">
							<input type="checkbox" name="settings[remove_constrain_class]" value="1" id="remove_constrain_class" <?php echo checked( $this->settings['remove_constrain_class'] ) ?>>
							<?php _e( 'Automatically remove .is-layout-constrained class', 'bootstrap' ) ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" class="button button-primary button-large" value="<?php _e( 'Save', 'bootstrap' ) ?>"></p><?php
	}

	function tab_bootstrap_config(){ ?>
		<h3 style="margin:0"><?php _e( 'Options', 'bootstrap' ) ?></h3>
		<p>
			<?php _e( 'Quickly modify global styling by enabling or disabling optional features', 'bootstrap' ) ?><br>
			<a href="https://getbootstrap.com/docs/5.2/customize/options/" target="_blank"><?php _e( 'Read more', 'bootstrap' ) ?></a>
		</p>
		
		<table class="form-table">
			<tbody>
				<?php foreach( $this->config_options as $key => $value ): ?>
					<tr>
						<th>
							<label>
								<input type="checkbox" name="options[<?php echo esc_attr( $key ) ?>]" value="1" <?php echo checked( $value ) ?>>
								<?php echo esc_attr( $key ) ?>
							</label>
						</th>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>

		<br><hr>
		<h3 style="margin-bottom:0"><?php _e( 'Grid breakpoints', 'bootstrap' ) ?></h3>
		<p><?php _e( 'Define the minimum dimensions at which your layout will change, adapting to different screen sizes, for use in media queries', 'bootstrap' ) ?></p>
		<p class="notice notice-error notice-large"><?php _e( 'This should be set only on the very beginning before you start using Bootstrap row block in your posts.', 'bootstrap' ) ?><br><?php _e( 'Changing prefixes and adding or removing rows will break all existing Bootstrap row blocks!', 'bootstrap' ) ?></p>

		<table class="form-table" style="vertical-align:middle">
			<thead>
				<tr>
					<th></th>
					<th>
						<?php _e( 'label', 'bootstrap' ) ?>
					</th>
					<th>
						<?php _e( 'prefix', 'bootstrap' ) ?>
					</th>
					<th>
						<?php _e( 'size [px]', 'bootstrap' ) ?>
					</th>
					<th>
						<?php _e( 'default', 'astrum' ) ?>
					</th>
					<th style="width:100%;"></th>
				</tr>
			</thead>
			<tbody class="sortable">
				<?php $i = 0 ?>
				<?php foreach( $this->config_breakpoints as $breakpoint => $data ): ?>
					<tr>
						<th>
							<span class="dashicons dashicons-move"></span>
						</th>
						<th>
							<input type="text" name="breakpoint[label][]" value="<?php echo esc_attr( $data['label'] ) ?>">
						</th>
						<th>
							<input type="text" name="breakpoint[prefix][]" value="<?php echo esc_attr( $data['prefix'] ) ?>">
						</th>
						<th>
							<input type="number" name="breakpoint[size][]" value="<?php echo intval( $breakpoint ) ?>">
						</th>
						<th style="text-align:center">
							<input type="radio" name="breakpoint[default]" value="<?php echo esc_attr( $data['label'] ) ?>" <?php echo checked( $data['default'] ) ?>>
						</th>
						<th style="width:100%;">
							<span class="dashicons dashicons-remove"></span>
							<span class="dashicons dashicons-insert"></span>
						</th>
					</tr>
					<?php $i++ ?>
				<?php endforeach ?>
			</tbody>
		</table>
		<small><?php _e( '* label = what you will see in the editor in breakpoint tabs - you should use standard ones xs, sm, md, lg, xl, xxl', 'bootstrap' ) ?></small><br>
		<small><?php _e( '* prefix = what will be used in CSS .d-PREFIX-block, .col-PREFIX-6, ...', 'bootstrap' ) ?></small><br>
		<small><?php _e( '* default = what should be preselected when the editor loads', 'bootstrap' ) ?></small><br>

		<br><hr>
		<h3 style="margin-bottom:0"><?php _e( 'Grid containers', 'bootstrap' ) ?></h3>
		<p><?php _e( 'Define the maximum width (it is including gutter) of `.container` for different screen sizes', 'bootstrap' ) ?></p>

		<table class="form-table" style="vertical-align:middle">
			<thead>
				<tr>
					<th></th>
					<th>
						<?php _e( 'prefix', 'bootstrap' ) ?>
					</th>
					<th>
						<?php _e( 'size [px]', 'bootstrap' ) ?>
					</th>
					<th style="width:100%;"></th>
				</tr>
			</thead>
			<tbody class="sortable">
				<?php foreach( $this->config_container as $key => $value ): ?>
					<tr>
						<th>
							<span class="dashicons dashicons-move"></span>
						</th>
						<th>
							<input type="text" name="container[prefix][]" value="<?php echo esc_attr( $key ) ?>">
						</th>
						<th>
							<input type="number" name="container[size][]" value="<?php echo intval( $value ) ?>">
						</th>
						<th style="width:100%;">
							<span class="dashicons dashicons-remove"></span>
							<span class="dashicons dashicons-insert"></span>
						</th>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>

		<br><hr>
		<h3 style="margin-bottom:0"><?php _e( 'Grid gutter', 'bootstrap' ) ?></h3>
		<p><?php _e( 'Gutters are the padding between your columns, used to responsively space and align content in the Bootstrap grid system', 'bootstrap' ) ?></p>

		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<input type="text" name="gutter" value="<?php echo esc_attr( $this->config_gutter ) ?>">
					</th>
				</tr>
			</tbody>
		</table>

		<br><hr>
		<h3 style="margin-bottom:0"><?php _e( 'Other SCSS overrides', 'bootstrap' ) ?></h3>
		<p><?php printf( esc_html__( 'Here you can specify your %sSCSS variables%s replacements', 'bootstrap' ), '<a href="https://github.com/twbs/bootstrap/blob/main/scss/_variables.scss" target="_blank">', '</a>' ) ?></p>
		<p class="notice notice-error notice-large"><?php _e( 'Proceed with caution: Recommended for experienced users who are familiar with the intricacies of this feature.', 'bootstrap' ) ?></p>

		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<textarea name="scss_overrides" placeholder="$spacer: 1rem;" class="description-wide" style="height:100px"><?php echo sanitize_textarea_field( $this->config_scss_overrides ) ?></textarea>
					</th>
				</tr>
			</tbody>
		</table>

		<br><hr>
		<h3 style="margin-bottom:0"><?php _e( 'Imports', 'bootstrap' ) ?></h3>
		<p><?php _e( 'Make sure you optimize Bootstrap by only @importing the components you need', 'bootstrap' ) ?></p>

		<table class="form-table">
			<tbody>
				<?php foreach( $this->config_imports as $key => $value ): ?>
					<tr>
						<th>
							<label>
								<input type="checkbox" name="imports[<?php echo esc_attr( $key ) ?>]" value="1" <?php echo checked( $value ) ?>>
								<?php echo esc_attr( $key ) ?>
								<?php if( isset( $this->config_imports_helpers[ $key ] ) ): ?>
									<small class="helper"> - <?php echo esc_html( $this->config_imports_helpers[ $key ] ) ?></small>
								<?php endif ?>
							</label>
						</th>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" class="button button-primary button-large" value="<?php _e( 'Save and recompile', 'bootstrap' ) ?>">
			&emsp;
			<input type="submit" class="button button-large button-link-delete" name="reset_settings" value="<?php _e( 'Reset settings and recompile', 'bootstrap' ) ?>">
		</p>

		<style>
		.form-table :is(th,td){
			padding: 4px 0;
			vertical-align: middle;
		}
		.dashicons-move{
			cursor: pointer;
			margin-right: 6px;
		}
		.dashicons-remove,
		.dashicons-insert{
			cursor: pointer;
			margin-left: 4px;
		}
		.dashicons-remove{
			margin-left: 8px;
		}
		small.helper{
			opacity: 0;
			transition: opacity .2s ease;
			font-weight: 400;
		}
		h3:hover + p + table small.helper,
		label:hover small.helper{
			opacity: 1;
		}
		</style><?php

		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script( 'bootstrap-admin-config', plugins_url( '/assets/js/bootstrap-admin-config.js', __FILE__ ), 'jquery-ui-sortable', 1, 1 );
	}

	function tab_experiments(){ ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th colspan="3" style="padding:0">
						<h3 style="margin:0"><?php _e( 'Experiments', 'bootstrap' ) ?></h3>
					</th>
				</tr>
				<tr>
					<td colspan="3">
						<p class="notice notice-error notice-large">
							You should NOT test these experiments on the live production website
							<br>
							Please, let us know if you will find some bugs or problems
						</p>
					</td>
				</tr>
				<tr>
					<th>
						<label for="js_parts"><?php _e( 'Load only needed JS files instead of full bootstrap.bundle.min.js', 'bootstrap' ) ?></label>
					</th>
					<td>
						<label for="js_parts">
							<input type="checkbox" name="experiments[js_parts]" value="1" id="js_parts" <?php echo checked( $this->experiments['js_parts'] ) ?>>
							<?php _e( 'Load JS parts of Bootstrap', 'bootstrap' ) ?>
						</label>
					</td>
					<td>
						<ul>
							<li>
								- you should have some aggregation and minification plugin to achieve the best performance with this option ( eg. Autoptimize, Asset CleanUp or some Cache plugin with these functionalities )
							</li>
							<li>
								- you can use bootstrap_js_parts filter to return only needed parts array [alert, button, carousel, collapse, dropdown, modal, offcanvas, scrollspy, tab, toast, tooltip, popover]
							</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th></th>
					<td colspan="2">
						<h4>Bootstrap JS parts</h4>
						<?php foreach( [ 'alert', 'button', 'carousel', 'collapse', 'dropdown', 'modal', 'offcanvas', 'scrollspy', 'tab', 'toast', 'tooltip', 'popover' ] as $js_part ): ?>
							<label for="js-<?php echo $js_part ?>">
								<input type="checkbox" name="experiments[js-<?php echo $js_part ?>]" value="1" id="js-<?php echo $js_part ?>" <?php echo checked( $this->experiments[ 'js-' . $js_part ] ) ?>>
								<?php echo $js_part ?>
							</label><br>
						<?php endforeach ?>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" class="button button-primary button-large" value="<?php _e( 'Save', 'bootstrap' ) ?>"></p><?php
	}

	function regenerate_bootstrap_css(){
		include_once 'compiler.php';
	}

	function wp_enqueue_scripts(){

		if( $this->settings['theme_js'] && ! $this->experiments['js_parts'] ){
			wp_enqueue_script( 'bootstrap', plugins_url( 'vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js', __FILE__ ), array(), '5.2.3', true );
		}
		
		// bo: experiments
		if( $this->experiments['js_parts'] ){

			$js_parts = [];
			if( $this->experiments['js-alert'] ) $js_parts[] = 'alert';
			if( $this->experiments['js-button'] ) $js_parts[] = 'button';
			if( $this->experiments['js-carousel'] ) $js_parts[] = 'carousel';
			if( $this->experiments['js-collapse'] ) $js_parts[] = 'collapse';
			if( $this->experiments['js-dropdown'] ) $js_parts[] = 'dropdown';
			if( $this->experiments['js-modal'] ) $js_parts[] = 'modal';
			if( $this->experiments['js-offcanvas'] ) $js_parts[] = 'offcanvas';
			if( $this->experiments['js-scrollspy'] ) $js_parts[] = 'scrollspy';
			if( $this->experiments['js-tab'] ) $js_parts[] = 'tab';
			if( $this->experiments['js-toast'] ) $js_parts[] = 'toast';
			if( $this->experiments['js-tooltip'] ) $js_parts[] = 'tooltip';
			if( $this->experiments['js-popover'] ) $js_parts[] = 'popover';

			$js_parts = apply_filters( 'bootstrap_js_parts', $js_parts );

			if( ! empty( $js_parts ) ){
				
				if( count( array_intersect( [ 'tooltip', 'popover', 'dropdown' ], $js_parts ) ) ){
					wp_enqueue_script( 'popper', plugins_url( 'assets/js/popper.min.js', __FILE__ ), [], '2.11.8', false );
					wp_add_inline_script( 'popper', 'globalThis["@popperjs/core"]=Popper' );
				}

				wp_enqueue_script( 'bs-util-index', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/index.js', __FILE__ ), [], '5.2.3', false );
				wp_enqueue_script( 'bs-data', plugins_url( 'vendor/twbs/bootstrap/js/dist/dom/data.js', __FILE__ ), ['bs-util-index'], '5.2.3', false );
				wp_enqueue_script( 'bs-dom-event-handler', plugins_url( 'vendor/twbs/bootstrap/js/dist/dom/event-handler.js', __FILE__ ), ['bs-data'], '5.2.3', false );
				wp_enqueue_script( 'bs-dom-manipulator', plugins_url( 'vendor/twbs/bootstrap/js/dist/dom/manipulator.js', __FILE__ ), ['bs-dom-event-handler'], '5.2.3', false );
				wp_enqueue_script( 'bs-dom-selector-engine', plugins_url( 'vendor/twbs/bootstrap/js/dist/dom/selector-engine.js', __FILE__ ), ['bs-dom-manipulator'], '5.2.3', false );
				wp_enqueue_script( 'bs-util-config', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/config.js', __FILE__ ), ['bs-dom-selector-engine'], '5.2.3', false );
				wp_enqueue_script( 'bs-base-component', plugins_url( 'vendor/twbs/bootstrap/js/dist/base-component.js', __FILE__ ), ['bs-util-config'], '5.2.3', false );

				wp_add_inline_script( 'bs-base-component', 'bootstrap={}' );

				foreach( $js_parts as $js_part ){
					switch( $js_part ){
						case 'alert':
							wp_enqueue_script( 'bs-util-component-functions', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/component-functions.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_enqueue_script( 'bs-alert', plugins_url( 'vendor/twbs/bootstrap/js/dist/alert.js', __FILE__ ), ['bs-util-component-functions'], '5.2.3', false );
							wp_add_inline_script( 'bs-alert', 'bootstrap.Alert=Alert' );
							break;
						case 'button':
							wp_enqueue_script( 'bs-button', plugins_url( 'vendor/twbs/bootstrap/js/dist/button.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_add_inline_script( 'bs-button', 'bootstrap.Button=Button' );
							break;
						case 'carousel':
							wp_enqueue_script( 'bs-util-swipe', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/swipe.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_enqueue_script( 'bs-carousel', plugins_url( 'vendor/twbs/bootstrap/js/dist/carousel.js', __FILE__ ), ['bs-util-swipe'], '5.2.3', false );
							wp_add_inline_script( 'bs-carousel', 'bootstrap.Carousel=Carousel' );
							break;
						case 'collapse':
							wp_enqueue_script( 'bs-collapse', plugins_url( 'vendor/twbs/bootstrap/js/dist/collapse.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_add_inline_script( 'bs-collapse', 'bootstrap.Collapse=Collapse' );
							break;
						case 'dropdown':
							wp_enqueue_script( 'bs-dropdown', plugins_url( 'vendor/twbs/bootstrap/js/dist/dropdown.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_add_inline_script( 'bs-dropdown', 'bootstrap.Dropdown=Dropdown' );
							break;
						case 'modal':
							wp_enqueue_script( 'bs-util-backdrop', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/backdrop.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_enqueue_script( 'bs-util-component-functions', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/component-functions.js', __FILE__ ), ['bs-util-backdrop'], '5.2.3', false );
							wp_enqueue_script( 'bs-util-focustrap', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/focustrap.js', __FILE__ ), ['bs-util-component-functions'], '5.2.3', false );
							wp_enqueue_script( 'bs-util-scrollbar', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/scrollbar.js', __FILE__ ), ['bs-util-focustrap'], '5.2.3', false );
							wp_enqueue_script( 'bs-modal', plugins_url( 'vendor/twbs/bootstrap/js/dist/modal.js', __FILE__ ), ['bs-util-scrollbar'], '5.2.3', false );
							wp_add_inline_script( 'bs-modal', 'bootstrap.Modal=Modal' );
							break;
						case 'offcanvas':
							wp_enqueue_script( 'bs-util-backdrop', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/backdrop.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_enqueue_script( 'bs-util-component-functions', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/component-functions.js', __FILE__ ), ['bs-util-backdrop'], '5.2.3', false );
							wp_enqueue_script( 'bs-util-focustrap', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/focustrap.js', __FILE__ ), ['bs-util-component-functions'], '5.2.3', false );
							wp_enqueue_script( 'bs-util-scrollbar', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/scrollbar.js', __FILE__ ), ['bs-util-focustrap'], '5.2.3', false );
							wp_enqueue_script( 'bs-offcanvas', plugins_url( 'vendor/twbs/bootstrap/js/dist/offcanvas.js', __FILE__ ), ['bs-util-scrollbar'], '5.2.3', false );
							wp_add_inline_script( 'bs-offcanvas', 'bootstrap.Offcanvas=Offcanvas' );
							break;
						case 'scrollspy':
							wp_enqueue_script( 'bs-scrollspy', plugins_url( 'vendor/twbs/bootstrap/js/dist/scrollspy.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_add_inline_script( 'bs-scrollspy', 'bootstrap.Scrollspy=Scrollspy' );
							break;
						case 'tab':
							wp_enqueue_script( 'bs-tab', plugins_url( 'vendor/twbs/bootstrap/js/dist/tab.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_add_inline_script( 'bs-tab', 'bootstrap.Tab=Tab' );
							break;
						case 'toast':
							wp_enqueue_script( 'bs-util-component-functions', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/component-functions.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_enqueue_script( 'bs-toast', plugins_url( 'vendor/twbs/bootstrap/js/dist/toast.js', __FILE__ ), ['bs-util-component-functions'], '5.2.3', false );
							wp_add_inline_script( 'bs-toast', 'bootstrap.Toast=Toast' );
							break;
						case 'tooltip':
							wp_enqueue_script( 'bs-util-sanitizer', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/sanitizer.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_enqueue_script( 'bs-util-template-factory', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/template-factory.js', __FILE__ ), ['bs-util-sanitizer'], '5.2.3', false );
							wp_enqueue_script( 'bs-tooltip', plugins_url( 'vendor/twbs/bootstrap/js/dist/tooltip.js', __FILE__ ), ['bs-util-template-factory'], '5.2.3', false );
							wp_add_inline_script( 'bs-tooltip', 'bootstrap.Tooltip=Tooltip' );
							break;
						case 'popover':
							wp_enqueue_script( 'bs-util-sanitizer', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/sanitizer.js', __FILE__ ), ['bs-base-component'], '5.2.3', false );
							wp_enqueue_script( 'bs-util-template-factory', plugins_url( 'vendor/twbs/bootstrap/js/dist/util/template-factory.js', __FILE__ ), ['bs-util-sanitizer'], '5.2.3', false );
							wp_enqueue_script( 'bs-tooltip', plugins_url( 'vendor/twbs/bootstrap/js/dist/tooltip.js', __FILE__ ), ['bs-util-template-factory'], '5.2.3', false );
							wp_add_inline_script( 'bs-tooltip', 'bootstrap.Tooltip=Tooltip' );
							wp_enqueue_script( 'bs-popover', plugins_url( 'vendor/twbs/bootstrap/js/dist/popover.js', __FILE__ ), ['bs-tooltip'], '5.2.3', false );
							wp_add_inline_script( 'bs-popover', 'bootstrap.Popover=Popover' );
							break;
					}
				}
			}
		}
		// eo: experiments
		
		$snapping_enqueued = false;
		if( $this->settings['theme_css'] ){
			$uploads = wp_get_upload_dir();
			if( file_exists( $uploads['basedir'] . '/bootstrap/bootstrap.min.css' ) ){
				$snapping_enqueued = true;
				wp_enqueue_style( 'bootstrap', $uploads['baseurl'] . '/bootstrap/bootstrap.min.css', array(), '5.2.3' );
			}else{
				wp_enqueue_style( 'bootstrap', plugins_url( 'vendor/twbs/bootstrap/dist/css/bootstrap.min.css', __FILE__ ), array(), '5.2.3' );
			}
		}

		if( ! $snapping_enqueued ){
			wp_enqueue_style( 'snapping', plugins_url( 'assets/css/snapping.css', __FILE__ ), array(), '5.2.3' );
		}
	}

	function wp_enqueue_scripts_override(){
		wp_enqueue_style( 'bootstrap-blocks', plugins_url( 'build/style-index.css', __FILE__ ), array(), filemtime( plugin_dir_path( __FILE__ ) . 'build/style-index.css' ) );
	}
}

register_activation_hook( __FILE__, function(){
	add_option( 'BootstrapBlocksActivated', 1 );
});

add_action( 'plugins_loaded', function(){
	return BootstrapBlocks::instance();
});