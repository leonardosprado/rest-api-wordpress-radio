<?php 

function registrar_cpt_promocao(){
    register_post_type('promocao', array(
        'label' => 'Promocao',
        'description ' => 'Promocao',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'promocao', 'with_front' => true),
        'query_var' => true,
        'supports' => array('custom=fields','author','title','content'),
        'publicly_queryable' => true
    ));
}

add_action('init','registrar_cpt_promocao');

?>