<?php

function api_galeria_video_post($request){
    $user = wp_get_current_user();
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
            'post_author' =>$user_id,
            'post_type' => 'galeria_video',
            'post_title' => $titulo,
            'post_status' => 'publish',
            'meta_input' => array(
                'nome_musica'   => $nome_musica,
                'cantor'        => $cantor,
                'url_embed'     => $url_embed,
                'descricao'     => $descricao,
                'data'          => $data,
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
                    'src' => $value->guid,
                );
            }
        }

        update_post_meta($imagem_id, 'cover', $images_array[0]);


        $response  = array(
            'post_author' =>$user_id,
            'post_type' => 'galeria_video',
            'post_title' => $titulo,
            'post_status' => 'publish',
            'meta_input' => array(
                'nome_musica'   => $nome_musica,
                'cantor'        => $cantor,
                'url_embed'     => $url_embed,
                'descricao'     => $descricao,
                'cover'         => $images_array[0],
            )
        );
    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
   
    return rest_ensure_response($response);
}

function registrar_api_galeria_video_post(){

    register_rest_route('api', '/galeria-video', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_galeria_video_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_galeria_video_post');


?>