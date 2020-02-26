<?php 

function api_tv_get($request){
    $page_title = "tv-sucesso";

    $page_check = get_page_by_title($page_title);
    return($page_check);
}

function registrar_api_tv_get(){

    register_rest_route('api', '/tvsucesso', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_tv_get',
        ),
    ));
}

add_action('rest_api_init','registrar_api_tv_get');


?>
