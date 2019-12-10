<?php 

$template_diretorio = get_template_directory();

require_once($template_diretorio . "/custom-post-type/transacao.php");
require_once($template_diretorio . "/custom-post-type/blog.php");
require_once($template_diretorio . "/custom-post-type/galeria_fotos.php");
require_once($template_diretorio . "/custom-post-type/galeria_videos.php");
require_once($template_diretorio . "/custom-post-type/banner.php");

require_once($template_diretorio . "/custom-post-type/promocao.php");

require_once($template_diretorio . "/custom-post-type/promocao_cadastro.php");

require_once($template_diretorio . "/custom-post-type/equipe.php");


// USUARIO DO GERENCIADOR 

require_once($template_diretorio . "/endpoints/usuario_post.php");
require_once($template_diretorio . "/endpoints/usuario_get.php");
require_once($template_diretorio . "/endpoints/usuario_put.php");

// NAO USA 
require_once($template_diretorio . "/endpoints/produto_post.php");
require_once($template_diretorio . "/endpoints/produto_get.php");

// RETORNA CATEGORIAS EXISTENTES
require_once($template_diretorio . "/endpoints/categoria_get.php");

// SUCESSO NEWS 
require_once($template_diretorio . "/endpoints/blog_post.php");
require_once($template_diretorio . "/endpoints/blog_get.php");
require_once($template_diretorio . "/endpoints/blog_put.php");
require_once($template_diretorio . "/endpoints/blog_delete.php");


// IMAGEM 
require_once($template_diretorio . "/endpoints/galeria_imagem_post.php");
require_once($template_diretorio . "/endpoints/galeria_imagem_get.php");
require_once($template_diretorio . "/endpoints/galeria_imagem_delete.php");
require_once($template_diretorio . "/endpoints/galeria_imagem_put.php");


// BANNER
require_once($template_diretorio . "/endpoints/banner_post.php");
require_once($template_diretorio . "/endpoints/banner_get.php");
require_once($template_diretorio . "/endpoints/banner_delete.php");
require_once($template_diretorio . "/endpoints/banner_put.php");

// PROMOCAO 
require_once($template_diretorio . "/endpoints/promocao_post.php");
require_once($template_diretorio . "/endpoints/promocao_get.php");
require_once($template_diretorio . "/endpoints/promocao_put.php");
require_once($template_diretorio . "/endpoints/promocao_delete.php");


// UPLOAD DE IMAGEM RETORNA LINK DO CAMINHO NO SERVIDOR
require_once($template_diretorio . "/endpoints/imagem_post.php");

// OUVINTE / USUARIO DO PORTAL 
require_once($template_diretorio . "/endpoints/ouvinte_post.php");

// PROMOCAO CADASTRATA
require_once($template_diretorio . "/endpoints/promocao_cadastro_post.php");


// MUSICA 
require_once($template_diretorio . "/endpoints/musica_post.php");
require_once($template_diretorio . "/endpoints/musica_get.php");
require_once($template_diretorio . "/endpoints/musica_delete.php");

// Equipe
require_once($template_diretorio . "/endpoints/equipe_post.php");
require_once($template_diretorio . "/endpoints/equipe_get.php");

// Filial 
require_once($template_diretorio . "/endpoints/filial_get.php");
require_once($template_diretorio . "/endpoints/filial_post.php");
require_once($template_diretorio . "/endpoints/filial_delete.php");

// Programacao
require_once($template_diretorio . "/endpoints/programacao_get.php");
require_once($template_diretorio . "/endpoints/programacao_post.php");
require_once($template_diretorio . "/endpoints/programacao_put.php");
require_once($template_diretorio . "/endpoints/programacao_delete.php");



function get_usuario_id_slug($slug){
    
    $query  = new WP_Query(array(
        'number' => 1,
        'search_columns' => array($slug),
    ));
    $users = $query->get_results();

    return $users;
    
}

function get_produto_id_slug($slug){
    $query = new WP_Query(array(
        'name' => $slug,
        'post_type' => 'produto',
        'number_post' => 1,
        'fields' => 'ids'
    ));
    $posts = $query->get_posts();
    return array_shift($posts);
}

function get_posts_id_slug($slug){
    $query = new WP_Query(array(
        'name' => $slug,
        'post_type' => 'blog',
        'number_post' => 1,
        'fields' => 'ids'
    ));
    $posts = $query->get_posts();
    return array_shift($posts);
}


function get_galeria_imagem_id_slug($slug){
   $query = new WP_Query(array(
        'name'          => $slug,
        'post_type'     => 'galeria_fotos',
        'numberposts'   => 1,
        'fields'        => 'ids'
   ));

   $posts = $query->get_posts();
   return array_shift($posts);
}

function get_banner_id_slug($slug){
    $query = new WP_Query(array(
         'name'          => $slug,
         'post_type'     => 'banner',
         'numberposts'   => 1,
         'fields'        => 'ids'
    ));
 
    $posts = $query->get_posts();
    return array_shift($posts);
 }
function get_promocao_id_slug($slug){
    $query = new WP_Query(array(
         'name'          => $slug,
         'post_type'     => 'promocao',
         'numberposts'   => 1,
         'fields'        => 'ids'
    ));
 
    $posts = $query->get_posts();
    return array_shift($posts);
 }

 function get_promocao_cadastrada_id_slug($slug){
    $query = new WP_Query(array(
         'name'          => $slug,
         'post_type'     => 'promocao_cadastrada',
         'numberposts'   => 1,
         'fields'        => 'ids'
    ));
 
    $posts = $query->get_posts();
    return array_shift($posts);
 }
 
function get_top_musica_id_slug($slug){
    $query = new WP_Query(array(
         'name'          => $slug,
         'post_type'     => 'topmusica',
         'numberposts'   => 1,
         'fields'        => 'ids'
    ));
    $posts = $query->get_posts();
    return array_shift($posts);
}


function get_equipe_id_slug($slug){
    $query = new WP_Query(array(
         'name'          => $slug,
         'post_type'     => 'equipe',
         'numberposts'   => 1,
         'fields'        => 'ids'
    ));
    $posts = $query->get_posts();
    return array_shift($posts);
}

function get_filial_id_slug($slug){
    $query = new WP_Query(array(
         'name'          => $slug,
         'post_type'     => 'filial',
         'numberposts'   => 1,
         'fields'        => 'ids'
    ));
    $posts = $query->get_posts();
    return array_shift($posts);
}


function get_programacao_id_slug($slug){
    $query = new WP_Query(array(
         'name'          => $slug,
         'post_type'     => 'programacao',
         'numberposts'   => 1,
         'fields'        => 'ids'
    ));
    $posts = $query->get_posts();
    return array_shift($posts);
}

function expire_token(){
    return time() + (60*60);
}

add_action('jwt_auth_expire', 'expire_token')

?>