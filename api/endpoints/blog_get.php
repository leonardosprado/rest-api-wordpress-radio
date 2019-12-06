<?php


function blog_scheme($slug){
    $post_id = get_posts_id_slug($slug);
    if($post_id){
        $post_meta = get_post_meta($post_id);
        $images = get_attached_media('image', $post_id);

        $response = array(
            'id' => $post_id,
            'estado'=>$post_meta['estado'][0],           
            'cidade'=>$post_meta['cidade'][0],                  
            'categoria_blog'=>$post_meta['categoria_blog'][0],  
        );
    }else{
        $response = new WP_Error('naoexiste','Produto não Encontraco.', array('status' => 404));
    }
    return $response;
}


function blog_posts($value){
    $postagem = get_post($value);
    $post_id = $postagem->ID;
    $post_meta = get_post_meta($value);
    $resposta = array(
        'id'             => $post_id,
        'post_author'    => $postagem->post_author,           //Autor da postagem
        'post_content'   => $postagem->post_content,           //Conteudo da Postagem
        'post_title'     => $postagem->post_title,            //Titulo da Postagem
        'post_status'    => $postagem->post_status,            //Status da Postagem.
        'post_type'      => $postagem->post_type,         //Categoria da Postagem.
        'post_excerpt'   => $postagem->post_excerpt,            //Resumo da Postagem. 
        'comment_status' => $postagem->comment_status,    //Status dos comentarios */
        'data'           => $postagem->post_date,
        'estado'         => $post_meta['estado'][0],           
        'cidade'         => $post_meta['cidade'][0],                  
        'categoria_blog' => $post_meta['categoria_blog'][0],
    ); 
    return $resposta;   
}


function api_blog_get($request){
    $q      =  sanitize_text_field($request['q']) ?: '';
    $_page  = sanitize_text_field($request['_page'])?: 0;  
    $_limit = sanitize_text_field($request['_limit'])?: 9;  
    $usuario_id = sanitize_text_field($request['usuario_id']);
    $usuario_id_query = null;
    if($usuario_id){
        $usuario_id_query = array(
            'key'=>'author',
            'value' => $usuario_id,
            'compare'=> '=',
        );
        
    }
    
    $query = array(
        'post_type' => 'blog',
        'posts_per_page' => $_limit,
        'paged' => $_page,
        's' => $q,
        'meta_query' => array(
            $usuario_id_query,
        ),
    );
    $loop = new WP_Query($query);
    $posts = $loop->posts;

    $total = $loop->found_posts;

    $blog_post = array();
    foreach ($posts as $key => $value) {
        $blog_post[] = blog_posts($value->ID);
    }

    $response = rest_ensure_response($blog_post);
    $response->header('X-Total-Count',$total);

    return $blog_post;


    }



    //Exemplo BASICO DE BUSCAR POSTAGEM COM ID EX: -> ID:5
    /* function api_blog_get($request){

        $postagem = get_post(5);
        $post_id = $postagem->ID;
        $post_meta = get_post_meta(5);
        $reposta = array(
            'id'             => $post_id,
            'post_author'    => $postagem->post_author,           //Autor da postagem
            'post_content'   => $postagem->post_content,           //Conteudo da Postagem
            'post_title'     => $postagem->post_title,            //Titulo da Postagem
            'post_status'    => $postagem->post_status,            //Status da Postagem.
            'post_type'      => $postagem->post_type,         //Categoria da Postagem.
            'post_excerpt'   => $postagem->post_excerpt,            //Resumo da Postagem. 
            'comment_status' => $postagem->comment_status,    //Status dos comentarios 
            'estado'         => $post_meta['estado'][0],           
            'cidade'         => $post_meta['cidade'][0],                  
            'categoria_blog' => $post_meta['categoria_blog'][0],
        ); 

        return (rest_ensure_response($reposta));
        
        
    } */


    function registrar_api_blog_get(){
        register_rest_route('api', '/blog', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_blog_get',
            ),
        ));
    }

    add_action('rest_api_init','registrar_api_blog_get');

?>