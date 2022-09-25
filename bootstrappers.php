<?php

/**
 * @author Shinzo SAITO
 *
 * ファイル名=bootstrappers.php
 * フォルダ=/ph35/
 */

use PH35slim\ShareReports\Classes\exceptions\CustomErrorRenderer;

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer("text/html", CustomErrorRenderer::class);
