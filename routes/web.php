<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'ProjectsController@index');

Route::post('/projects', 'ProjectsController@store');
Route::delete('/projects/{project}', 'ProjectsController@destroy');
Route::put('/projects/{project}', 'ProjectsController@update');

Route::post('/projects/{project}/tasks', 'TasksController@store');
Route::delete('/projects/{project}/tasks/{task}', 'TasksController@destroy');
Route::put('/projects/{project}/tasks/{task}', 'TasksController@update');
