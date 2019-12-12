<?php

function api_galeria_video_put($request){

    $slug = $request['slug'];

    $user = wp_get_current_user();
    $imagem_id = get_galeria_video_id_slug($slug);

    $user_id =  $user->ID;

    if($user_id > 0){

        $titulo             =  sanitize_text_field($request['titulo']);
        $data               =  sanitize_text_field($request['data']);
        $cantor             =  sanitize_text_field($request['cantor']);
        $nome_musica        =  sanitize_text_field($request['nome_musica']);
        $url_embed          =  sanitize_text_field($request['url_embed']);
        $descricao          =  sanitize_text_field($request['descricao']);

        $usuario_id  =  $user->user_login;
        $response    = array(
            'ID' =>  $imagem_id,
            'post_author' =>$user_id,
            'post_type' => 'galeria_video',
            'post_title' => $titulo,
            'post_status' => 'publish',
        );
        wp_update_post($response);
        update_post_meta($imagem_id, 'titulo',      $titulo);
        update_post_meta($imagem_id, 'cantor',      $cantor);
        update_post_meta($imagem_id, 'data',        $data);
        update_post_meta($imagem_id, 'nome_musica', $nome_musica);
        update_post_meta($imagem_id, 'url_embed',   $url_embed);
        update_post_meta($imagem_id, 'descricao',   $descricao);


    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response);
}

function registrar_api_galeria_video_put(){

    register_rest_route('api', '/galeria-video/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_galeria_video_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_galeria_video_put');


?>