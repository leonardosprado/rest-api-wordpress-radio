<?php

function api_blog_put($request){


    $slug = $request['slug'];

    $user = wp_get_current_user();
    $post_id = get_posts_id_slug($slug);

    $user_id =  $user->ID;
    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);

    if ($user_id > 0 and ($roles === "administrator" or $roles === "editor" or $roles === "author")) {

        $titulo                 =  sanitize_text_field($request['titulo']);
        $content                =  ($request['post_content']);
        $resumo                 =  sanitize_text_field($request['resumo']);
        $status                 =  sanitize_text_field($request['status']);
        $comment_status         =  sanitize_text_field($request['comment_status']);
        $estado                 = sanitize_text_field($request['estado']);
        $cidade                 = sanitize_text_field($request['cidade']);
        $categoria_blog         = sanitize_text_field($request['categoria_blog']);

        $response = array(
            'ID'        => $post_id,
            'post_author'    => $user_id,           //Autor da postagem
            'post_title'     => $titulo,            //Titulo da Postagem
            'post_content'   => $content,           //Conteudo da Postagem
            'post_status'    => $status,            //Status da Postagem.
            'post_type'      => 'blog',         //Categoria da Postagem.
            'post_excerpt'   => $resumo,            //Resumo da Postagem. 
            'comment_status' => $comment_status,    //Status dos comentarios
        );

        $update = wp_update_post($response);

        update_post_meta($post_id, 'estado', $estado);
        update_post_meta($post_id, 'cidade', $cidade);
        update_post_meta($post_id, 'categoria_blog', $categoria_blog);
        
        // $files = $request->get_file_params();  //Recebe a Imagem em Destaque 

        // if($files){
        //     require_once(ABSPATH . 'wp-admin/includes/image.php');
        //     require_once(ABSPATH . 'wp-admin/includes/file.php');
        //     require_once(ABSPATH . 'wp-admin/includes/media.php');

        //     foreach($files as $file => $array){
        //         media_handle_upload($file, $blog_id); //Faz uplouad das imagem vinculado a Postagem
        //     }
        // }

    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response);
}

function registrar_api_blog_put(){

    register_rest_route('api', '/blog/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_blog_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_blog_put');


?>