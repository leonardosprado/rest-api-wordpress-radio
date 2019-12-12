<?php



//API IMAGENS DELETE
function api_galeria_video_delete($request){

    $slug = $request['slug'];


    $imagem_id = get_galeria_video_id_slug($slug);

    $user = wp_get_current_user();

    $author_id = (int) get_post_field('post_author', $imagem_id);
    $user_id = (int) $user->ID;
    if($user_id === $author_id){

        $images = get_attached_media('image', $imagem_id);
        if($images){
            foreach($images as $key => $value){
                wp_delete_attachment($value->ID, true);
            }
        }
        $response = wp_delete_post($imagem_id, true);

    }else{
        $response = new WP_Error('permissao','Usuario nao possui permissão.', array('status'=> 401));
    }

    $response = rest_ensure_response($imagens);

    return ($response);
    
}


function registrar_api_galeria_video_delete(){

    register_rest_route('api', '/galeria-video/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => 'api_galeria_video_delete',
        ),
    ));
}

add_action('rest_api_init','registrar_api_galeria_video_delete');

?>