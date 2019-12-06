<?php

function api_promocao_cadastrada_post($request){
    $user = wp_get_current_user();
    $user_id =  $user->ID;

    $q = sanitize_text_field($request['q'])?:'';
    $_page = sanitize_text_field($request['_page'])?:0;
    $_limit = sanitize_text_field($request['_limit'])?:-1;
    $usuario_id = sanitize_text_field($usuario_id);

    if($user_id > 0){

        $titulo     = sanitize_text_field($request['titulo']);
        $promo_id   = sanitize_text_field($request['promo_id']);

        // Verificar se o Usuario já esta cadastrado na promocao. 
        $queryPromo = array(
            'post_type'=>'promocao_cadastrada',
            'post_per_page' => $_limit,
            'paged' => $_page,
            's' =>$q,
            'meta_query' => array(
                array(
                    'key' => 'promo_id',
                    'value' => $promo_id,
                    'compare' => '=',
                ),
            )
        );
        $loopPromo = new WP_Query($queryPromo);
        $posts = $loopPromo->posts;
        
        $ver_id = false;
        foreach($posts as $key => $value){
            $id[] = ($value->post_author);
            if($value->post_author == $user_id ){
                $ver_id = true;
            }
        }


        if(!$ver_id){
            $usuario_id =  $user->user_login;
                    $response   = array(
                        'post_author'   =>$user_id,
                        'post_type'     => 'promocao_cadastrada',
                        'post_title'    => $titulo,
                        'post_status'   => 'publish',
                        'meta_input'    => array(
                            'promo_id'  => $promo_id,
                        )
                    
                    );
            $promo_cadastro_id = wp_insert_post($response);
        }
        else{
            $response = new WP_Error('JaCadastrado','Usuário já já cadastrado na Promoção.', array('status'=> 403 ));
        }
        // $conteudo   = sanitize_text_field($request['conteudo']);
        // $banner     = sanitize_text_field($request['banner']);
        // $data_ini   = sanitize_text_field($request['data_ini']);
        // $data_fim   = sanitize_text_field($request['data_fim']);
        // $status     = sanitize_text_field( $request['status']);
       
        // $promo_cadastro_id = wp_insert_post($response);
        
        
        // $response['id'] = get_post_field('post_name',$imagem_id);
        // $files = $request->get_file_params();
    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
   
    return rest_ensure_response($response);
}

function registrar_api_promocao_cadastrada_post(){

    register_rest_route('api', '/promocadastro', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_promocao_cadastrada_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_promocao_cadastrada_post');

?>