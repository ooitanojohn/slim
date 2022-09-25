<?php

/**
 * PH35 Sample14 マスタテーブル管理Slim版 Src08
 *
 * @author Shinzo SAITO
 *
 * ファイル名=CustomErrorRenderer.php
 * フォルダ=/ph35/scottadminslim/classes/exceptions/
 */

namespace PH35slim\ShareReports\Classes\exceptions;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Error\Renderers\HtmlErrorRenderer;
use Slim\Views\Twig;
use PH35slim\ShareReports\Classes\exceptions\NoLoginException;
use PH35slim\ShareReports\Classes\exceptions\DataAccessException;

/**
 * 自作エラーレンダラクラス。
 */
class CustomErrorRenderer
{
  /**
   * 実行メソッド。
   * NoLoginExceptionとDataAccessExceptionの例外処理を行なっている。
   *
 それ以外の例外が発生した場合は、デフォルトのHTMLエラーレンダラクラス(HtmlErrorRenderer)
 に処理を移管している。
   *
   * @param Throwable $exception 発生した例外。
   * @param bool $displayErrorDetails エラーの詳細を表示させるかどうかのフラグ。
   * @return string レスポンスとして返す文字列。ここでは生成されたHTML文字列。
   */
  public function __invoke(Throwable $exception, bool $displayErrorDetails): string
  {
    $view =
      Twig::create(__DIR__ . '/../../templates');
    if ($exception instanceof NoLoginException) {
      $validationMsgs[] =
        "ログインしていないか、前回ログインしてから一定時間が経過しています。もう一度ログインしなおしてください。";
      $assign["validationMsgs"] = $validationMsgs;
      $returnHtml = $view->fetch("login.html", $assign);
    } elseif ($exception instanceof DataAccessException) {
      $assign["errorMsg"] = $exception->getMessage();
      $returnHtml = $view->fetch("error.html", $assign);
    } else {
      $htmlErrorRenderer = new HtmlErrorRenderer();
      $returnHtml = $htmlErrorRenderer($exception, $displayErrorDetails);
    }
    return $returnHtml;
  }
}
