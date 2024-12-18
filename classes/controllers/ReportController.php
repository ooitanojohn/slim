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
  public function showList(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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
      // join
      $dist = $reportDAO->findJoin();
      var_dump($dist);
      
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
    // pagination ５件表示
    if (!isset($args['page'])) {
      $args['page'] = 1;
    }
    $lastPage = (int)$args['page'] * 5;
    $assign['page'] = $args['id'];
    $assign['pageMax'] = ceil(count($reportList) / 5);
    $assign['reportList'] = array_slice($reportList, $lastPage - 5, $lastPage);

    $returnResponse = $this->view->render(
      $response,
      "report/reportList.html",
      $assign
    );
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

    $flashMessages = $this->flash->getMessages();
    if (isset($flashMessages)) {
      $assign["flashMsg"] = $this->flash->getFirstMessage("flashMsg");
    }
    $this->cleanSession();

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
    // var_dump($assign);
    $returnResponse = $this->view->render($response, $templatePath, $assign);
    return $returnResponse;
  }

  /**
   * 部門情報登録画面表示処理。
   */
  public function goAdd(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $templatePath = "report/reportAdd.html";
    $assign = [];

    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      // レポート詳細カラムを取得
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
  public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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
        $reportcateDAO = new ReportcateDAO($db);
        $assign['reportcates'] = $reportcateDAO->findAll();
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
    return $returnResponse;
  }

  /**
   * 部門情報更新画面表示処理。
   */
  public function prepareEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $templatePath = "report/reportEdit.html";
    $assign = [];
    $editReportId = $args["id"];
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      // レポート情報
      $reportDAO = new reportDAO($db);
      $report = $reportDAO->findByReportId($editReportId);
      // レポート詳細カラム
      $reportcateDAO = new ReportcateDAO($db);
      $assign['reportcates'] = $reportcateDAO->findAll();
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
  public function edit(ServerRequestInterface $request, ResponseInterface
  $response, array $args): ResponseInterface
  {
    $templatePath = "report/reportEdit.html";
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
    $report->setId($args['id']);
    $report->setRpDate($addRpDate);
    $report->setRpTimeFrom($addRpTimeFrom);
    $report->setRpTimeTo($addRpTimeTo);
    $report->setRpContent($addRpContent);
    $report->setReportcateId($addReportcateId);
    $report->setUserId($_SESSION['id']);

    // validate
    $validationMsgs = [];
    if (empty($addRpContent)) {
      $validationMsgs[] = "作業内容の入力は必須です。";
    }
    if (strtotime($addRpDate . ' ' . $addRpTimeFrom) > strtotime($addRpDate . ' ' . $addRpTimeTo)) {
      $validationMsgs[] = '作業開始時刻は作業終了時刻の以前の時刻である必要があります。';
    }

    $flag = false;
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      $reportDAO = new reportDAO($db);
      if (empty($validationMsgs)) {
        $result = $reportDAO->update($report);
        if ($result) {
          $isRedirect = true;
          $this->flash->addMessage(
            "flashMsg",
            "レポートID" . $report->getId() . "でレポート情報を更新しました。"
          );
        } else {
          throw new
            DataAccessException("情報更新に失敗しました。もう一度はじめからやり直してください。");
        }
      } else {
        // レポート詳細カラム
        $reportcateDAO = new ReportcateDAO($db);
        $assign['reportcates'] = $reportcateDAO->findAll();
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
        "/reports/showDetail/" . $report->getId()
      );
    } else {
      $returnResponse = $this->view->render($response, $templatePath, $assign);
    }
    return $returnResponse;
  }

  /**
   * 部門情報削除確認画面表示処理。
   */
  public function confirmDelete(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
  ): ResponseInterface {
    $templatePath = "report/reportConfirmDelete.html";
    $assign = [];

    $reportId = $args["id"];
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      $reportDAO = new reportDAO($db);
      $report = $reportDAO->findByReportId($reportId);
      if (empty($report)) {
        throw new DataAccessException("レポート情報の取得に失敗しました。");
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
   * 部門情報削除処理。
   */
  public function delete(ServerRequestInterface $request, ResponseInterface
  $response, array $args): ResponseInterface
  {
    try {
      $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
      $reportDAO = new reportDAO($db);
      $result = $reportDAO->delete($args['id']);
      if ($result) {
        $this->flash->addMessage(
          "flashMsg",
          "レポートID" . $args['id'] . "の部門情報を削除しました。"
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
    // ログイン情報
    $assign['session'] = $_SESSION;
    $returnResponse = $response->withStatus(302)->withHeader(
      "Location",
      "/reports/showList"
    );
    return $returnResponse;
  }
}
