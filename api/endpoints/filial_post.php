<?php


function api_filial_post($request){

    $user = wp_get_current_user();
    $user_id =  $user->ID;

    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);


    if ($user_id > 0 and ($roles === "administrator")) {

        $titulo     = sanitize_text_field($request['titulo']);
        // $filial  = sanitize_text_field($request['filial']);
        $cidade     = sanitize_text_field($request['cidade']);
        $estado   = sanitize_text_field($request['estado']);
        $logo_filial   = sanitize_text_field($request['logo_filial']);


        $usuario_id =  $user->user_login;
        $response   = array(
            'post_author'   =>$user_id,
            'post_type'     => 'filial',
            'post_title'    => $titulo,
            'post_status'   => 'publish',
            'meta_input'    => array(
                'filial'            => $cidade."-".$estado,
                'cidade'            => $cidade,
                'estado'            => $estado,
                'logo_filial'       => $logo_filial,
            )
        );
        $post_id = wp_insert_post($response);

        $response['id'] = get_post_field('post_name',$imagem_id);
       
    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
   
    return rest_ensure_response($response);
}

function registrar_api_filial_post(){

    register_rest_route('api', '/filial', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_filial_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_filial_post');


?>