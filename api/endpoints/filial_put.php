<?php

function api_filial_put($request){


    $slug = $request['slug'];

    $user = wp_get_current_user();
    $post_id = get_filial_id_slug($slug);

    $user_id =  $user->ID;
    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);

    if ($user_id > 0 and ($roles === "administrator")) {

        $titulo     = sanitize_text_field($request['titulo']);
        $cidade     = sanitize_text_field($request['cidade']);
        $estado     = sanitize_text_field($request['estado']);
        $post_name  = strtolower(tirarAcentos($cidade));
        $streaming  = sanitize_text_field($request['streaming']);


        $response = array(
            'ID'             => $post_id,
            'post_author'    => $user_id,           //Autor da postagem
            'post_title'     => $post_name,          //Titulo da Postagem
        );

        $update = wp_update_post($response);

        $files = $request->get_file_params();  //Recebe a Imagem em Destaque 
        
        if($files){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $images = get_attached_media('image', $post_id);
            if($images){
                foreach($images as $key => $value){
                    wp_delete_attachment($value->ID, true);
                }
            }


            foreach($files as $file => $array){
                media_handle_upload($file, $post_id); //Faz uplouad das imagem vinculado a Postagem
            }
        }



        $update = wp_update_post($response);

        update_post_meta($post_id, 'cidade', $cidade);
        update_post_meta($post_id, 'estado', $estado);
        update_post_meta($post_id, 'streaming', $streaming);
        
        // $files = $request->get_file_params();  //Recebe a Imagem em Destaque 

        // if($files){
        //     require_once(ABSPATH . 'wp-admin/includes/image.php');
        //     require_once(ABSPATH . 'wp-admin/includes/file.php');
        //     require_once(ABSPATH . 'wp-admin/includes/media.php');

        //     foreach($files as $file => $array){
        //         media_handle_upload($file, $blog_id); //Faz uplouad das imagem vinculado a Postagem
        //     }
        // }

    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response);
}

function registrar_api_filial_put(){

    register_rest_route('api', '/filial/edit/(?P<slug>[-\w]+)', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_filial_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_filial_put');


?>