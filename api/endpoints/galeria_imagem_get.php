<?php

function galeria_imagem_scheme($slug){

    $post_id = get_galeria_imagem_id_slug($slug);
 
    
    if($post_id){
        $post_meta = get_post_meta($post_id);

        $images = get_attached_media('image', $post_id);

        $images_array = null;

        if($images){
            $images_array = array();
            foreach($images as $key => $value ){
                $images_array[] = array(
                    'id'    => $value->ID,
                    'titulo' => $value->post_name,
                    'src' => $value->guid,
                );
            }
        }
        
        $cover = $post_meta['cover'][0];
        $cover = maybe_unserialize($cover);
        $response = array(
            'id' => $slug,
            "fotos" => $images_array,
            "nome" =>$post_meta['nome'][0],
            "titulo" =>$post_meta['titulo'][0],
            "descricao" =>$post_meta['descricao'][0],
            "filial" =>$post_meta['filial'][0],
            "cover" =>$cover,
        );

    }
    else{ 
        $response = new WP_Error('naoexiste','Foto não Encontrada',array('status'=>404));
    }

    return $response;
}

function api_galeria_imagem_get($request){

    $response = galeria_imagem_scheme($request['slug']);
    return rest_ensure_response($response);
}

function registrar_api_galeria_imagem_get(){

    register_rest_route('api', '/galeria-imagem/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_galeria_imagem_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_galeria_imagem_get');




//API IMAGENS 
function api_galeria_imagems_get($request){

    $q = sanitize_text_field($request['q'])?:'';
    $_page = sanitize_text_field($request['_page'])?:0;
    $_limit = sanitize_text_field($request['_limit'])?:-1;
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
        'post_type'=>'galeria_fotos',
        'post_per_page' => $_limit,
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
        $imagens[] = galeria_imagem_scheme($value->post_name);
    }

    $response = rest_ensure_response($imagens);
    $response->header('X-Total-Count',$total);
    return ($response);
    
}


function registrar_api_galeria_imagems_get(){

    register_rest_route('api', '/galeria-imagem', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_galeria_imagems_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_galeria_imagems_get');







//RETORNAR GALERIA DE IMAGEM PELA FILIAL

function api_galeria_filial_get($request){

    $slug = $request["slug"];

    $q = sanitize_text_field($request['q'])?:'';
    $_page = sanitize_text_field($request['_page'])?:0;
    $_limit = sanitize_text_field($request['_limit'])?:-1;
    $usuario_id = sanitize_text_field($usuario_id);

    $usuario_id_query = null;

    if($usuario_id){
        $usuario_id_query = array(
            'key' => 'usuario_id',
            'value' => $usuario_id,
            'compare' => '='
        );
    }


    $queryEquipe = array(
        'post_type'=>'galeria_fotos',
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

    $loop = new WP_Query($queryEquipe);
    $posts = $loop->posts;
    $total = $loop->found_posts;

    $imagens = array();

    foreach($posts as $key => $value){
        $imagens[] = galeria_imagem_scheme($value->post_name);
    }

    $response = rest_ensure_response($imagens);
    $response->header('X-Total-Count',$total);
    return ($response);
}


function registrar_api_galeria_filial_get(){

    register_rest_route('api', '/galeria-imagemfilial/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_galeria_filial_get',
        ),
    ));
}


add_action('rest_api_init','registrar_api_galeria_filial_get');






?>

