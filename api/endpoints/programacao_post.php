<?php


function api_programacao_post($request){
    $user = wp_get_current_user();
    $user_id =  $user->ID;

    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);

    if ($user_id > 0 and ($roles === "administrator" or $roles === "editor" or $roles === "author")) {
        $titulo     = sanitize_text_field($request['titulo']);
        $apresentacao  = sanitize_text_field($request['apresentacao']);
        $dia_semana  = sanitize_text_field($request['dia_semana']);
        $hora_ini  = sanitize_text_field($request['hora_ini']);
        $hora_fim  = sanitize_text_field($request['hora_fim']);
        $filial  = sanitize_text_field($request['filial']);
        $cidade     = sanitize_text_field($request['cidade']);
        $estado   = sanitize_text_field($request['estado']);
        $imagem_programa   = $request['imagem_programa'];
        $logo_programa   = $request['logo_programa'];

        $usuario_id =  $user->user_login;
        $response   = array(
            'post_author'   =>$user_id,
            'post_type'     => 'programacao',
            'post_title'    => $titulo,
            'post_status'   => 'publish',
            'meta_input'    => array(
                'apresentacao'      => $apresentacao,
                'dia_semana'        => $dia_semana,
                'hora_ini'          => $hora_ini,
                'hora_fim'          => $hora_fim,
                'filial'            => $filial,
                'cidade'            => $cidade,
                'estado'            => $estado,
                'imagem_programa'   => $imagem_programa,
                'logo_programa'     => $logo_programa,
            )
        );
        $post_id = wp_insert_post($response);

        $response['id'] = get_post_field('post_name',$imagem_id);
        // $files = $request->get_file_params();

        // if($files){
        //     require_once(ABSPATH . 'wp-admin/includes/image.php');
        //     require_once(ABSPATH . 'wp-admin/includes/file.php');
        //     require_once(ABSPATH . 'wp-admin/includes/media.php');

        //     foreach($files as $file => $array){
        //         media_handle_upload($file, $imagem_id);
        //     }
        // }
    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
   
    return rest_ensure_response($response);
}

function registrar_api_programacao_post(){

    register_rest_route('api', '/programacao', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_programacao_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_programacao_post');


?>