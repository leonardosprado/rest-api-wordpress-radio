<?php

function api_programacao_put($request){

    $slug = $request['slug'];

    $user = wp_get_current_user();

    $post_id = get_programacao_id_slug($slug);

    $user_id =  $user->ID;
    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);

    if ($user_id > 0 and ($roles === "administrator" or $roles === "editor" or $roles === "author")) {
        $titulo             = sanitize_text_field($request['titulo']);
        $apresentacao       = sanitize_text_field($request['apresentacao']);
        $dia_semana         = sanitize_text_field($request['dia_semana']);
        $hora_ini           = sanitize_text_field($request['hora_ini']);
        $hora_fim           = sanitize_text_field($request['hora_fim']);
        $filial             = sanitize_text_field($request['filial']);
        $cidade             = sanitize_text_field($request['cidade']);
        $estado             = sanitize_text_field($request['estado']);
        $imagem_programa    = $request['imagem_programa'];
        $logo_programa      = $request['logo_programa'];

        $usuario_id =  $user->user_login;
        $response   = array(
            'ID'            =>  $post_id,
            'post_author'   =>  $user_id,
            'post_type'     =>  'programacao',
            'post_title'    =>  $titulo,
            'post_status'   =>  'publish',
        );

        wp_update_post($response);
        update_post_meta($post_id, 'apresentacao', $apresentacao);
        update_post_meta($post_id, 'dia_semana', $dia_semana);
        update_post_meta($post_id, 'hora_ini', $hora_ini);
        update_post_meta($post_id, 'filial', $filial);
        update_post_meta($post_id, 'cidade', $cidade);
        update_post_meta($post_id, 'estado', $estado);
        update_post_meta($post_id, 'imagem_programa', $imagem_programa);
        update_post_meta($post_id, 'logo_programa', $logo_programa);

    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response);
}

function registrar_api_programacao_put(){

    register_rest_route('api', '/programacao/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_programacao_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_programacao_put');


?>