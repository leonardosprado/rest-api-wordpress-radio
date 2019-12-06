
<?php 

function registrar_cpt_galeria_videos(){
    register_post_type('galeria_videos', array(
        'label' => 'Galeria_videos',
        'description ' => 'Galeria_videos',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'galeria_videos', 'with_front' => true),
        'query_var' => true,
        'supports' => array('custom=fields','author','title','content'),
        'publicly_queryable' => true
    ));
}

add_action('init','registrar_cpt_galeria_videos');

?>