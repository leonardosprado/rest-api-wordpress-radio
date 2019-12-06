<?php

function api_usuario_post($request){
    $user = wp_get_current_user();
    $iduser = $user->ID;
    if($iduser > 0){

        $user_nome = sanitize_text_field($request['nome_usuario']);
        $email =  sanitize_email($request['email']);

        $primeiroNome = sanitize_text_field( $request['primeiroNome']);
        $sobrenome = sanitize_text_field( $request['sobrenome']);
        
        $cidade = sanitize_text_field($request['cidade']);
        $estado = sanitize_text_field($request['estado']);

        $senha = sanitize_text_field($request['senha']);

        $capabilities = sanitize_text_field($request['funcao']);

        $user_exists = username_exists($user_nome);
        $email_exists = email_exists($email);

        if(!$user_exists && !$email_exists){
            $user_id = wp_create_user($user_nome, $senha, $email);
            $response = array(
                'ID' => $user_id,
                'display_name' => $user_nome,
                'first_name' => $primeiroNome,
                'role' => $capabilities,
            );
            wp_update_user($response);
            update_user_meta($user_id, 'last_name', $sobrenome);
            update_user_meta($user_id, 'cidade', $cidade);
            update_user_meta($user_id, 'estado', $estado);
        }
        else{
            $response = new WP_Error('email','Email ja cadastrao.', array('status'=> 403 ));
        }
    
    }
    else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response);
}

function registrar_api_usuario_post(){

    register_rest_route('api', '/usuario', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_usuario_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_usuario_post');


?>