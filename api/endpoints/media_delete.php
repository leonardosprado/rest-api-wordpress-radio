<?php

//API Promocao DELETE
function api_media_delete($request)
{

    $slug = $request['slug'];

    // $imagem_id = get_media_id_slug($slug);

    // BUSCAR POR ID O PST QUE TEM O TIPO IMAGEM. 
    $query = new WP_Query(array(
        'p'             => (int) $slug,
        'post_type'     => 'imagem',
        'numberposts'   => 1,
        'fields'        => 'ids'
    ));


    $post_id  = $query->get_posts();
    //    $post = array_shift($posts);

    $post = get_post(array_shift($post_id));


    $user = wp_get_current_user();
    $user_id = (int) $user->ID;

    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);
    $author_id = (int) get_post_field('post_author', $post);


    if ($user_id > 0 and ($roles === "administrator" or $roles === "editor" or $roles === "author")) {

        $images = get_attached_media('image', $post->ID);
        if ($images) {
            foreach ($images as $key => $value) {
                wp_delete_attachment($value->ID, true);
            }
        }
        $response = wp_delete_post($post->ID, true);
       


    } else {
        $response = new WP_Error('permissao', 'Usuario nao possui permissão.', array('status' => 401));
    }
    $response = rest_ensure_response($response);
    return ($response);
}


function registrar_api_media_delete()
{

    register_rest_route('api', '/media/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => 'api_media_delete',
        ),
    ));
}

add_action('rest_api_init', 'registrar_api_media_delete');
