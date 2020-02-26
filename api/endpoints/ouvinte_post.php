<?php

function api_ouvinte_post($request){


        // $user_nome = sanitize_text_field($request['nome_usuario']);
        $email          =  sanitize_email($request['email']);
        $primeiroNome   = sanitize_text_field( $request['primeironome']);
        $sobrenome      = sanitize_text_field( $request['sobrenome']);
        $telefone       = sanitize_text_field( $request['telefone']);
        $cidade         = sanitize_text_field( $request['cidade']);
        $estado         = sanitize_text_field( $request['estado']);
        $filial         = sanitize_text_field($request['filial']);
    
        // $cidade = sanitize_text_field($request['cidade']);
        // $estado = sanitize_text_field($request['estado']);

        $senha = sanitize_text_field($request['senha']);


        // $capabilities = sanitize_text_field($request['funcao']);
        $user_nome = $email;
        if($email){

            $user_exists = username_exists($user_nome);
            $email_exists = email_exists($email);
    
            if(!$user_exists && !$email_exists){
                $user_id = wp_create_user($user_nome, $senha, $email);
                $response = array(
                    'ID' => $user_id,
                    'display_name' => $primeiroNome,
                    'first_name' => $primeiroNome,
                    'role' => 'subscriber',
                );
                wp_update_user($response);
                update_user_meta($user_id, 'last_name', $sobrenome);
                update_user_meta($user_id, 'telefone', $telefone);
                update_user_meta($user_id, 'cidade', $cidade);
                update_user_meta($user_id, 'estado', $estado);
                $response = array(
                    'ID' => $user_id,
                    'display_name' => $primeiroNome,
                    'first_name' => $primeiroNome,
                    'role' => 'subscriber',
                    'message': 'Conta criada com sucesso!',
                );
            }
            else{
                $response = new WP_Error('email','Email já cadastrado!', array('status'=> 403 ));
            }
        }
        else{
            $response = new WP_Error('email','E-mail não Inserido', array('status'=> 403 ));
        }

        return rest_ensure_response($response);
}

function registrar_api_ouvinte_post(){

    register_rest_route('api', '/ouvinte', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_ouvinte_post',
        ),
    ));
}

add_action('rest_api_init','registrar_api_ouvinte_post');






// Mudar Senha que esqueceu, no caso gerar uma nova. 
function api_ouvinte_lost_pass($request){


    $email  =  sanitize_email($request['email']);
    if($email){
        $email_exists = email_exists($email);
        if($email_exists){

        }
        else{
            $response = new WP_Error('email','Email não cadastrado!', array('status'=> 403 ));
        }

    }
    else{
        $response = new WP_Error('email','E-mail não Inserido', array('status'=> 403 ));
    }


    return rest_ensure_response($response);
}


function registrar_api_ouvinte_lost_pass(){

    register_rest_route('api', '/ouvinte/password', array(
        array(
            'methods' => 'POST',
            'callback' => 'api_ouvinte_lost_pass',
        ),
    ));
}

add_action('rest_api_init','registrar_api_ouvinte_lost_pass');

?>