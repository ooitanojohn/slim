<?php

/**
 * PH35 Sample14 マスタテーブル管理Slim版 Src06
 *
 * @author Shinzo SAITO
 *
 * ファイル名=NoLoginException.php
 * フォルダ=/ph35/scottadminslim/classes/exceptions/
 */

namespace PH35slim\ShareReports\Classes\exceptions;

use Exception;
use Psr\Http\Message\ServerRequestInterface as ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as ResponseInterface;

/**
 * 未ログイン状態を検知した時に発生させる例外クラス。
 */
class NoLoginException extends Exception
{
}
