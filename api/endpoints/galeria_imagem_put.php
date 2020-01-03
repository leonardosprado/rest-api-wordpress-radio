<?php

function api_galeria_imagem_put($request){

    $slug = $request['slug'];

    $user = wp_get_current_user();
    $imagem_id = get_galeria_imagem_id_slug($slug);

    $user_id =  $user->ID;

    if($user_id > 0){

        $titulo         =  sanitize_text_field($request['titulo']);
        $descricao      =  sanitize_text_field($request['descricao']);
        $filial         =  sanitize_text_field($request['filial']);
        $cover          =  sanitize_text_field($request['cover']);
        $images         =  $request['images'];
        $usuario_id  =  $user->user_login;
        $response    = array(
            'ID' =>  $imagem_id,
            'post_author' =>$user_id,
            'post_type' => 'galeria_fotos',
            'post_title' => $titulo,
            'post_status' => 'publish',
        );
        wp_update_post($response);
        update_post_meta($imagem_id, 'titulo', $titulo);
        update_post_meta($imagem_id, 'descricao', $descricao);
        update_post_meta($imagem_id, 'filial', $filial);
        update_post_meta($imagem_id, 'images', $images);
        update_post_meta($imagem_id, 'cover', $images[0]);


    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response);
}

function registrar_api_galeria_imagem_put(){

    register_rest_route('api', '/galeria-imagem/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_galeria_imagem_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_galeria_imagem_put');


?>