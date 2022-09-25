<?php

/**
 * ReportのDAO
 * @author Syuto Niimi
 * name=ReportDAO.php
 * dir=
 */

namespace PH35slim\ShareReports\Classes\daos;

use PDO;
use PH35slim\ShareReports\Classes\entities\Report;

class ReportDAO
{
  /**
   * @var PDO DB接続オブジェクト
   */
  private PDO $db;

  /**
   * コンストラクタ
   *
   * @param PDO $db DB接続オブジェクト
   */
  public function __construct(PDO $db)
  {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $this->db = $db;
  }
  /**
   * レポートリスト全取得
   *
   * @return Report Reportオブジェクト 該当なしの場合null
   */
  public function findAll(): array
  {
    $sql = "SELECT * FROM reports ORDER BY rp_date DESC";
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute();
    $reportList = [];
    while ($row = $stmt->fetch()) {
      $report = new Report();
      $report->setId($row["id"]);
      $report->setRpDate($row["rp_date"]);
      $report->setRpTimeFrom($row["rp_time_from"]);
      $report->setRpTimeTo($row["rp_time_to"]);
      $report->setRpContent($row["rp_content"]);
      $report->setRpCreatedAt($row["rp_created_at"]);
      $report->setReportcateId($row["reportcate_id"]);
      $report->setUserId($row["user_id"]);
      $reportList[$row['id']] = $report;
    }
    return $reportList;
  }
  /**
   * レポートIDによる検索。
   *
   * @param int $reportId ログインID。
   * @return Report 該当するreportオブジェクト。ただし、該当データがない場合はnull。
   */
  public function findByReportId(int $loginId): ?Report
  {
    $sql = "SELECT * FROM reports WHERE id = :loginId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":loginId", $loginId, PDO::PARAM_INT);
    $result = $stmt->execute();
    $report = null;
    if ($result && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $report = new Report();
      $report->setId($row["id"]);
      $report->setRpDate($row["rp_date"]);
      $report->setRpTimeFrom($row["rp_time_from"]);
      $report->setRpTimeTo($row["rp_time_to"]);
      $report->setRpContent($row["rp_content"]);
      $report->setRpCreatedAt($row["rp_created_at"]);
      $report->setReportcateId($row["reportcate_id"]);
      $report->setUserId($row["user_id"]);
    }
    return $report;
  }
  /**
   * レポート新規作成画面
   * @param Report
   * @return integer 登録成功 = 登録ID 登録失敗 = -1
   */
  public function insert(Report $report): int
  {
    $sqlInsert = "INSERT INTO reports (rp_date,rp_time_from,rp_time_to,rp_content,rp_created_at,reportcate_id,user_id) VALUES(:rp_date,:rp_time_from,:rp_time_to,:rp_content, NOW(),:reportcate_id,:user_id)";
    $stmt = $this->db->prepare($sqlInsert);
    $stmt->bindvalue(':rp_date', $report->getRpDate(), PDO::PARAM_STR);
    $stmt->bindvalue(':rp_time_from', $report->getRpTimeFrom(), PDO::PARAM_STR);
    $stmt->bindvalue(':rp_time_to', $report->getRpTimeTo(), PDO::PARAM_STR);
    $stmt->bindvalue(':rp_content', $report->getRpContent(), PDO::PARAM_STR);
    $stmt->bindvalue(':reportcate_id', (int)$report->getReportcateId(), PDO::PARAM_INT);
    $stmt->bindvalue(':user_id', (int)$report->getUserId(), PDO::PARAM_INT);

    $result = $stmt->execute();
    if ($result) {
      $reportId = $this->db->lastInsertId();
    } else {
      $reportId = -1;
    }
    return $reportId;
  }
  /**
   * レポート更新
   * @param Report
   * @return boolean
   */
  public function update(Report $report): bool
  {
    $sqlUpdate = " UPDATE reports SET rp_date = :rp_date , = :rp_time_from ,rp_time_to = :rp_time_to,rp_content = :rp_content , rp_crated_at = NOW() ,reportcate_id = :reportcate_id ,user_id = :user_id WHERE id = :id";
    $stmt = $this->db->prepare($sqlUpdate);
    $stmt->bindvalue(':rp_date', $report->getRpDate(), PDO::PARAM_STR);
    $stmt->bindvalue(':rp_time_from', $report->getRpTimeFrom(), PDO::PARAM_STR);
    $stmt->bindvalue(':rp_time_to', $report->getRpTimeTo(), PDO::PARAM_STR);
    $stmt->bindvalue(':rp_content', $report->getRpContent(), PDO::PARAM_STR);
    $stmt->bindvalue(':reportcate_id', $report->getReportcateId(), PDO::PARAM_INT);
    $stmt->bindvalue(':user_id', $report->getUserId(), PDO::PARAM_INT);
    $result = $stmt->execute();
    return $result;
  }
  /**
   * レポート削除
   * @param integer レポートID
   * @return boolean 登録が成功したかどうか
   */
  public function delete(int $id): bool
  {
    $sql = "DELETE FROM reports WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $result = $stmt->execute();
    return $result;
  }
}
