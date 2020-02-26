<?php

function promocao_scheme($slug){

    $post_id = get_promocao_id_slug($slug);
    
    if($post_id){
        $post = get_post($post_id);
        $post_meta = get_post_meta($post_id);

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
       

        $response = array(
            'id' => $slug,
            "titulo" => $post->post_title,
            "nome"  => $post->post_name,
            "fotos" => $images_array,
            "texto_alternativo" =>$post_meta['_wp_attachment_image_alt'][0],
            "conteudo" =>$post_meta['conteudo'][0],
            "data_ini" =>$post_meta['data_ini'][0],
            "data_fim" =>$post_meta['data_fim'][0],
            "status" =>$post_meta['status'][0],
            "media" => unserialize($post_meta['media'][0]),
            "filial" =>$post_meta['filial'][0],
            
        );

    }
    else{ 
        $response = new WP_Error('naoexiste','Foto não Encontrada',array('status'=>404));
    }

    return $response;
}


//API IMAGENS 
function api_promocao_get($request){

    $q = sanitize_text_field($request['q']) ?:'';
    $_page = sanitize_text_field($request['_page']) ?:0;
    $_limit = sanitize_text_field($request['_limit']) ?: -1;
    $usuario_id = sanitize_text_field($usuario_id);

    $usuario_id_query = null;
    if($usuario_id){
        $usuario_id_query = array(
            'key' => 'usuario_id',
            'value' => $usuario_id,
            'compare' => '='
        );
    }


    $query = array(
        'post_type'=>'promocao',
        'posts_per_page' => $_limit,
        'paged' => $_page,
        's' =>$q,
        'meta_query' => array(
            $usuario_id_query,
        )
    );

    $loop = new WP_Query($query);
    $posts = $loop->posts;
    $total = $loop->found_posts;

    $imagens = array();

    foreach($posts as $key => $value){
        $imagens[] = promocao_scheme($value->post_name);
    }

    $response = rest_ensure_response($imagens);
    $response->header('X-Total-Count',$total);
    return ($response);
    
}


function registrar_api_promocao_get(){

    register_rest_route('api', '/promocao', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_promocao_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_promocao_get');



function api_promocao_id_get($request){
    
    $response = promocao_scheme($request["slug"]);
    return rest_ensure_response($response);

}


function registrar_api_promocao_id_get(){

    register_rest_route('api', '/promocao/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_promocao_id_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_promocao_id_get');



// Retornar Promoção Por filial 

function api_promocao_filial_get($request){
    $slug = $request["slug"];
    
    $q = sanitize_text_field($request['q'])?:'';
    $_page = sanitize_text_field($request['_page'])?:0;
    $_limit = sanitize_text_field($request['_limit'])?:-1;
    $usuario_id = sanitize_text_field($usuario_id);


    $queryPromocao = array(
        'post_type'=>'promocao',
        'post_per_page' => $_limit,
        'paged' => $_page,
        's' =>$q,
        'meta_query' => array(
            array(
                'key' => 'filial',
                'value' => $slug,
                'compare' => '=',
            )
        )
    );

    $loopProgramacao = new WP_Query($queryPromocao);
    $posts = $loopProgramacao->posts;

    foreach($posts as $key => $value){
        $progra[] = promocao_scheme($value->post_name);
    }

    $response = rest_ensure_response($progra);
    $response->header('X-Total-Count',$total);
    return ($response);
    
}

function registrar_api_promocao_filial_get(){

    register_rest_route('api', '/promocaofilial/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_promocao_filial_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_promocao_filial_get');


?>