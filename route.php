<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PH35slim\ShareReports\Classes\middlewares\LoginCheck;
use PH35slim\ShareReports\Classes\controllers\ReportController;
use PH35slim\ShareReports\Classes\controllers\LoginController;
use PH35slim\ShareReports\Classes\entities\Report;

// ログイン画面表示処理
$app->get("/", LoginController::class . ":goLogin");
$app->post("/login", LoginController::class . ":login");
$app->get("/logout", LoginController::class . ":logout");


// レポートリスト表示処理
$app->get('/reports/showList', ReportController::class . ':showReportList')->add(new LoginCheck());
// レポート登録画面表示処理
$app->get('/reports/goAdd', ReportController::class . ':goReportAdd')->add(new LoginCheck());
// レポート登録処理
$app->post('/reports/add', ReportController::class . ':reportAdd')->add(new LoginCheck());
// レポート詳細表示処理
$app->get('/reports/showDetail/{id}', ReportController::class . ':showDetail')->add(new LoginCheck());
// レポート編集画面商事処理
$app->get('/reports/prepareEdit', ReportController::class . ':prepareEdit')->add(new LoginCheck());
// レポート更新処理
$app->post('/reports/showList', function (Request $request, Response $response, $args) {
  return true;
});
// レポート削除確認表示処理
$app->get('/reports/confirmDelete', function (Request $request, Response $response, $args) {
  return true;
});
// レポート削除処理
$app->get('/reports/delete', function (Request $request, Response $response, $args) {
  return true;
});
