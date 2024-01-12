<?php

namespace Padaliyajay\PHPAutoprefixer\Vendor;

use Padaliyajay\PHPAutoprefixer\Vendor\Vendor;

class CustomWebkit extends Vendor {
	protected static $RULE_PROPERTY = array(
		'backface-visibility' => '-webkit-backface-visibility',
		'box-reflect' => '-webkit-box-reflect',
		'clip-path' => '-webkit-clip-path',
		'column-count' => '-webkit-column-count',
		'column-gap' => '-webkit-column-gap',
		'column-rule' => '-webkit-column-rule',
		'flow-from' => '-webkit-flow-from',
		'flow-into' => '-webkit-flow-into',
		'font-feature-settings' => '-webkit-font-feature-settings',
		'hyphens' => '-webkit-hyphens',
		'margin-inline-end' => '-webkit-margin-end',
		'mask-image' => '-webkit-mask-image',
		'mask-size' => '-webkit-mask-size',
		'mask-position' => '-webkit-mask-position',
		'perspective' => '-webkit-perspective',
		'print-color-adjust' => '-webkit-print-color-adjust',
		'text-decoration-skip-ink' => '-webkit-text-decoration-skip-ink',
		'user-select' => '-webkit-user-select',
	);
	
	protected static $RULE_VALUE = array(
		'position' => array( 'sticky' => '-webkit-sticky' ),
		'width' => array( 'max-content' => '-webkit-max-content' ),
	);
	
	protected static $PSEUDO = array(
		'::placeholder' => '::-webkit-input-placeholder',
		'::file-selector-button' => '::-webkit-file-upload-button',
	);
	
	protected static $AT_RULE = array(
		// 'keyframes' => '-webkit-keyframes',
	);
}