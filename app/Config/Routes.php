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
    header("Content-type: application/json; charset=utf-8");
    http_response_code(404);

    echo ('Rota da api inexistente.');
    exit;
});

$routes->group('cms', function($routes) {
    // CMS API

    // USERS

    // Método GET
    $routes->get('users', 'CMS\UserController::getAllUsers');
    $routes->get('users/(:segment)', 'CMS\UserController::getUser/$1');

    // Método POST
    $routes->post('users', 'CMS\UserController::createUser');

    // Método PUT
    $routes->put('users/(:segment)', 'CMS\UserController::updateUser/$1');

    // Método DELETE
    $routes->delete('users/(:segment)', 'CMS\UserController::deleteUser/$1');
});

$routes->group('community', function($routes) {
    // COMMUNITY API

    // POSTS

    // Método GET
    $routes->get('posts/recommended', 'Community\PostController::getAllRecommendedPosts');
    $routes->get('posts/(:segment)', 'Community\PostController::getPost/$1');
    $routes->get('posts', 'Community\PostController::getAllPosts');
    // Método POST
    $routes->post('posts', 'Community\PostController::createPost');
    // Método PUT (QUE ERA PRA SER)
    $routes->post('posts/(:segment)/edit', 'Community\PostController::updatePost/$1');
    // Método DELETE
    $routes->delete('posts/(:segment)', 'Community\PostController::deletePost/$1');

    // COMENTÁRIOS

    // Método GET
    $routes->get('comments/(:any)', 'Community\CommentsController::getAllCommentsFromPost/$1');
    // Método POST
    $routes->post('posts/(:segment)/comments', 'Community\CommentsController::createComment/$1');
    // Método PUT
    $routes->put('comments/(:segment)', 'Community\CommentsController::updateComment/$1');
    // Método DELETE
    $routes->delete('comments/(:segment)', 'Community\CommentsController::deleteComment/$1');

    // Ultilizadores (users)

    // USER

    // Método GET
    $routes->get('posts/user/(:segment)', 'Community\UserController::getPostsByUserId/$1');
    $routes->get('posts/commented/(:segment)', 'Community\UserController::getPostsCommentedByUser/$1');
    $routes->get('posts/saved/(:segment)', 'Community\UserController::getPostsSavedByUser/$1');
//    $routes->get('posts/user/interacted/(:any)', 'Community\UserController::getUserInteractedPosts/$1');
    // Método POST
    $routes->post('posts/saved/(:segment)', 'Community\UserController::savePost/$1');
    $routes->post('login', 'Community\UserController::index');
    $routes->post('user', 'Community\UserController::getUserInfo/$1/$2');
    // Método DELETE
    $routes->delete('posts/saved/(:segment)', 'Community\UserController::deleteSavedPost/$1');

    // LIKE
    // Método GET
    $routes->get('liked/comments/(:segment)', 'Community\LikeController::getCommentsLikedFromUser/$1');
    $routes->get('liked/posts/(:segment)', 'Community\LikeController::getPostsLikedFromUser/$1');
    // Método POST
    $routes->post('liked/posts/(:segment)', 'Community\LikeController::likePost/$1');
    $routes->post('liked/comments/(:segment)', 'Community\LikeController::likeComment/$1');
    // Método DELETE
    $routes->delete('liked/posts/(:segment)', 'Community\LikeController::deleteLikedPost/$1');
    $routes->delete('liked/comments/(:segment)', 'Community\LikeController::deleteLikedComment/$1');

    // API ADMIN (Similar ao usuário, mas dividido especialmente para os admins
    // As operações de posts são similares ao do usuário normal (rota, parametros e payload).

    //Temas

    // Método GET
    $routes->get('themes/pendingUsers', 'Community\ThemeController::getAllPendingUsersFromThemes');
    $routes->get('themes/(:segment)/posts/(:segment)', 'Community\ThemeController::getPostByThemeId/$1/$2');
    $routes->get('themes/(:segment)/posts', 'Community\ThemeController::getPostsByThemeId/$1');
    $routes->get('themes/(:segment)', 'Community\ThemeController::getTheme/$1');
    $routes->get('themes', 'Community\ThemeController::getAllThemes');
    // Método POST
    $routes->post('themes/check/(:segment)', 'Community\ThemeController::checkIfUserBelongsToPrivateTheme/$1');
    $routes->post('themes', 'Community\ThemeController::createTheme');
    $routes->post('themes/(:segment)/enter', 'Community\ThemeController::enterTheme/$1');
    // Método PUT
    $routes->put('themes/(:segment)/refuse', 'Community\ThemeController::refuseUserFromTheme/$1');
    $routes->put('themes/(:segment)/accept', 'Community\ThemeController::acceptUserToTheme/$1');
    $routes->put('themes/(:any)', 'Community\ThemeController::updateTheme/$1');
    // Método DELETE
    $routes->delete('themes/(:any)', 'Community\ThemeController::deleteTheme/$1');
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
