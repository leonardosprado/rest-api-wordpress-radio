<?php



function programacao_scheme($slug){

    $post_id = get_programacao_id_slug($slug); 
    
    if($post_id){
        $post = get_post($post_id);
        $post_meta = get_post_meta($post_id);
        $images = get_attached_media('image', $post_id);

        $images_array = null;

        if($images){
            $images_array = array();
            foreach($images as $key => $value ){
                $images_array[] = array(
                    'src' => $value->guid,
                );
            }
        }

        $response = array(
            'id' => $post_id,
            'slug' => $slug,
            "titulo" => $post->post_title,
            "apresentacao" =>$post_meta['apresentacao'][0],
            "dia_semana" =>$post_meta['dia_semana'][0],
            "hora_ini" =>$post_meta['hora_ini'][0],
            "hora_fim" =>$post_meta['hora_fim'][0],
            "filial" =>$post_meta['filial'][0],
            "cidade" =>$post_meta['cidade'][0],
            "estado" =>$post_meta['estado'][0],
            "imagem_programa" => maybe_unserialize($post_meta['imagem_programa'][0]),
            "logo_programa" =>maybe_unserialize($post_meta['logo_programa'][0]),
        );

    }
    else{ 
        $response = new WP_Error('naoexiste','Prgramação não Encontrado',array('status'=>404));
    }

    return $response;
}



function programacao_scheme_horario($slug){

    $post_id = get_programacao_id_slug($slug); 

    $timezone  = -3;
    $dataAtual  = date('d/m/Y às H:i:s');
    // $hora       = date("H:i:m", time() + 3600*($timezone+date("I")));
    $hora       = date("H", time() + 3600*($timezone+date("I")));
    $minuto       = date("i", time() + 3600*($timezone+date("I")));
    $dia_semana_numero = date('N');
    $dia_semana_string = $diasemanastring[$dia_semana_numero];
    $hora_int = (int)$hora;
    
    if($post_id){
        $post = get_post($post_id);
        $post_meta = get_post_meta($post_id);
        $hora_ini = $post_meta['hora_ini'][0];
        $hora_fim = $post_meta['hora_fim'][0];

        if($hora>=$hora_ini and $hora<$hora_fim){
            $response = array(
                'id' => $post_id,
                "hora_ini" =>$hora_ini,
                "hora_fim" =>$hora_fim,
                
            );
            return true;
        }
        else{
            $response = array(
                'id' => $post_id,
                "hora_ini" =>$hora_ini,
                "hora_fim" =>$hora_fim,
                
            );
            return false;

        }

    }
    else{ 
        $response = new WP_Error('naoexiste','Prgramação não Encontrado',array('status'=>404));
        return false;
    }

    // return $response;
}

// Retornar programação que está no AR.
function api_programacao_ar_get($request){

    $q      =  sanitize_text_field($request['q']) ?: '';
    $_page  = sanitize_text_field($request['_page'])?: 0;  
    $_limit = sanitize_text_field($request['_limit'])?: 9;  
    $usuario_id = sanitize_text_field($request['usuario_id']);


    // date_default_timezone_set(‘America/Sao_Paulo’);
    // date_default_timezone_set('America/Sao_Paulo');

    $diasemanastring = array('domingo', 'segunda-feira', 'terca-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sabado');




    $timezone  = -3;
    $dataAtual  = date('d/m/Y às H:i:s');
    // $hora       = date("H:i:m", time() + 3600*($timezone+date("I")));
    $hora       = date("H", time() + 3600*($timezone+date("I")));
    $minuto       = date("i", time() + 3600*($timezone+date("I")));
    $dia_semana_numero = date('N');
    $dia_semana_string = $diasemanastring[$dia_semana_numero];
    $hora_int = (int)$hora;


    $custom_meta = array(
        'relation' => 'AND',
        array(
            'key' => 'dia_semana',
            'value'=>$dia_semana_string,
            'compare'=>'=',
        ),        
    );

    $queryProgramacao = array(
            'post_type'=>'programacao',
            'post_per_page' => $_limit,
            'paged' => $_page,
            's' =>'',
            'meta_query' => $custom_meta,
        );
    
    
    // $slug = $request["slug"];

    // echo "Mês: $mes; Dia: $dia; Ano: $ano<br />\n";

    // $q = sanitize_text_field($request['q'])?:'';
    // $_page = sanitize_text_field($request['_page'])?:0;
    // $_limit = sanitize_text_field($request['_limit'])?:-1;
    // $usuario_id = sanitize_text_field($usuario_id);


    // $queryProgramacao = array(
    //     'post_type'=>'programacao',
    //     'post_per_page' => $_limit,
    //     'paged' => $_page,
    //     's' =>'',
    //     'meta_query' => array(
    //         'relation' => 'AND',
    //         array(
    //             'key' => 'hora_ini',
    //             'value'=>$hora,
    //             'compare'=>'>',
    //         ),
    //         array(
    //             'key' => 'hora_fim',
    //             'value'=>$hora,
    //             'compare'=>'<',
    //         )
    //     )
    // );

    $loopProgramacao = new WP_Query($queryProgramacao);
    $posts = $loopProgramacao->posts;

    foreach($posts as $key => $value){
        if(programacao_scheme_horario($value->post_name)){
            $progra[] = programacao_scheme($value->post_name);
        }
    }

    $response = rest_ensure_response($progra);
    // $response->header('X-Total-Count',$total);
    return ($response);
    
}

function registrar_api_programacao_ar_get(){

    register_rest_route('api', '/programaar', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_programacao_ar_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_programacao_ar_get');

// Retornar programação por filial e Dia da Semana 
function api_programacao_filial_semana_get($request){

    $q      =  sanitize_text_field($request['q']) ?: '';
    $_page  = sanitize_text_field($request['_page'])?: 0;  
    $_limit = sanitize_text_field($request['_limit'])?: 9;  
    $usuario_id = sanitize_text_field($request['usuario_id']);

    $slug = $request["slug"];

    // echo "Mês: $mes; Dia: $dia; Ano: $ano<br />\n";

    // $q = sanitize_text_field($request['q'])?:'';
    // $_page = sanitize_text_field($request['_page'])?:0;
    // $_limit = sanitize_text_field($request['_limit'])?:-1;
    // $usuario_id = sanitize_text_field($usuario_id);


    $queryProgramacao = array(
        'post_type'=>'programacao',
        'post_per_page' => $_limit,
        'paged' => $_page,
        's' =>'',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'filial',
                'value' => $slug,
                'compare' => '=',
            ),
            array(
                'key' => 'dia_semana',
                'value' => $q,
                'compare' => '=',
            )
        )
    );

    $loopProgramacao = new WP_Query($queryProgramacao);
    $posts = $loopProgramacao->posts;

    foreach($posts as $key => $value){
        $progra[] = programacao_scheme($value->post_name);
    }

    $response = rest_ensure_response($progra);
    $response->header('X-Total-Count',$total);
    return ($response);
    
}

function registrar_api_programacao_filial_semana_get(){

    register_rest_route('api', '/programasemana/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_programacao_filial_semana_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_programacao_filial_semana_get');



// Retornar programação por filial 

function api_programacao_filial_get($request){
    $slug = $request["slug"];
    
    $q = sanitize_text_field($request['q'])?:'';
    $_page = sanitize_text_field($request['_page'])?:0;
    $_limit = sanitize_text_field($request['_limit'])?:-1;
    $usuario_id = sanitize_text_field($usuario_id);


    $queryProgramacao = array(
        'post_type'=>'programacao',
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

    $loopProgramacao = new WP_Query($queryProgramacao);
    $posts = $loopProgramacao->posts;

    foreach($posts as $key => $value){
        $progra[] = programacao_scheme($value->post_name);
    }

    $response = rest_ensure_response($progra);
    $response->header('X-Total-Count',$total);
    return ($response);
    
}

function registrar_api_programacao_filial_get(){

    register_rest_route('api', '/programafilial/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_programacao_filial_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_programacao_filial_get');


//RETORNAR TODAS AS Filiais
function api_programacao_get($request){

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
        'post_type'=>'programacao',
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
        $imagens[] = programacao_scheme($value->post_name);
    }

    $response = rest_ensure_response($imagens);
    $response->header('X-Total-Count',$total);
    return ($response);
}


function registrar_api_programacao_get(){

    register_rest_route('api', '/programacao', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_programacao_get',
        ),
    ));
}


add_action('rest_api_init','registrar_api_programacao_get');


// Retornar Filial Pelo Seu ID 
function api_programacao_id_get($request){
    
    $response = programacao_scheme($request["slug"]);
    return rest_ensure_response($response);

}


function registrar_api_programacao_id_get(){

    register_rest_route('api', '/programacao/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_programacao_id_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_programacao_id_get');

?>