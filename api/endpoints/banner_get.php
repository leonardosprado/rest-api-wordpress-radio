<?php





function galeria_banner_scheme($slug){

    $post_id = get_banner_id_slug($slug);
    
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
            "fotos" => $images_array,
            "texto_alternativo" =>$post_meta['_wp_attachment_image_alt'][0],
            "status" =>$post_meta['status'][0]
        );

    }
    else{ 
        $response = new WP_Error('naoexiste','Foto não Encontrada',array('status'=>404));
    }

    return $response;
}


///RETORNAR BANNESR QUE ESTÃO ATIVADO E PROMOÇÃO QUE POSSUI BANNER

function api_banners_get($request){
    
    $q = sanitize_text_field($request['q'])?:'';
    $_page = sanitize_text_field($request['_page'])?:0;
    $_limit = sanitize_text_field($request['_limit'])?:-1;
    $usuario_id = sanitize_text_field($usuario_id);


    // Puxar Promocão que possui Banner Ativado.
    $queryPromocao = array(
        'post_type'=>'promocao',
        'post_per_page' => $_limit,
        'paged' => $_page,
        's' =>$q,
        'meta_query' => array(
            array(
                'key' => 'status',
                'value' => 'on',
                'compare' => '=',
            )
        )
    );

    $loopPromocao= new WP_Query($queryPromocao);
    $postsPromocao = $loopPromocao->posts;
    
    foreach($postsPromocao as $key => $value){
        $listPromocao[] = promocao_scheme($value->post_name);
    }


    // Puxa Banners que possui o staus ONLINE
    $queryBanner = array(
        'post_type'=>'banner',
        'post_per_page' => $_limit,
        'paged' => $_page,
        's' =>$q,
        'meta_query' => array(
            array(
                'key' => 'status',
                'value' => 'online',
                'compare' => '=',
            )
        )
    );

    $loopBanner = new WP_Query($queryBanner);
    $posts = $loopBanner->posts;

    
    foreach($posts as $key => $value){
        $imagens[] = galeria_banner_scheme($value->post_name);
    }

    if($listPromocao && $imagens){
        $banners = array_merge($listPromocao,$imagens);
    }
    else{
        if($listPromocao){
            $banners = $listPromocao;
        }
        if($imagens){
            $banners = $imagens;
        }
        else{
            $banners = null;
        }
    }

   
    // $banners = array_merge($listPromocao,$imagens);
    

    $response = rest_ensure_response($banners);
    $response->header('X-Total-Count',$total);
    return ($response);
}

function registrar_api_banners_get(){

    register_rest_route('api', '/banners', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_banners_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_banners_get');

///FIM ---- RETORNAR BANNESR QUE ESTÃO ATIVADO E PROMOÇÃO QUE POSSUI BANNER

//RETORNAR TODOS BANNERS
function api_banner_get($request){

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
        'post_type'=>'banner',
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
        $imagens[] = galeria_banner_scheme($value->post_name);
    }

    $response = rest_ensure_response($imagens);
    $response->header('X-Total-Count',$total);
    return ($response);
    
}


function registrar_api_banner_get(){

    register_rest_route('api', '/banner', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_banner_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_banner_get');

?>