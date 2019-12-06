<?php 

function registrar_cpt_promocao_cadastrada(){
    register_post_type('promocao_cadastrada', array(
        'label' => 'Promoção Cadastrada',
        'description ' => 'Promoção Cadastrada',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'promocao_cadastrada', 'with_front' => true),
        'query_var' => true,
        'supports' => array('custom=fields','author','title','content'),
        'publicly_queryable' => true
    ));
}

add_action('init','registrar_cpt_promocao_cadastrada');

?>