<?php

//API IMAGENS DELETE
function api_blog_delete($request){

    $slug = $request['slug'];


    $post_id = get_posts_id_slug($slug);

    $user = wp_get_current_user();

    $user_id =  $user->ID;
    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);

    // $author_id = (int) get_post_field('post_author', $post_id);
    $user_id = (int) $user->ID;
    if ($user_id > 0 and ($roles === "administrator" or $roles === "editor" or $roles === "author"))
    { 
        if($post_id>0){
            $images = get_attached_media('image', $post_id);
            if($images){
                foreach($images as $key => $value){
                    wp_delete_attachment($value->ID, true);
                }
            }
            
            $response = wp_delete_post($post_id, true);

        }
        else{
            $response = new WP_Error('naoexiste','Post não esxiste!', array('status'=> 403));
        }

    }
    
    else{
        $response = new WP_Error('permissao','Usuario nao possui permissão.', array('status'=> 401));
    }


    return(rest_ensure_response($response));
    
}


function registrar_api_blog_delete(){

    register_rest_route('api', '/blog/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => 'api_blog_delete',
        ),
    ));
}

add_action('rest_api_init','registrar_api_blog_delete');

?>