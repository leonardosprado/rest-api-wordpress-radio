<?php

function api_equipe_post($request){
    $user = wp_get_current_user();
    $user_id =  $user->ID;

    if($user_id > 0){
        $titulo             = sanitize_text_field($request['titulo']);
        $nome               = sanitize_text_field($request['nome']);
        $sobrenome          = sanitize_text_field($request['sobrenome']);
        $filial             = sanitize_text_field($request['filial']);
        $nome_programa      = sanitize_text_field($request['nome_programa']);
        $cidade             = sanitize_text_field($request['cidade']);
        $estado             = sanitize_text_field($request['estado']);
        $texto_alt          = sanitize_text_field($request['texto_alt']);

        $usuario_id =  $user->user_login;
        $response   = array(
            'post_author'   => $user_id,
            'post_type'     => 'equipe',
            'post_title'    => $titulo,
            'post_status'   => 'publish',
            'meta_input'    => array(
                'nome'                      => $nome,
                'sobrenome'                 => $sobrenome,
                'filial'                    => $filial,
                'nome_programa'             => $nome_programa,
                'cidade'                    => $cidade,
                'estado'                    => $estado,
                '_wp_attachment_image_alt'  => $texto_alt,
            )
        );

        $post_id = wp_insert_post($response);
        $response['id'] = get_post_field('post_name',$post_id);

        $files = $request->get_file_params();

        if($files){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            foreach($files as $file => $array){
                media_handle_upload($file, $post_id);
            }
        }
    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
   
    return rest_ensure_response($response);
}

function registrar_api_equipe_post(){

    register_rest_route('api', '/equipe', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_equipe_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_equipe_post');


?>