<?php



//API DELETE MUSICA PELO ID
function api_musica_delete($request){

    $slug = $request['slug'];

    $post_id = get_top_musica_id_slug($slug);

    $user = wp_get_current_user();

    $author_id = (int) get_post_field('post_author', $post_id);
    $user_id = (int) $user->ID;
    if($user_id > 0 ){

        $images = get_attached_media('image', $post_id);
        if($images){
            foreach($images as $key => $value){
                wp_delete_attachment($value->ID, true);
            }
        }
        $response = wp_delete_post($post_id, true);

    }else{
        $response = new WP_Error('permissao','Usuario nao possui permissão.', array('status'=> 401));
    }

    $response = rest_ensure_response($imagens);

    return ($response);
    
}


function registrar_api_musica_delete(){

    register_rest_route('api', '/musica/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => 'api_musica_delete',
        ),
    ));
}

add_action('rest_api_init','registrar_api_musica_delete');

?>