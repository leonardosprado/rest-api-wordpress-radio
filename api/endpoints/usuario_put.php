<?php


// PUT USUARIO LOGADO

function api_usuario_put($request){

    $user = wp_get_current_user();
    $user_id = $user->ID;

    if($user_id > 0){
        //$id = sanitize_text_field($request['id']);
        $nome = sanitize_text_field($request['nome']);
        $email =  sanitize_email($request['email']);
        $senha = $request['senha'];
        $primeiroNome = sanitize_text_field($request['primeiroNome']);
        $sobrenome = sanitize_text_field($request['sobrenome']);
        $apelido = sanitize_text_field($request['apelido']);
        
        $cep = sanitize_text_field($request['cep']);
        $cidade = sanitize_text_field($request['cidade']);
        $estado = sanitize_text_field($request['estado']);
    
        $biografia = sanitize_text_field($request['biografia']);
        $funcao = sanitize_text_field( $request['funcao'] );

        $email_exists = email_exists($email);
     
        if(!$email_exists|| $email_exists === $user_id){
            
            if($senha){
                $response = array(
                    'ID' => $user_id,
                    'user_pass'=>$senha,
                    'user_email'=>$email,
                    'display_name' => $nome,
                    'first_name' => $nome,
                    'senha' => "Senha Alterada",
                );
            }
            else {
                $response = array(
                'ID' => $user_id,
                'user_email'=>$email,
                'display_name' => $nome,
                'first_name' => $nome,
                'senha' => "Senha não alterada",
                );
            }
            wp_update_user($response);

            update_user_meta($user_id, 'last_name', $sobrenome);
            update_user_meta($user_id, 'nickname', $apelido);
            
            update_user_meta($user_id, 'cep', $cep);
            update_user_meta($user_id, 'cidade', $cidade);
            update_user_meta($user_id, 'estado', $estado);
            
            update_user_meta($user_id, 'description', $biografia);
            update_user_meta($user_id, 'wp_capabilities', $funcao);
        }
        else{
            $response = new WP_Error('email','Email ja cadastrao.', array('status'=> 403 ));
        }
        
    }else{
        $response = new WP_Error('permissao','Usuario não possui permissão.', array('status'=> 401 ));
    }

   
    return rest_ensure_response($response);
}


function registrar_api_usuario_put(){

    register_rest_route('api', '/usuario', array(
        array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_usuario_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_usuario_put');


// PUT USUARIO SLUG

function api_user_put($request){

    // Usuario Atual
    $user_atual = wp_get_current_user();
    $user_id_atual = $user_atual->ID;
    
    // Verifica se o Usuario esta logado. 
    if($user_id_atual > 0){
        $user_meta_atual = get_user_meta($user_id_atual); 
        $funcao_atual =  $user_meta_atual['wp_capabilities'][0]; //Funcao do usuario.

        $slug = $request['slug'];
        $user = get_user_by('login',$slug); 
        $user_id = $user->ID;

        if($user){
            // Verifica se o usuario tem permisao para alterar o outro usuario.
            //Somente administrador ou o titular da conta pode alterar.
            if($funcao_atual === "administrator" || $user_id_atual === $user->ID){                                  
                $nome = sanitize_text_field($request['nome']);
                $email =  sanitize_email($request['email']);
                $senha = $request['senha'];
                $primeiroNome = sanitize_text_field($request['primeiroNome']);
                $sobrenome = sanitize_text_field($request['sobrenome']);
                $apelido = sanitize_text_field($request['apelido']);

                $cep = sanitize_text_field($request['cep']);
                $cidade = sanitize_text_field($request['cidade']);
                $estado = sanitize_text_field($request['estado']);
            
                $biografia = sanitize_text_field($request['biografia']);
                $funcao = sanitize_text_field( $request['funcao'] );

                $cores = sanitize_text_field($request['cores']);


                $email_exists = email_exists($email); 
            
                if(!$email_exists || $email_exists === $user_id){
                    
                    if($senha){
                        $response = array(
                            'ID' => $user_id,
                            'user_pass'=>$senha,
                            'user_email'=>$email,
                            'display_name' => $nome,
                            'first_name' => $primeiroNome,
                            'role'  => $funcao,
                            'senha' => "Senha Alterada",
                        );
                    }
                    else {
                        $response = array(
                        'ID' => $user_id,
                        'user_email'=>$email,
                        'display_name' => $nome,
                        'first_name' => $primeiroNome,
                        'role'  => $funcao,
                        'senha' => "Senha não alterada",
                        );
                    }

                    wp_update_user($response);

                    update_user_meta($user_id, 'last_name', $sobrenome);
                    update_user_meta($user_id, 'nickname', $apelido);
                    
                    update_user_meta($user_id, 'cep', $cep);
                    update_user_meta($user_id, 'cidade', $cidade);
                    update_user_meta($user_id, 'estado', $estado);
                    
                    update_user_meta($user_id, 'description', $biografia);

                    update_user_meta( $user_id, 'cores', $cores);
                }
                else{
                    $response = new WP_Error('email','Email ja cadastrao.', array('status'=> 403 ));
                }
            }
            else{
                $response = new WP_Error('permissao','Usuario não possui permissão.', array('status'=> 401 ));
            }
        }
        else{
            $response = new WP_Error('naoexiste','Usuário a ser alterado não existe.', array('status'=> 401 ));

        }

    }
    else{
        $response = new WP_Error('permissao','Usuario não possui permissão.', array('status'=> 401 ));
    }


    return rest_ensure_response($response);

}


function registrar_api_user_put(){

    register_rest_route('api', '/usuario/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_user_put',
        ),
    ));
}

add_action('rest_api_init','registrar_api_user_put');


?>