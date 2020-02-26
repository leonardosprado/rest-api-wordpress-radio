<?php

function api_promocao_post($request){
    $user = wp_get_current_user();
    $user_id =  $user->ID;
    if($user_id > 0){
        $titulo     = sanitize_text_field($request['titulo']);
        $texto_alt  = sanitize_text_field($request['texto_alt']);
        $conteudo   = $request['conteudo'];
        $banner     = sanitize_text_field($request['banner']);
        $data_ini   = sanitize_text_field($request['data_ini']);
        $data_fim   = sanitize_text_field($request['data_fim']);
        $status     = sanitize_text_field( $request['status']);
        $filial     = sanitize_text_field( $request['filial']);
        $media     =  $request['media'];

        $usuario_id =  $user->user_login;
        $response   = array(
            'post_author'   =>$user_id,
            'post_type'     => 'promocao',
            'post_title'    => $titulo,
            'post_status'   => 'publish',
            'meta_input'    => array(
                'conteudo'                  => $conteudo,
                '_wp_attachment_image_alt'  => $texto_alt,
                'banner'                    => $banner,
                'data_ini'                  => $data_ini,
                'data_fim'                  => $data_fim,
                'status'                    => $status,
                'filial'                    => $filial,
                'media'                     => $media,
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
    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
   
    return rest_ensure_response($response);
}

function registrar_api_promocao_post(){

    register_rest_route('api', '/promocao', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_promocao_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_promocao_post');


?>