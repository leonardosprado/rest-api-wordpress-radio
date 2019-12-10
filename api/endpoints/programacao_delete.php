<?php

//API Promocao DELETE
function api_programacao_delete($request){

    $slug = $request['slug'];

    $post_id = get_programacao_id_slug($slug);

    $user = wp_get_current_user();

    $author_id = (int) get_post_field('post_author', $imagem_id);
    $user_id = (int) $user->ID;

    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);

    if ($user_id > 0 and ($roles === "administrator" or $roles === "editor" or $roles === "author")) {
        $images = get_attached_media('image', $post_id);
        if($images){
            foreach($images as $key => $value){
                wp_delete_attachment($value->ID, true);
            }
        }
        $response = wp_delete_post($post_id, true);
        $reponse = "Apagado com sucesso";

    }else{
        $response = new WP_Error('permissao','Usuario nao possui permissão.', array('status'=> 401));
    }
    $response = rest_ensure_response($imagens);

    return ($response);
    
}


function registrar_api_programacao_delete(){

    register_rest_route('api', '/programacao/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => 'api_programacao_delete',
        ),
    ));
}

add_action('rest_api_init','registrar_api_programacao_delete');



?>