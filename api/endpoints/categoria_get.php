<?php 
    function get_categoria_id_slug($slug){
        $query = new WP_Query(array(
            'post_id' => $slug,
            'post_type' => 'blog',
            'number_post' => 1,
            'fields' => 'ids'
        ));
        $posts = $query->get_posts();
        return array_shift($posts);
    }

    function categoria_scheme($slug){
        $post_id = $slug;

        if($post_id){
            $post_meta = get_post_meta($post_id);
            $response = array(
                'categoria_blog' => $post_meta['categoria_blog'][0],
            );
        }
        else{
            $response = new WP_Error('naoexiste','Categoria não encontrada', array('status' => 404));

        }
        return $response;
    }


    function api_categoria_get($request){

        $query = array(
            'post_type'     =>'blog',
            'posts_per_page' => '-1',
            'paged'         => '0',
        );

        $loop = new WP_Query($query);
        $posts = $loop->posts;
        $id = array();
        $categoria = array();
        foreach ($posts as $key => $value) {
            $id[] = $value->ID;
            $categoria[] = categoria_scheme($value->ID);
        }

        return rest_ensure_response($categoria);

    }




    function registrar_api_categoria_get(){

        register_rest_route('api','/categorias', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_categoria_get',
            ),
        ));
    }

    add_action('rest_api_init','registrar_api_categoria_get');



?>