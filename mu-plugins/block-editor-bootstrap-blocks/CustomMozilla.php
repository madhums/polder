<?php

namespace Padaliyajay\PHPAutoprefixer\Vendor;

use Padaliyajay\PHPAutoprefixer\Vendor\Vendor;

class CustomMozilla extends Vendor {
    protected static $RULE_PROPERTY = array(
        'column-count' => '-moz-column-count',
        'column-gap' => '-moz-column-gap',
        'column-rule' => '-moz-column-rule',
        'user-select' => '-moz-user-select',
        'appearance' => '-moz-appearance',
        'font-feature-settings' => '-moz-font-feature-settings',
        'hyphens' => '-moz-hyphens',
    );

    protected static $RULE_VALUE = array(
        'width' => array( 'max-content' => '-moz-max-content' ),
    );
    
    protected static $PSEUDO = array(
        '::placeholder' => '::-moz-placeholder',
        ':placeholder-shown' => ':-moz-placeholder-shown',
    );
}

