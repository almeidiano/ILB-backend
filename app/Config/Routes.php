<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);

$routes->set404Override(function(){
    $status = [
        'http_code' => '404',
        'message' => "Rota da api inexistente",
        'error' => true
    ];

    header("Content-type: application/json; charset=utf-8");
    http_response_code(404);
    echo json_encode($status);
    exit;
});

$routes->group('community', function($routes) {
    // API

    // POSTS

    // Método GET
    $routes->get('posts/recommended', 'PostController::getAllRecommendedPosts');
    $routes->get('posts/(:segment)', 'PostController::getPost/$1');
    $routes->get('posts', 'PostController::getAllPosts');
    // Método POST
    $routes->post('posts', 'PostController::createPost');
    // Método PUT (QUE ERA PRA SER)
    $routes->post('posts/(:segment)/edit', 'PostController::updatePost/$1');
    // Método DELETE
    $routes->delete('posts/(:segment)', 'PostController::deletePost/$1');

    // COMENTÁRIOS

    // Método POST
    $routes->post('posts/(:segment)/comments', 'CommentsController::createComment/$1');
    // Método PUT
    $routes->put('comments/(:segment)', 'CommentsController::updateComment/$1');
    // Método DELETE
    $routes->delete('comments/(:segment)', 'CommentsController::deleteComment/$1');

    // Ultilizadores (users)

    // USER

    // Método GET
    $routes->get('posts/user/(:segment)', 'UserController::getPostsByUserId/$1');
    $routes->get('posts/commented/(:segment)', 'UserController::getPostsCommentedByUser/$1');
    $routes->get('posts/saved/(:segment)', 'UserController::getPostsSavedByUser/$1');
//    $routes->get('posts/user/interacted/(:any)', 'UserController::getUserInteractedPosts/$1');
    // Método POST
    $routes->post('posts/saved/(:segment)', 'UserController::savePost/$1');
    $routes->post('login', 'UserController::index');
    $routes->post('user', 'UserController::getUserInfo/$1/$2');
    // Método DELETE
    $routes->delete('posts/saved/(:segment)', 'UserController::deleteSavedPost/$1');

    // LIKE
    // Método GET
    $routes->get('comments/liked/(:segment)', 'LikeController::getCommentsLikedFromUser/$1');
    $routes->get('posts/liked/(:segment)', 'LikeController::getPostsLikedFromUser/$1');
    // Método POST
    $routes->post('posts/liked/(:segment)', 'LikeController::likePost/$1');
    $routes->post('comments/liked/(:segment)', 'LikeController::likeComment/$1');
    // Método DELETE
    $routes->delete('posts/liked/(:segment)', 'LikeController::deleteLikedPost/$1');
    $routes->delete('comments/liked/(:segment)', 'LikeController::deleteLikedComment/$1');

    // API ADMIN (Similar ao usuário, mas dividido especialmente para os admins
    // As operações de posts são similares ao do usuário normal (rota, parametros e payload).

    //Temas

    // Método GET
    $routes->get('themes/pendingUsers', 'ThemeController::getAllPendingUsersFromThemes');
    $routes->get('themes/(:segment)/posts/(:segment)', 'ThemeController::getPostByThemeId/$1/$2');
    $routes->get('themes/(:segment)/posts', 'ThemeController::getPostsByThemeId/$1');
    $routes->get('themes/(:segment)', 'ThemeController::getTheme/$1');
    $routes->get('themes', 'ThemeController::getAllThemes');
    // Método POST
    $routes->post('themes/check/(:segment)', 'ThemeController::checkIfUserBelongsToPrivateTheme/$1');
    $routes->post('themes', 'ThemeController::createTheme');
    $routes->post('themes/(:segment)/enter', 'ThemeController::enterTheme/$1');
    // Método PUT
    $routes->put('themes/(:segment)/refuse', 'ThemeController::refuseUserFromTheme/$1');
    $routes->put('themes/(:segment)/accept', 'ThemeController::acceptUserToTheme/$1');
    $routes->put('themes/(:any)', 'ThemeController::updateTheme/$1');
    // Método DELETE
    $routes->delete('themes/(:any)', 'ThemeController::deleteTheme/$1');
});

// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/community/posts', 'Home::index');
//$routes->get('/community/posts/(:any)', 'Home::getPost/$1');
//$routes->post('/community/posts', 'Home::createPost');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
