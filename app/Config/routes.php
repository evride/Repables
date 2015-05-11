<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/feedback', array('controller'=>'pages', 'action'=>'feedback'));
	Router::connect('/', array('controller' => 'pages', 'action' => 'index'));
	Router::connect('/privacypolicy', array('controller'=>'pages', 'action'=>'privacypolicy'));
	Router::connect('/licenseagreement', array('controller'=>'pages', 'action'=>'licenseagreement'));
	Router::connect('/login/*', array('controller'=> 'user', 'action'=> 'login'));
	Router::connect('/logout', array('controller'=> 'user', 'action'=> 'logout'));	
	Router::connect('/register', array('controller'=> 'user', 'action'=> 'register'));
	Router::connect('/resetpassword/*', array('controller'=> 'user', 'action'=> 'resetpassword'));
	
	
	Router::connect('/upload', array('controller'=> 'uploads', 'action'=> 'index'));
	Router::connect('/upload/save', array('controller'=> 'uploads', 'action'=> 'save'));
	Router::connect('/dashboard', array('controller'=>'pages', 'action'=>'dashboard'));
	Router::connect('/explore/*', array('controller'=>'explore', 'action'=>'index'));
	Router::connect('/upload/:action/*', array('controller'=>'uploads'));
	Router::connect('/process/*', array('controller'=>'uploads', 'action'=>'process'));
	
	
	Router::connect('/edit/*', array('controller'=>'uploads', 'action'=>'edit'));
	Router::connect('/r/*', array('controller'=>'items', 'action'=>'index'));
	Router::connect('/u/*', array('controller'=>'profiles', 'action'=>'index'));
	
	Router::connect('/tag/*', array('controller'=>'explore', 'action'=>'tag'));
	Router::connect('/search/*', array('controller'=>'explore', 'action'=>'search'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
