<?php

function filial_scheme($slug){

    $post_id = get_filial_id_slug($slug); 
    
    if($post_id){
        $post = get_post($post_id);
        $post_meta = get_post_meta($post_id);
        $images = get_attached_media('image', $post_id);

        $images_array = null;

        if($images){
            $images_array = array();
            foreach($images as $key => $value ){
                $images_array[] = array(
                    'src' => $value->guid,
                );
            }
        }


        $response = array(
            'id' => $post_id,
            "titulo" => $post->post_title,
            "slug" => $post->post_name,
            "filial" =>$post_meta['filial'][0],
            "cidade" =>$post_meta['cidade'][0],
            "estado" =>$post_meta['estado'][0],
            "streaming" =>$post_meta['streaming'][0],
            "logo" =>$images_array[0],
        );

    }
    else{ 
        $response = new WP_Error('naoexiste','Foto não Encontrada',array('status'=>404));
    }

    return $response;
}


//RETORNAR TODAS AS Filiais
function api_filial_get($request){

    $q = sanitize_text_field($request['q'])?:'';
    $_page = sanitize_text_field($request['_page'])?:0;
    $_limit = sanitize_text_field($request['_limit'])?:-1;
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
        'post_type'=>'filial',
        'post_per_page' => $_limit,
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
        $imagens[] = filial_scheme($value->post_name);
    }

    $response = rest_ensure_response($imagens);
    $response->header('X-Total-Count',$total);
    return ($response);
}


function registrar_api_filial_get(){

    register_rest_route('api', '/filial', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_filial_get',
        ),
    ));
}


add_action('rest_api_init','registrar_api_filial_get');


// Retornar Filial Pelo Seu ID 
function api_filial_id_get($request){
    
    $response = filial_scheme($request["slug"]);
    return rest_ensure_response($response);

}


function registrar_api_filial_id_get(){

    register_rest_route('api', '/filial/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_filial_id_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_filial_id_get');

?>