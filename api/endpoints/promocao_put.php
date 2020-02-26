<?php

function api_promocao_put($request){

    $slug = $request['slug'];

    $user = wp_get_current_user();

    $promo_id = get_promocao_id_slug($slug);

    $user_id =  $user->ID;
    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);

    if ($user_id > 0 and ($roles === "administrator" or $roles === "editor" or $roles === "author")) {


        $titulo      =       sanitize_text_field($request['titulo']);
        // $texto_alt   =       sanitize_text_field($request['texto_alt']);
        $status      =       sanitize_text_field($request['status']);
        $source_url  =       sanitize_text_field($request['source_url']);
        $conteudo    =       ($request['conteudo']);
        $data_ini    =       sanitize_text_field($request['data_ini']);
        $data_fim    =       sanitize_text_field($request['data_fim']);
        $filial      =       sanitize_text_field($request['filial']);
        $media       =       $request['media'];

        

        // $files = $request->get_file_params();

        // if($files){
        //     $images = get_attached_media('image', $imagem_id);
        //     if($images){
        //         foreach($images as $key => $value){
        //             wp_delete_attachment($value->ID, true);
        //         }
        //     }

        //     require_once(ABSPATH . 'wp-admin/includes/image.php');
        //     require_once(ABSPATH . 'wp-admin/includes/file.php');
        //     require_once(ABSPATH . 'wp-admin/includes/media.php');

        //     foreach($files as $file => $array){
        //         media_handle_upload($file, $imagem_id);
        //     }
        // }

        
        $usuario_id  =       $user->user_login;
        $response    =   array(
            'ID' =>  $promo_id,
            'post_author'   =>  $user_id,
            'post_type'     => 'promocao',
            'post_title'    =>  $titulo,
            'post_status'   => 'publish',
        );

        wp_update_post($response);
        update_post_meta($promo_id, 'conteudo', $conteudo);
        update_post_meta($promo_id, 'data_ini', $data_ini);
        update_post_meta($promo_id, 'data_fim', $data_fim);
        update_post_meta($promo_id, 'status', $status);
        update_post_meta($promo_id, 'source_url', $source_url);
        update_post_meta($promo_id, 'filial', $filial);
        update_post_meta($promo_id, 'media', $media);




        // update_post_meta($imagem_id, '_wp_attachment_image_alt', $texto_alt);
        // add_post_meta($imagem_id, '_wp_attachment_image_alt', $meta_value, $unique);


    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response);
}

function registrar_api_promocao_put(){

    register_rest_route('api', '/promocao/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_promocao_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_promocao_put');


?>