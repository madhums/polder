<?php
require(WPMU_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php');
require(WPMU_PLUGIN_DIR . '/block-editor-bootstrap-blocks/block-editor-bootstrap-blocks.php');
require(WPMU_PLUGIN_DIR . '/breadcrumb-navxt/breadcrumb-navxt.php');


function my_display_attributes_filter($attribs, $types, $id)
{
    $extra_attribs = array('class' => array('breadcrumb-item'));
    //For the current item we need to add a little more info
    if(is_array($types) && in_array('current-item', $types))
    {
        $extra_attribs['class'][] = 'active';
        $extra_attribs['aria-current'] = array('page');
    }
    $atribs_array = array();
    preg_match_all('/([a-zA-Z]+)=["\']([a-zA-Z0-9\-\_ ]*)["\']/i', $attribs, $matches);
    if(isset($matches[1]))
    {
        foreach ($matches[1] as $key => $tag)
        {
            if(isset($matches[2][$key]))
            {
                $atribs_array[$tag] = explode(' ', $matches[2][$key]);
            }
        }
    }
    $merged_attribs = array_merge_recursive($atribs_array , $extra_attribs);
    $output = '';
    foreach($merged_attribs as $tag => $vals)
    {
        $output .= sprintf(' %1$s="%2$s"', $tag, implode(' ', $vals));
    }
    return $output;
}

add_filter('bcn_display_attributes', 'my_display_attributes_filter', 10, 3);
