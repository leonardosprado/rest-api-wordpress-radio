<?php

function api_blog_post($request){
    $user = wp_get_current_user();
    $user_id =  $user->ID;
    //JSON 
    if($user_id > 0){
        $titulo       =  sanitize_text_field($request['titulo']);
        $content      =  $request['post_content'];
        $descricao    =  sanitize_text_field($request['descricao']);
        $categoria    =  sanitize_text_field($request['categoria']);
        $resumo       =  sanitize_text_field($request['resumo']);
        $autor_post   =  sanitize_text_field($request['autor_post']);
        $status       =  sanitize_text_field($request['status']);
        $comment_status       =  sanitize_text_field($request['comment_status']);
        $estado       = sanitize_text_field($request['estado']);
        $cidade       = sanitize_text_field($request['cidade']);
        
        $categoria_blog = sanitize_text_field($request['categoria_blog']);
        
        $usuario_id   =  $user->user_login;
        
        $response = array(
            'post_author'    => $user_id,           //Autor da postagem
            'post_content'   => $content,           //Conteudo da Postagem
            'post_title'     => $titulo,            //Titulo da Postagem
            'post_status'    => $status,            //Status da Postagem.
            'post_type'      => $categoria,         //Categoria da Postagem.
            'post_excerpt'   => $resumo,            //Resumo da Postagem. 
            'comment_status' => $comment_status,    //Status dos comentarios
            'post_name'      => $titulo,            //Nome da postagem, normalmente é o proprio titulo
            //'post_modified'  = $post_modified     //Data que a postagem foi modidicada a ultima vez
            //'tags_input'     => $tags_input,      //Matriz de Tags
            'meta_input' => array(                  //
                'estado'=>$estado,                  //
                'cidade'=>$cidade,                  //
                'categoria_blog'=>$categoria_blog   //
            ),
            

        );

        $blog_id = wp_insert_post($response);

        $images = get_attached_media('image', $post_id);

        $images_array = null;

        if($images){
            $images_array = array();
            foreach($images as $key => $value ){
                $images_array[] = array(
                    'titulo' => $value->post_name,
                    'src' => $value->guid,
                );
            }
        }


        $response['id'] = get_post_field('post_name',$blog_id);
    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
   
    return rest_ensure_response($response);
}

function registrar_api_blog_post(){

    register_rest_route('api', '/blog', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_blog_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_blog_post');


?>