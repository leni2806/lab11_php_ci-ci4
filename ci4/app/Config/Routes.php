<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 1. Rute Halaman Statis & Home
$routes->get('/', 'Home::index');
$routes->get('/about', 'Page::about');
$routes->get('/contact', 'Page::contact');
$routes->get('/faqs', 'Page::faqs');

// 2. Rute Artikel untuk User (Public)
$routes->get('/artikel', 'Artikel::index'); 
$routes->get('/artikel/(:any)', 'Artikel::view/$1');

// 3. Rute Login & Logout (DITARUH DI LUAR GRUP ADMIN)
$routes->get('user/login', 'User::login');
$routes->post('user/login', 'User::login');
$routes->get('user/logout', 'User::logout');

$routes->resource('post');

// 4. Grup Rute khusus Admin
$routes->group('admin', function($routes) { 
    $routes->get('artikel', 'Artikel::admin_index'); 
    $routes->add('artikel/add', 'Artikel::add'); 
    $routes->add('artikel/edit/(:any)', 'Artikel::edit/$1'); 
    $routes->get('artikel/delete/(:any)', 'Artikel::delete/$1');
    // Rute untuk halaman utama AJAX
    $routes->get('ajax', 'AjaxController::index');

    // Rute untuk mengambil data JSON (digunakan oleh jQuery)
    $routes->get('ajax/getData', 'AjaxController::getData');

    // --- BARU: Rute untuk Ambil Data Satuan & Simpan (Tambah/Edit) ---
    $routes->get('ajax/getById/(:num)', 'AjaxController::getById/$1'); 
    $routes->post('ajax/save', 'AjaxController::save');
    
    // Rute untuk menghapus data artikel via AJAX
    $routes->post('ajax/delete/(:num)', 'AjaxController::delete/$1'); 

});
