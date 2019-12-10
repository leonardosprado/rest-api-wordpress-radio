<?php


function blog_scheme($slug){
    $post_id = get_posts_id_slug($slug);
    if($post_id){

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

        $postagem = get_post($post_id);
        $post_meta = get_post_meta($post_id);
        $response = array(
            'id'             => $post_id,
            'slug'           => $postagem->post_name,
            'post_title'     => $postagem->post_title,
            'post_date'      => $postagem->post_date,
            'post_modified'  =>$postagem->post_modified,                //Titulo da Postagem
            'post_author'    => $postagem->post_author,           //Autor da postagem
            'post_content'   => $postagem->post_content,           //Conteudo da Postagem
            'post_status'    => $postagem->post_status,            //Status da Postagem.
            'post_type'      => $postagem->post_type,         //Categoria da Postagem.
            'post_excerpt'   => $postagem->post_excerpt,            //Resumo da Postagem. 
            'comment_status' => $postagem->comment_status,    //Status dos comentarios */
            'data'           => $postagem->post_date,
            'estado'         => $post_meta['estado'][0],           
            'cidade'         => $post_meta['cidade'][0],                  
            'categoria_blog' => $post_meta['categoria_blog'][0],
            'imagem'         => $images_array,
        );
    }else{
        $response = new WP_Error('naoexiste','Produto não Encontraco.', array('status' => 404));
    }
    return $response;
}


// Retorna Um Post 
function blog_posts($value){
    $postagem = get_post($value);

    $post_id = $postagem->ID;

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
    $post_meta = get_post_meta($post_id);

    $cat_blog = $post_meta['categoria_blog'][0];

    $resposta = array(
        'id'             => $post_id,
        'slug'           => $postagem->post_name,
        'post_title'     => $postagem->post_title,
        'post_date'      => $postagem->post_date,
        'post_modified'  =>$postagem->post_modified,               //Titulo da Postagem
        'post_author'    => $postagem->post_author,           //Autor da postagem
        'post_content'   => $postagem->post_content,           //Conteudo da Postagem
        'post_status'    => $postagem->post_status,            //Status da Postagem.
        'post_type'      => $postagem->post_type,         //Categoria da Postagem.
        'post_excerpt'   => $postagem->post_excerpt,            //Resumo da Postagem. 
        'comment_status' => $postagem->comment_status,    //Status dos comentarios */
        'data'           => $postagem->post_date,
        'estado'         => $post_meta['estado'][0],           
        'cidade'         => $post_meta['cidade'][0],                  
        'categoria_blog' => $cat_blog,
        'imagem'         => $images_array[0],
    ); 
    return $resposta;   
}


// RETORNA TODOS AS POSTAGENS OPEN E PRIVATE

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

    function registrar_api_blog_get(){
        register_rest_route('api', '/blog', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_blog_get',
            ),
        ));
    }

    add_action('rest_api_init','registrar_api_blog_get');


    // RETORNAR POSTAGEM PELO ID OU SLUG
    function api_post_id_get($request){

        $response = blog_scheme($request['slug']);
        return rest_ensure_response($response);
    }
    
    function registrar_api_post_id_get(){
    
        register_rest_route('api', '/blog/(?P<slug>[-\w]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => 'api_post_id_get',
            ),
        ));
    }
    
    add_action('rest_api_init','registrar_api_post_id_get');



    // RETORNA TODOS POSTS publish
    function api_post_publish_get($request){

        $query = new WP_Query( 
            array( 
                'post_status' => 'publish',
                'post_type'     => 'blog',
                'numberposts'   => -1,
                
            ) 
        );

        $posts = $query->get_posts();
        $blog_post = array();
        foreach ($posts as $key => $value) {
            $blog_post[] = blog_posts($value->ID);
        }
    


       return (rest_ensure_response($blog_post));



    }
    
    function registrar_api_post_publish_get(){
    
        register_rest_route('api', '/blogpublish/', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => 'api_post_publish_get',
            ),
        ));
    }
    
    add_action('rest_api_init','registrar_api_post_publish_get');

?>