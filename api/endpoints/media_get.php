<?php

function media_scheme($slug){

    $post_id = get_media_id_slug($slug);
    
    if($post_id){
        $post = get_post($post_id);
        $post_meta = get_post_meta($post_id);

        $images = get_attached_media('image', $post_id);

        $images_array = null;

        if($images){
            $images_array = array();
            foreach($images as $key => $value ){
                $images_array[] = array(
                    'ID' => $value->ID,
                    'titulo' => $value->post_name,
                    'src' => $value->guid,
                );
            }
        }

        $response = array(
            'id' => $post_id,
            'slug' => $post->post_name,
            "titulo" => $post->post_title,
            "nome"  => $post->post_name,
            "media" => $images_array[0],
        
        );

    }
    else{ 
        $response = new WP_Error('naoexiste','Media não Encontrada',array('status'=>404));
    }

    return $response;
}


//API IMAGENS 
function api_media_get($request){

    $q = sanitize_text_field($request['q']) ?:'';
    $_page = sanitize_text_field($request['_page']) ?:0;
    $_limit = sanitize_text_field($request['_limit']) ?: -1;
    $usuario_id = sanitize_text_field($usuario_id);

    $usuario_id_query = null;
    if($usuario_id){
        $usuario_id_query = array(
            'key' => 'usuario_id',
            'value' => $usuario_id,
            'compare' => '='
        );
    }


    $query = array(
        'post_type'=>'imagem',
        'posts_per_page' => $_limit,
        'paged' => $_page,
        's' =>$q,
        'meta_query' => array(
            $usuario_id_query,
        )
    );

    $loop = new WP_Query($query);
    $posts = $loop->posts;
    $total = $loop->found_posts;

    $imagens = array();

    foreach($posts as $key => $value){
        $imagens[] = media_scheme($value->post_name);
    }

    $response = rest_ensure_response($imagens);
    $response->header('X-Total-Count',$total);
    return ($response);
    
}


function registrar_api_media_get(){

    register_rest_route('api', '/media', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_media_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_media_get');



?>