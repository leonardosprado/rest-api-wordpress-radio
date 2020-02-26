<?php

function api_top_musica_put($request){

    $slug = $request['slug'];  //Pega o SLUG : ID

    $user = wp_get_current_user();  //PEGA O USUÁRIO LOGADO NO MOMENTO
 
    $post_id = get_top_musica_id_slug($slug); //PEGAO ID DO POST

    $user_id =  $user->ID;
    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);

    // Verifica se o Usuário tem permissão. 
    if ($user_id > 0 and ($roles === "administrator" or $roles === "editor" or $roles === "author")) {
        $titulo         = sanitize_text_field($request['titulo']);
        $nome_musica    = sanitize_text_field($request['nome_musica']);
        $artista        = sanitize_text_field($request['artista']);
        $posicao        = sanitize_text_field($request['posicao']);
        $texto_alt      = sanitize_text_field($request['texto_alt']);
        $cover      = $request['cover'];


        $usuario_id =  $user->user_login;
        $response   = array(
            'ID'            =>  $post_id,
            'post_author'   =>  $user_id,
            'post_type'     =>  'topmusica',
            'post_title'    =>  $titulo,
            'post_status'   =>  'publish',
        );

        wp_update_post($response);
        update_post_meta($post_id, 'titulo', $titulo);
        update_post_meta($post_id, 'nome_musica', $nome_musica);
        update_post_meta($post_id, 'artista', $artista);
        update_post_meta($post_id, 'posicao', $posicao);
        update_post_meta($post_id, 'texto_alt', $texto_alt);
        update_post_meta($post_id, 'cover', $cover);
        // update_post_meta($post_id, 'logo_programa', $logo_programa);

    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response);
}

function registrar_api_top_musica_put(){

    register_rest_route('api', '/musica/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_top_musica_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_top_musica_put');


?>