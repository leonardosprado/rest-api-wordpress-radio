<?php 

function registrar_cpt_galeria_fotos(){
    register_post_type('galeria_fotos', array(
        'label' => 'Galeria_fotos',
        'description ' => 'Galeria_fotos',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'galeria_fotos', 'with_front' => true),
        'query_var' => true,
        'supports' => array('custom=fields','author','title','content'),
        'publicly_queryable' => true
    ));
}

add_action('init','registrar_cpt_galeria_fotos');

?>