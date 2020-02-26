<?php

function top_musica_scheme($slug){

    $post_id = get_top_musica_id_slug($slug);
    
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
            'slug' => $slug,
            'id' => $post->ID,
            "titulo" => $post->post_title,
            "capa" => $images_array[0],
            "nome_musica" =>$post_meta['nome_musica'][0],
            "artista" =>$post_meta['artista'][0],
            "posicao" =>$post_meta['posicao'][0],
            "texto_alternativo" =>$post_meta['_wp_attachment_image_alt'][0],            
        );

    }
    else{ 
        $response = new WP_Error('naoexiste','Musica não Encontrada',array('status'=>404));
    }

    return $response;
}





//RETORNAR TODAS AS MUSICAS
function api_top_musica_get($request){

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
        'post_type'=>'topmusica',
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

    $postagem = array();

    foreach($posts as $key => $value){
        $postagem[] = top_musica_scheme($value->post_name);
    }
   
    $response = rest_ensure_response($postagem);
    $response->header('X-Total-Count',$total);
    return ($response);
}


function registrar_api_top_musica_get(){

    register_rest_route('api', '/musica', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_top_musica_get',
        ),
    ));
}


add_action('rest_api_init','registrar_api_top_musica_get');


// Retornar Musica Pelo Seu ID 
function api_top_musica_id_get($request){
    
    $response = top_musica_scheme($request["slug"]);
    return rest_ensure_response($response);

}


function registrar_api_top_musica_id_get(){

    register_rest_route('api', '/musica/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_top_musica_id_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_top_musica_id_get');

?>