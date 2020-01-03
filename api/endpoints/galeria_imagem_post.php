<?php

function api_galeria_imagem_post($request){
    $user = wp_get_current_user();
    $user_id =  $user->ID;
    if($user_id > 0){

        $titulo         =  sanitize_text_field($request['titulo']);
        $descricao      =  sanitize_text_field($request['descricao']);
        $data           =  sanitize_text_field($request['data']);
        $filial         =  sanitize_text_field($request['filial']);
        
        $usuario_id  =  $user->user_login;
        $response    = array(
            'post_author' =>$user_id,
            'post_type' => 'galeria_fotos',
            'post_title' => $titulo,
            'post_status' => 'publish',
            'meta_input' => array(
                'nome' => $nome,
                'titulo' =>$titulo,
                'descricao' =>$descricao,
                'cover' =>''
            )
        );
        $imagem_id = wp_insert_post($response);
        
        $response['id'] = get_post_field('post_name',$imagem_id);

        $files = $request->get_file_params();

        if($files){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            foreach($files as $file => $array){
                media_handle_upload($file, $imagem_id);
            }
        }

        $images = get_attached_media('image', $imagem_id);
        $cover = null;
        $images_array = null;
        if($images){
            $images_array = array();
            foreach($images as $key => $value ){
                $images_array[] = array(
                    'ID' => $value->ID,
                    'src' => $value->guid,
                );
            }
        }

        update_post_meta($imagem_id, 'cover', $images_array[0]);
        update_post_meta($imagem_id, 'images', $images_array);


        $response  = array(
            'post_author' =>$user_id,
            'post_type' => 'galeria_fotos',
            'post_title' => $titulo,
            'post_status' => 'publish',
            'meta_input' => array(
                'nome' => $nome,
                'titulo' =>$titulo,
                'descricao' =>$descricao,
                'filial'    => $filial,
                'cover' => $images_array[0],
                'images' => $images_array,
            )
        );
    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
   
    return rest_ensure_response($response);
}

function registrar_api_galeria_imagem_post(){

    register_rest_route('api', '/galeria-imagem', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_galeria_imagem_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_galeria_imagem_post');


?>