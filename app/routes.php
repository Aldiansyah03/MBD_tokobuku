<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

include_once __DIR__ . '/../app/tables/buku.php';
include_once __DIR__ . '/../app/tables/pembeli.php';
include_once __DIR__ . '/../app/tables/ulasan.php';
include_once __DIR__ . '/../app/tables/transaksi.php';
include_once __DIR__ . '/../app/tables/detail_transaksi.php';

};