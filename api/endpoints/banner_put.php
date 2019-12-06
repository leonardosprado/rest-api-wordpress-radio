<?php

function api_banner_put($request){

    $slug = $request['slug'];

    $user = wp_get_current_user();
    $imagem_id = get_banner_id_slug($slug);

    $user_id =  $user->ID;

    if($user_id > 0){

        $titulo      =  sanitize_text_field($request['titulo']);
        $texto_alt   =  sanitize_text_field($request['texto_alt']);
        $status     =   sanitize_text_field( $request['status']);
        $usuario_id  =  $user->user_login;
        $response    = array(
            'post_author' =>$user_id,
            'post_type' => 'banner',
            'post_title' => $titulo,
            'post_status' => 'publish'
        );
        wp_update_post($response);
        update_post_meta($imagem_id, '_wp_attachment_image_alt', $texto_alt);
        update_post_meta($imagem_id, 'status', $status);
        
        add_post_meta($post_id, '_wp_attachment_image_alt', $meta_value, $unique);

    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response);
}

function registrar_api_banner_put(){

    register_rest_route('api', '/banner/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_banner_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_banner_put');


?>