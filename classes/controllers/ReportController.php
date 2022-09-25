<?php

/**
 * @author Shinzo SAITO
 *
 * ファイル名=reportController.php
 * フォルダ=/ph35/scottadminslim/classes/controllers/
 */

namespace PH35slim\ShareReports\Classes\controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use PH35slim\ShareReports\Classes\Conf;
use PH35slim\ShareReports\Classes\exceptions\DataAccessException;
use PH35slim\ShareReports\Classes\entities\Report;
use PH35slim\ShareReports\Classes\daos\ReportDAO;
use PH35slim\ShareReports\Classes\daos\ReportcateDAO;
use PH35slim\ShareReports\Classes\daos\UserDAO;
use PH35slim\ShareReports\Classes\controllers\ParentController;

/**
 * 部門情報管理に関するコントローラクラス。
 */
class ReportController extends ParentController
{
  /**
   * 部門情報リスト画面表示処理。
   */
  public function showReportList(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    // 各flashMsg取得
    $flashMessages = $this->flash->getMessages();
    if (isset($flashMessages)) {
      $assign["flashMsg"] = $this->flash->getFirstMessage("flashMsg");
    }
    $this->cleanSession();
    // 各情報取得
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      // レポート一覧
      $reportDAO = new ReportDAO($db);
      $reportList = $reportDAO->findAll();
      $assign["reportList"] = $reportList;
      // ユーザ一覧
      $userDAO = new UserDAO($db);
      $userList = $userDAO->findAll();
      $assign['userList'] = $userList;
    } catch (PDOException $ex) {
      $exCode = $ex->getCode();
      throw new DataAccessException("DB接続に失敗しました。", $exCode, $ex);
    } finally {
      $db = null;
    }
    // ログイン情報
    $assign['session'] = $_SESSION;
    $returnResponse = $this->view->render(
      $response,
      "report/reportList.html",
      $assign
    );
    var_dump($assign);
    return $returnResponse;
  }
  /**
   * レポート詳細画面表示処理。
   */
  public function showDetail(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $templatePath = "report/reportDetail.html";
    $assign = [];
    $reportId = $args["id"];
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      // レポート情報
      $reportDAO = new reportDAO($db);
      $report = $reportDAO->findByReportId($reportId);
      if (empty($report)) {
        throw new DataAccessException("レポート情報の取得に失敗しました。");
      } else {
        $assign["report"] = $report;
      }
      // user情報
      $userDAO = new userDAO($db);
      $user = $userDAO->findByUserId($report->getUserId());
      if (empty($user)) {
        throw new DataAccessException("user情報の取得に失敗しました。");
      } else {
        $assign["user"] = $user;
      } // 作業種類情報
      $reportcateDAO = new reportcateDAO($db);
      $reportcate = $reportcateDAO->findByReportcateId($report->getReportcateId());
      if (empty($reportcate)) {
        throw new DataAccessException("レポート情報の取得に失敗しました。");
      } else {
        $assign["reportcate"] = $reportcate;
      }
    } catch (PDOException $ex) {
      $exCode = $ex->getCode();
      throw new DataAccessException("DB接続に失敗しました。", $exCode, $ex);
    } finally {
      $db = null;
    }
    // ログイン情報
    $assign['session'] = $_SESSION;
    var_dump($assign);
    $returnResponse = $this->view->render($response, $templatePath, $assign);
    return $returnResponse;
  }

  /**
   * 部門情報登録画面表示処理。
   */
  public function goReportAdd(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $templatePath = "report/reportAdd.html";
    $assign = [];
    // レポート詳細カラムを取得
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      $reportcateDAO = new ReportcateDAO($db);
      $assign['reportcates'] = $reportcateDAO->findAll();
    } catch (PDOException $ex) {
      $exCode = $ex->getCode();
      throw new DataAccessException("DB接続に失敗しました。", $exCode, $ex);
    } finally {
      $db = null;
    }
    // ログイン情報
    $assign['session'] = $_SESSION;
    $returnResponse = $this->view->render($response, $templatePath, $assign);
    return $returnResponse;
  }

  /**
   * 部門情報登録処理。
   */
  public function reportAdd(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $templatePath = "report/reportAdd.html";
    $isRedirect = false;
    $assign = [];

    $postParams = $request->getParsedBody();
    foreach ($postParams as $key => $val) {
      $val = str_replace([' ', '　'], "", $val);
      ${$key} = trim($val);
    }
    $addRpDate = $addRpDateYear . '-' . $addRpDateMonth . '-' . $addRpDateDay;
    $addRpTimeFrom = $addRpTimeFromHour . ':' . $addRpTimeFromTime;
    $addRpTimeTo = $addRpTimeToHour . ':' . $addRpTimeToTime;

    $report = new report();
    $report->setRpDate($addRpDate);
    $report->setRpTimeFrom($addRpTimeFrom);
    $report->setRpTimeTo($addRpTimeTo);
    $report->setRpContent($addRpContent);
    $report->setReportcateId($addReportcateId);
    $report->setUserId($_SESSION['id']);


    // バリデート
    $validationMsgs = [];
    if (empty($addRpContent)) {
      $validationMsgs[] = "作業内容の入力は必須です。";
    }
    if (strtotime($addRpDate . ' ' . $addRpTimeFrom) > strtotime($addRpDate . ' ' . $addRpTimeTo)) {
      $validationMsgs[] = '作業開始時刻は作業終了時刻の以前の時刻である必要があります。';
    }

    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      $reportDAO = new reportDAO($db);
      if (empty($validationMsgs)) {
        $dpId = $reportDAO->insert($report);
        if ($dpId === -1) {
          throw new
            DataAccessException("情報登録に失敗しました。もう一度はじめからやり直してください。");
        } else {
          $isRedirect = true;
          $this->flash->addMessage(
            "flashMsg",
            "レポートID" . $dpId . "でレポート情報を登録しました。"
          );
        }
      } else {
        $assign["report"] = $report;
        $assign["validationMsgs"] = $validationMsgs;
      }
    } catch (PDOException $ex) {
      $exCode = $ex->getCode();
      throw new DataAccessException("DB接続に失敗しました。", $exCode, $ex);
    } finally {
      $db = null;
    }
    // ログイン情報
    $assign['session'] = $_SESSION;
    if ($isRedirect) {
      $returnResponse = $response->withStatus(302)->withHeader(
        "Location",
        "/reports/showList"
      );
    } else {
      $returnResponse = $this->view->render($response, $templatePath, $assign);
    }
    var_dump($assign);
    return $returnResponse;
  }

  /**
   * 部門情報更新画面表示処理。
   */
  public function prepareEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $templatePath = "report/reportEdit.html";
    $assign = [];

    $editreportId = $args["dpId"];
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      $reportDAO = new reportDAO($db);
      $report = $reportDAO->findByPK($editreportId);
      if (empty($report)) {
        throw new DataAccessException("部門情報の取得に失敗しました。");
      } else {
        $assign["report"] = $report;
      }
    } catch (PDOException $ex) {
      $exCode = $ex->getCode();
      throw new DataAccessException("DB接続に失敗しました。", $exCode, $ex);
    } finally {
      $db = null;
    }
    // ログイン情報
    $assign['session'] = $_SESSION;
    $returnResponse = $this->view->render($response, $templatePath, $assign);
    return $returnResponse;
  }

  /**
   * 部門情報編集処理。
   */
  public function reportEdit(ServerRequestInterface $request, ResponseInterface
  $response, array $args): ResponseInterface
  {
    $templatePath = "report/reportEdit.html";
    $isRedirect = false;
    $assign = [];

    $postParams = $request->getParsedBody();
    $editDpId = $postParams["editDpId"];
    $editDpNo = $postParams["editDpNo"];
    $editDpName = $postParams["editDpName"];
    $editDpLoc = $postParams["editDpLoc"];
    $editDpName = str_replace(" ", " ", $editDpName);
    $editDpLoc = str_replace(" ", " ", $editDpLoc);
    $editDpName = trim($editDpName);
    $editDpLoc = trim($editDpLoc);

    $report = new report();
    $report->setId($editDpId);
    $report->setDpNo($editDpNo);
    $report->setDpName($editDpName);
    $report->setDpLoc($editDpLoc);

    $validationMsgs = [];

    if (empty($editDpName)) {
      $validationMsgs[] = "部門名の入力は必須です。";
    }

    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      $reportDAO = new reportDAO($db);
      $reportDB = $reportDAO->findByDpNo($report->getDpNo());
      if (!empty($reportDB) && $reportDB->getId() != $editDpId) {
        $validationMsgs[] =
          "その部門番号はすでに使われています。別のものを指定してください。";
      }
      if (empty($validationMsgs)) {
        $result = $reportDAO->update($report);
        if ($result) {
          $isRedirect = true;
          $this->flash->addMessage(
            "flashMsg",
            "部門ID" . $editDpId . "で部門情報を更新しました。"
          );
        } else {
          throw new
            DataAccessException("情報更新に失敗しました。もう一度はじめからやり直してください。");
        }
      } else {
        $assign["report"] = $report;
        $assign["validationMsgs"] = $validationMsgs;
      }
    } catch (PDOException $ex) {
      $exCode = $ex->getCode();
      throw new DataAccessException("DB接続に失敗しました。", $exCode, $ex);
    } finally {
      $db = null;
    }

    if ($isRedirect) {
      $returnResponse = $response->withStatus(302)->withHeader(
        "Location",
        "/ph35/scottadminslim/public/report/showreportList"
      );
    } else {
      $returnResponse = $this->view->render($response, $templatePath, $assign);
    }
    return $returnResponse;
  }

  /**
   * 部門情報削除確認画面表示処理。
   */
  public function confirmreportDelete(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
  ): ResponseInterface {
    $templatePath = "report/reportConfirmDelete.html";
    $assign = [];

    $editreportId = $args["dpId"];
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      $reportDAO = new reportDAO($db);
      $report = $reportDAO->findByPK($editreportId);
      if (empty($report)) {
        throw new DataAccessException("部門情報の取得に失敗しました。");
      } else {
        $assign["report"] = $report;
      }
    } catch (PDOException $ex) {
      $exCode = $ex->getCode();
      throw new DataAccessException("DB接続に失敗しました。", $exCode, $ex);
    } finally {
      $db = null;
    }
    $returnResponse = $this->view->render($response, $templatePath, $assign);
    return $returnResponse;
  }

  /**
   * 部門情報削除処理。
   */
  public function reportDelete(ServerRequestInterface $request, ResponseInterface
  $response, array $args): ResponseInterface
  {
    $postParams = $request->getParsedBody();
    $deletereportId = $postParams["deletereportId"];
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      $reportDAO = new reportDAO($db);
      $result = $reportDAO->delete($deletereportId);
      if ($result) {
        $this->flash->addMessage(
          "flashMsg",
          "部門ID" . $deletereportId . "の部門情報を削除しました。"
        );
      } else {
        throw new
          DataAccessException("情報削除に失敗しました。もう一度はじめからやり直してください。");
      }
    } catch (PDOException $ex) {
      $exCode = $ex->getCode();
      throw new DataAccessException("DB接続に失敗しました。", $exCode, $ex);
    } finally {
      $db = null;
    }
    $returnResponse = $response->withStatus(302)->withHeader(
      "Location",
      "/ph35/scottadminslim/public/report/showreportList"
    );
    return $returnResponse;
  }
}
