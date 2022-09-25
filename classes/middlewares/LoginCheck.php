<?php

/**
 * PH35 Sample14 マスタテーブル管理Slim版 Src09
 *
 * @author Shinzo SAITO
 *
 * ファイル名=LoginCheck.php
 * フォルダ=/ph35/scottadminslim/classes/middlewares/
 */

namespace PH35slim\ShareReports\Classes\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PH35slim\ShareReports\Classes\exceptions\NoLoginException;

/**
 * ログインチェックミドルウェアクラス。
 */
class LoginCheck
{
  /**
   * ログインチェック処理。
   * ログインされていない状態を検知したらNoLoginExceptionが発生する。
   *
   * @param ServerRequestInterface $request リクエストオブジェクト。
   * @param RequestHandlerInterface $handler リクエストハンドラオブジェクト。
   * @return ResponseInterface レスポンスオブジェクト。
   */
  public function __invoke(ServerRequestInterface $request, RequestHandlerInterface
  $handler): ResponseInterface
  {
    if (
      !isset($_SESSION["loginFlg"]) || $_SESSION["loginFlg"] == false ||
      !isset($_SESSION["id"]) || !isset($_SESSION["name"]) || !isset($_SESSION["auth"])
    ) {
      // $result = true;
      throw new NoLoginException();
    }
    $response = $handler->handle($request);
    return $response;
  }
}
