<?php 

function registrar_cpt_banner(){
    register_post_type('banner', array(
        'label' => 'Banner',
        'description ' => 'Banner',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'banner', 'with_front' => true),
        'query_var' => true,
        'supports' => array('custom=fields','author','title','content'),
        'publicly_queryable' => true
    ));
}

add_action('init','registrar_cpt_banner');

?>