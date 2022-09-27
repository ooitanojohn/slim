<?php

use PH35slim\ShareReports\Classes\middlewares\LoginCheck;
use PH35slim\ShareReports\Classes\controllers\ReportController;
use PH35slim\ShareReports\Classes\controllers\LoginController;

// ログイン画面表示処理
$app->get("/", LoginController::class . ":goLogin");
$app->post("/login", LoginController::class . ":login");
$app->get("/logout", LoginController::class . ":logout");


// レポートリスト表示処理
$app->get('/reports/showList', ReportController::class . ':showList')->add(new LoginCheck());
$app->get('/reports/showList/{page}', ReportController::class . ':showList')->add(new LoginCheck());
// レポート登録画面表示処理
$app->get('/reports/goAdd', ReportController::class . ':goAdd')->add(new LoginCheck());
// レポート登録処理
$app->post('/reports/add', ReportController::class . ':add')->add(new LoginCheck());
// レポート詳細表示処理
$app->get('/reports/showDetail/{id}', ReportController::class . ':showDetail')->add(new LoginCheck());
// レポート編集画面商事処理
$app->get('/reports/prepareEdit/{id}', ReportController::class . ':prepareEdit')->add(new LoginCheck());
// レポート更新処理
$app->post('/reports/edit/{id}', ReportController::class . ':edit')->add(new LoginCheck());
// レポート削除確認表示処理
$app->get('/reports/confirmDelete/{id}', ReportController::class . ':confirmDelete')->add(new LoginCheck());
// レポート削除処理
$app->post('/reports/delete/{id}', ReportController::class . ':delete')->add(new LoginCheck());
