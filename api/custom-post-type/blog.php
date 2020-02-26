<?php 

function registrar_cpt_blog(){

    register_post_type('blog', array(
        'label' => 'Blog',
        'description ' => 'Blog',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'blog', 'with_front' => true),
        'query_var' => true,
        'supports' => array('custom=fields','author','title','content'),
        'publicly_queryable' => true,
        'show_in_rest'          => true,
        'rest_base'             => 'myslug',
        'rest_controller_class' => 'WP_REST_Posts_Controller'
    ));
}

add_action('init','registrar_cpt_blog');

?>