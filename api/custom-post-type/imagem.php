<?php 

function registrar_cpt_imagem(){
    register_post_type('imagem', array(
        'label' => 'Imagem',
        'description ' => 'Imagem',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'imagem', 'with_front' => true),
        'query_var' => true,
        'supports' => array('custom=fields','author','title','content'),
        'publicly_queryable' => true
    ));
}

add_action('init','registrar_cpt_imagem');

?>