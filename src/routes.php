<?php

namespace PHPMaker2021\project1;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// Handle Routes
return function (App $app) {
    // mahasiswa
    $app->any('/mahasiswalist[/{id_mhs}]', MahasiswaController::class . ':list')->add(PermissionMiddleware::class)->setName('mahasiswalist-mahasiswa-list'); // list
    $app->any('/mahasiswaadd[/{id_mhs}]', MahasiswaController::class . ':add')->add(PermissionMiddleware::class)->setName('mahasiswaadd-mahasiswa-add'); // add
    $app->any('/mahasiswaedit[/{id_mhs}]', MahasiswaController::class . ':edit')->add(PermissionMiddleware::class)->setName('mahasiswaedit-mahasiswa-edit'); // edit
    $app->any('/mahasiswadelete[/{id_mhs}]', MahasiswaController::class . ':delete')->add(PermissionMiddleware::class)->setName('mahasiswadelete-mahasiswa-delete'); // delete
    $app->group(
        '/mahasiswa',
        function (RouteCollectorProxy $group) {
            $group->any('/' . Config("LIST_ACTION") . '[/{id_mhs}]', MahasiswaController::class . ':list')->add(PermissionMiddleware::class)->setName('mahasiswa/list-mahasiswa-list-2'); // list
            $group->any('/' . Config("ADD_ACTION") . '[/{id_mhs}]', MahasiswaController::class . ':add')->add(PermissionMiddleware::class)->setName('mahasiswa/add-mahasiswa-add-2'); // add
            $group->any('/' . Config("EDIT_ACTION") . '[/{id_mhs}]', MahasiswaController::class . ':edit')->add(PermissionMiddleware::class)->setName('mahasiswa/edit-mahasiswa-edit-2'); // edit
            $group->any('/' . Config("DELETE_ACTION") . '[/{id_mhs}]', MahasiswaController::class . ':delete')->add(PermissionMiddleware::class)->setName('mahasiswa/delete-mahasiswa-delete-2'); // delete
        }
    );

    // mata_kuliah
    $app->any('/matakuliahlist[/{id_mk}]', MataKuliahController::class . ':list')->add(PermissionMiddleware::class)->setName('matakuliahlist-mata_kuliah-list'); // list
    $app->any('/matakuliahadd[/{id_mk}]', MataKuliahController::class . ':add')->add(PermissionMiddleware::class)->setName('matakuliahadd-mata_kuliah-add'); // add
    $app->any('/matakuliahedit[/{id_mk}]', MataKuliahController::class . ':edit')->add(PermissionMiddleware::class)->setName('matakuliahedit-mata_kuliah-edit'); // edit
    $app->any('/matakuliahdelete[/{id_mk}]', MataKuliahController::class . ':delete')->add(PermissionMiddleware::class)->setName('matakuliahdelete-mata_kuliah-delete'); // delete
    $app->group(
        '/mata_kuliah',
        function (RouteCollectorProxy $group) {
            $group->any('/' . Config("LIST_ACTION") . '[/{id_mk}]', MataKuliahController::class . ':list')->add(PermissionMiddleware::class)->setName('mata_kuliah/list-mata_kuliah-list-2'); // list
            $group->any('/' . Config("ADD_ACTION") . '[/{id_mk}]', MataKuliahController::class . ':add')->add(PermissionMiddleware::class)->setName('mata_kuliah/add-mata_kuliah-add-2'); // add
            $group->any('/' . Config("EDIT_ACTION") . '[/{id_mk}]', MataKuliahController::class . ':edit')->add(PermissionMiddleware::class)->setName('mata_kuliah/edit-mata_kuliah-edit-2'); // edit
            $group->any('/' . Config("DELETE_ACTION") . '[/{id_mk}]', MataKuliahController::class . ':delete')->add(PermissionMiddleware::class)->setName('mata_kuliah/delete-mata_kuliah-delete-2'); // delete
        }
    );

    // pengguna
    $app->any('/penggunalist[/{id_pengguna}]', PenggunaController::class . ':list')->add(PermissionMiddleware::class)->setName('penggunalist-pengguna-list'); // list
    $app->any('/penggunaadd[/{id_pengguna}]', PenggunaController::class . ':add')->add(PermissionMiddleware::class)->setName('penggunaadd-pengguna-add'); // add
    $app->any('/penggunaedit[/{id_pengguna}]', PenggunaController::class . ':edit')->add(PermissionMiddleware::class)->setName('penggunaedit-pengguna-edit'); // edit
    $app->any('/penggunadelete[/{id_pengguna}]', PenggunaController::class . ':delete')->add(PermissionMiddleware::class)->setName('penggunadelete-pengguna-delete'); // delete
    $app->group(
        '/pengguna',
        function (RouteCollectorProxy $group) {
            $group->any('/' . Config("LIST_ACTION") . '[/{id_pengguna}]', PenggunaController::class . ':list')->add(PermissionMiddleware::class)->setName('pengguna/list-pengguna-list-2'); // list
            $group->any('/' . Config("ADD_ACTION") . '[/{id_pengguna}]', PenggunaController::class . ':add')->add(PermissionMiddleware::class)->setName('pengguna/add-pengguna-add-2'); // add
            $group->any('/' . Config("EDIT_ACTION") . '[/{id_pengguna}]', PenggunaController::class . ':edit')->add(PermissionMiddleware::class)->setName('pengguna/edit-pengguna-edit-2'); // edit
            $group->any('/' . Config("DELETE_ACTION") . '[/{id_pengguna}]', PenggunaController::class . ':delete')->add(PermissionMiddleware::class)->setName('pengguna/delete-pengguna-delete-2'); // delete
        }
    );

    // kuliah
    $app->any('/kuliahlist[/{id_kuliah}]', KuliahController::class . ':list')->add(PermissionMiddleware::class)->setName('kuliahlist-kuliah-list'); // list
    $app->any('/kuliahadd[/{id_kuliah}]', KuliahController::class . ':add')->add(PermissionMiddleware::class)->setName('kuliahadd-kuliah-add'); // add
    $app->any('/kuliahedit[/{id_kuliah}]', KuliahController::class . ':edit')->add(PermissionMiddleware::class)->setName('kuliahedit-kuliah-edit'); // edit
    $app->any('/kuliahdelete[/{id_kuliah}]', KuliahController::class . ':delete')->add(PermissionMiddleware::class)->setName('kuliahdelete-kuliah-delete'); // delete
    $app->any('/kuliahpreview', KuliahController::class . ':preview')->add(PermissionMiddleware::class)->setName('kuliahpreview-kuliah-preview'); // preview
    $app->group(
        '/kuliah',
        function (RouteCollectorProxy $group) {
            $group->any('/' . Config("LIST_ACTION") . '[/{id_kuliah}]', KuliahController::class . ':list')->add(PermissionMiddleware::class)->setName('kuliah/list-kuliah-list-2'); // list
            $group->any('/' . Config("ADD_ACTION") . '[/{id_kuliah}]', KuliahController::class . ':add')->add(PermissionMiddleware::class)->setName('kuliah/add-kuliah-add-2'); // add
            $group->any('/' . Config("EDIT_ACTION") . '[/{id_kuliah}]', KuliahController::class . ':edit')->add(PermissionMiddleware::class)->setName('kuliah/edit-kuliah-edit-2'); // edit
            $group->any('/' . Config("DELETE_ACTION") . '[/{id_kuliah}]', KuliahController::class . ':delete')->add(PermissionMiddleware::class)->setName('kuliah/delete-kuliah-delete-2'); // delete
            $group->any('/' . Config("PREVIEW_ACTION") . '', KuliahController::class . ':preview')->add(PermissionMiddleware::class)->setName('kuliah/preview-kuliah-preview-2'); // preview
        }
    );

    // v_mhs
    $app->any('/vmhslist', VMhsController::class . ':list')->add(PermissionMiddleware::class)->setName('vmhslist-v_mhs-list'); // list
    $app->group(
        '/v_mhs',
        function (RouteCollectorProxy $group) {
            $group->any('/' . Config("LIST_ACTION") . '', VMhsController::class . ':list')->add(PermissionMiddleware::class)->setName('v_mhs/list-v_mhs-list-2'); // list
        }
    );

    // v_mk
    $app->any('/vmklist', VMkController::class . ':list')->add(PermissionMiddleware::class)->setName('vmklist-v_mk-list'); // list
    $app->group(
        '/v_mk',
        function (RouteCollectorProxy $group) {
            $group->any('/' . Config("LIST_ACTION") . '', VMkController::class . ':list')->add(PermissionMiddleware::class)->setName('v_mk/list-v_mk-list-2'); // list
        }
    );

    // v_kuliah
    $app->any('/vkuliahlist', VKuliahController::class . ':list')->add(PermissionMiddleware::class)->setName('vkuliahlist-v_kuliah-list'); // list
    $app->group(
        '/v_kuliah',
        function (RouteCollectorProxy $group) {
            $group->any('/' . Config("LIST_ACTION") . '', VKuliahController::class . ':list')->add(PermissionMiddleware::class)->setName('v_kuliah/list-v_kuliah-list-2'); // list
        }
    );

    // error
    $app->any('/error', OthersController::class . ':error')->add(PermissionMiddleware::class)->setName('error');

    // personal_data
    $app->any('/personaldata', OthersController::class . ':personaldata')->add(PermissionMiddleware::class)->setName('personaldata');

    // login
    $app->any('/login', OthersController::class . ':login')->add(PermissionMiddleware::class)->setName('login');

    // logout
    $app->any('/logout', OthersController::class . ':logout')->add(PermissionMiddleware::class)->setName('logout');

    // Swagger
    $app->get('/' . Config("SWAGGER_ACTION"), OthersController::class . ':swagger')->setName(Config("SWAGGER_ACTION")); // Swagger

    // Index
    $app->any('/[index]', OthersController::class . ':index')->add(PermissionMiddleware::class)->setName('index');

    // Route Action event
    if (function_exists(PROJECT_NAMESPACE . "Route_Action")) {
        Route_Action($app);
    }

    /**
     * Catch-all route to serve a 404 Not Found page if none of the routes match
     * NOTE: Make sure this route is defined last.
     */
    $app->map(
        ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
        '/{routes:.+}',
        function ($request, $response, $params) {
            $error = [
                "statusCode" => "404",
                "error" => [
                    "class" => "text-warning",
                    "type" => Container("language")->phrase("Error"),
                    "description" => str_replace("%p", $params["routes"], Container("language")->phrase("PageNotFound")),
                ],
            ];
            Container("flash")->addMessage("error", $error);
            return $response->withStatus(302)->withHeader("Location", GetUrl("error")); // Redirect to error page
        }
    );
};
