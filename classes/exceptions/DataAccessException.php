<?php

/**
 * PH35 Sample14 マスタテーブル管理Slim版 Src07
 *
 * @author Shinzo SAITO
 *
 * ファイル名=DataAccessException.php
 * フォルダ=/ph35/scottadminslim/classes/exceptions/
 */

namespace PH35slim\ShareReports\Classes\exceptions;

use Exception;

/**
 * データ処理中に想定外の事態を検知した時に発生させる例外クラス。
 */
class DataAccessException extends Exception
{
}
