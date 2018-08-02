<?php
$slim->any('/', '\HomeCtrl:index')->setName('home');

$slim->any('/login/', '\AdminCtrl:login')->setName('login');
$slim->get('/logout/', '\AdminCtrl:logout')->setName('logout');

$slim->get('/sites/', '\SitesCtrl:index')->setName('sites_index');
$slim->any('/sites/add/', '\SitesCtrl:add')->setName('sites_add');