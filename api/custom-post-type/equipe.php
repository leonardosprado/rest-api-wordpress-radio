<?php 

function registrar_cpt_equipe(){
    register_post_type('equipe', array(
        'label' => 'Equipe',
        'description ' => 'Equipe',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'equipe', 'with_front' => true),
        'query_var' => true,
        'supports' => array('custom=fields','author','title','content'),
        'publicly_queryable' => true
    ));
}

add_action('init','registrar_cpt_equipe');

?>