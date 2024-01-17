<?php
class TSBBlock{
	function __construct(){
		add_action( 'enqueue_block_assets', [$this, 'enqueueBockAssets'] );
		add_action( 'init', [$this, 'onInit'] );
	}

	function enqueueBockAssets(){
		wp_register_style( 'fontAwesome', TSB_DIR_URL . 'assets/css/fontAwesome.min.css', [], '5.15.4' );
	}

	function onInit() {
		wp_register_style( 'tsb-team-style', TSB_DIR_URL . 'dist/style.css', [ 'fontAwesome' ], TSB_VERSION ); // Style
		wp_register_style( 'tsb-team-editor-style', TSB_DIR_URL . 'dist/editor.css', [ 'tsb-team-style' ], TSB_VERSION ); // Backend Style

		register_block_type( __DIR__, [
			'editor_style'		=> 'tsb-team-editor-style',
			'style'				=> 'tsb-team-style',
			'render_callback'	=> [$this, 'render']
		] ); // Register Block

		wp_set_script_translations( 'tsb-team-editor-script', 'team-section', TSB_DIR_PATH . 'languages' );
	}

	function render( $attributes ){
		extract( $attributes );

		wp_enqueue_style( 'tsb-team-style' );
		wp_enqueue_script( 'tsb-team-script', TSB_DIR_URL . 'dist/script.js', [ 'react', 'react-dom' ], TSB_VERSION, true );
		wp_set_script_translations( 'tsb-team-script', 'team-section', TSB_DIR_PATH . 'languages' );

		$className = $className ?? '';
		$blockClassName = "wp-block-tsb-team $className align$align";

		ob_start(); ?>
		<div class='<?php echo esc_attr( $blockClassName ); ?>' id='tsbTeamMembers-<?php echo esc_attr( $cId ) ?>' data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ) ?>'></div>

		<?php return ob_get_clean();
	} // Render
}
new TSBBlock;