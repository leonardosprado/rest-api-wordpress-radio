<?php



 function usuario_scheme($user_id){

    $user = get_user_by( 'ID', $user_id );
    $user_meta = get_user_meta($user_id);

    $response = array(
        "id"            => $user->user_login,
        "nome"          => $user->user_name,
        "email"         => $user->user_email,
        "primeiroNome"  => $user_meta['first_name'][0],
        "sobrenome"     => $user_meta['last_name'][0],
        "apelido"       => $user_meta['nickname'][0],
        "cep"           => $user_meta['cep'][0],
        "numero"        => $user_meta['numero'][0],
        "rua"           => $user_meta['rua'][0],
        "bairro"        => $user_meta['bairro'][0],
        "cidade"        => $user_meta['cidade'][0],
        "estado"        => $user_meta['estado'][0],
        "biografia"     => $user_meta['description'][0],
        "funcao"        => $user->roles[0],
    );
    return $response;
 }

// Retornar usuario logado. 
function api_usuario_get($request){

    $user = wp_get_current_user();

    $user_id = $user->ID;

    if($user_id > 0){
        $user_meta = get_user_meta($user_id);

        $response = array(
            "id" => $user->user_login,
            "nome" => $user->user_name,
            "email" => $user->user_email,
            "primeiroNome" =>  $user_meta['first_name'][0],
            "sobrenome"    =>  $user_meta['last_name'][0],
            "apelido"      =>  $user_meta['nickname'][0],
            "cep" => $user_meta['cep'][0],
            "numero" => $user_meta['numero'][0],
            "rua" => $user_meta['rua'][0],
            "bairro" => $user_meta['bairro'][0],
            "cidade" => $user_meta['cidade'][0],
            "estado" => $user_meta['estado'][0],
            "biografia" => $user_meta['description'][0],
            "funcao"    =>  $user->roles[0],
        );
    }
    else{

        $response = wp_send_json_error( 'Usuario não possui permissão', 403 );
        // $response =  new WP_REST_Response(array('permissao' => 'Usuario não possui permissão'), 403);
        // $response = new WP_Error('permissao','Usuario não possui permissão',array('status' => 401));
    }

    return rest_ensure_response($response);
}

function registrar_api_usuario_get(){

    register_rest_route('api', '/usuario', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_usuario_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_usuario_get');


// Retornar Lista com todos Usuarios. 
function api_usuarios_get($request){

    $query = array(
        'number' => -1,
        'role__not_in' => 'Subscriber',

    );
    $loop = new WP_User_Query($query);
    $users = $loop->get_results();

    $usuarios = array();

    foreach ($users as $key => $value) {
        $usuarios[] = usuario_scheme($value->ID);
    }
    return rest_ensure_response($usuarios);
}

function registrar_api_usuarios_get(){
    
    register_rest_route('api', '/usuarios', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_usuarios_get',
        ),
    ));
}
add_action('rest_api_init','registrar_api_usuarios_get');


/// Retorna usuario pelo SLUG 
function api_usuario_slug_get($request){
    $useron = wp_get_current_user();
    $user_id =  $useron->ID;
    if($user_id > 0){

        $slug = $request['slug'];
        $user = get_user_by('login', $slug );

        if($user){
        $response = usuario_scheme($user->ID);
        }
        else{
            $response = new WP_Error('naoexiste','Usuario não encontrado',array('status'=>404));
        }
        
        
    } else{
        $response = new WP_Error('Permissao','Usuario não possui permissaão.', array('status'=> 403 ));
    }
    return rest_ensure_response($response); 
   
} 

function registrar_api_usuario_slug_get(){
    register_rest_route('api', '/usuario/(?P<slug>[-\w]+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_usuario_slug_get',
        ),
    ));
}
add_action('rest_api_init','registrar_api_usuario_slug_get');


?>