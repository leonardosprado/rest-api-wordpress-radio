<?php

function api_imagem_post($request){
    $user = wp_get_current_user();
    $user_id =  $user->ID;
    //JSON 
    if($user_id > 0){
        $titulo       =  sanitize_text_field($request['titulo']);
        $content      =  ($request['post_content']);
        
        $usuario_id   =  $user->user_login;
        
        $response = array(
            'post_author'    => $user_id,           //Autor da postagem
            'post_status'    => 'publish',            //Status da Postagem.
            'post_type'      => 'imagem',         //Categoria da Postagem.
            'post_name'      => $titulo,            //Nome da postagem, normalmente é o proprio titulo
            //'post_modified'  = $post_modified     //Data que a postagem foi modidicada a ultima vez
            //'tags_input'     => $tags_input,      //Matriz de Tags         
        );

        $post_id = wp_insert_post($response);

        $files = $request->get_file_params();
        if($files){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            foreach($files as $file => $array){
                media_handle_upload($file, $post_id);
            }
        }
        
        $images = get_attached_media('image', $post_id);

        $images_array = null;

        if($images){
            $images_array = array();
            foreach($images as $key => $value ){
                $images_array[] = array(
                    'ID' => $value->ID,
                    'url' => $value->guid,
                );
            }
        }
        $response = array(
            "imagem" => $images_array[0],
        );

    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
   
    return rest_ensure_response($response);
}

function registrar_api_imagem_post(){

    register_rest_route('api', '/imagem', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_imagem_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_imagem_post');


?>