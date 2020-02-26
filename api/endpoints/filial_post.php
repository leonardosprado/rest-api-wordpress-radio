<?php


function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}

function api_filial_post($request){

    $user = wp_get_current_user();
    $user_id =  $user->ID;

    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps); //Retronar a Função do Usuario
    $roles = array_shift($roles);


    if ($user_id > 0 and ($roles === "administrator")) {

        $titulo     = sanitize_text_field($request['titulo']);
        $cidade     = sanitize_text_field($request['cidade']);
        $estado     = sanitize_text_field($request['estado']);
        $post_name  = strtolower(tirarAcentos($cidade));
        $streaming  = sanitize_text_field($request['streaming']);
        
        $usuario_id =  $user->user_login;
        $response   = array(
            'post_author'   =>$user_id,
            'post_type'     => 'filial',
            'post_title'    => $post_name,
            'post_status'   => 'publish',
            'meta_input'    => array(
                'filial'            => $cidade."-".$estado,
                'cidade'            => $cidade,
                'estado'            => $estado,
                'streaming'         => $streaming,
            )
        );
        $post_id = wp_insert_post($response);

        $files = $request->get_file_params();  //Recebe a Imagem em Destaque 

        if($files){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            foreach($files as $file => $array){
                media_handle_upload($file, $post_id); //Faz uplouad das imagem vinculado a Postagem
            }
        }

        $response['id'] = get_post_field('post_name',$post_id);
       
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