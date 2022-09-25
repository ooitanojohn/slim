<?php

/**
 * ReportcateのDAO
 * @auther Syuto Niimi
 * name=ReportcateDAO.php
 * dir=
 */

namespace PH35slim\ShareReports\Classes\daos;

use PDO;
use PH35slim\ShareReports\Classes\entities\Reportcate;

/**
 * レポート詳細オブジェクト
 */
class ReportcateDAO
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
   * userIdによる検索。
   *
   * @param int $userId userID 。
   * @return Reportcate 該当するReportcateオブジェクト。ただし、該当データがない場合はnull。
   */
  public function findByReportcateId(int $reportcateId): ?Reportcate
  {
    $sql = "SELECT * FROM reportcates WHERE id = :reportcateId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":reportcateId", $reportcateId, PDO::PARAM_STR);
    $result = $stmt->execute();
    $Reportcate = null;
    if ($result && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $Reportcate = new Reportcate();
      $Reportcate->setId($row["id"]);
      $Reportcate->setRcName($row["rc_name"]);
      $Reportcate->setRcNote($row["rc_note"]);
      $Reportcate->setRcListFlg($row["rc_list_flg"]);
      $Reportcate->setRcOrder($row["rc_order"]);
    }
    return $Reportcate;
  }
  /**
   * 作業種類リスト全取得
   *
   * @return array array 該当なしの場合null
   */
  public function findAll(): array
  {
    $sql = "SELECT * FROM reportcates";
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute();
    $ReportcateList = [];
    while ($row = $stmt->fetch()) {
      $Reportcate = new Reportcate();
      $Reportcate->setId($row["id"]);
      $Reportcate->setRcName($row["rc_name"]);
      $Reportcate->setRcNote($row["rc_note"]);
      $Reportcate->setRcListFlg($row["rc_list_flg"]);
      $Reportcate->setRcOrder($row["rc_order"]);
      $ReportcateList[$row['id']] = $Reportcate;
    }
    return $ReportcateList;
  }
  /**
   * 作業種類新規作成画面
   * @param Reportcate
   * @return integer 登録失敗 = -1
   */
  public function insert(Reportcate $Reportcate): int
  {
    $sqlInsert = "INSERT INTO Reportcates (rc_name,rc_note,rc_list_flg,
rc_order) VALUES(:rc_name,:rc_note,:rc_list_flg,:rc_order)";
    $stmt = $this->db->prepare($sqlInsert);
    $stmt->bindvalue('rc_name', $Reportcate->getRcName(), PDO::PARAM_STR);
    $stmt->bindvalue('rc_note', $Reportcate->getRcNote(), PDO::PARAM_STR);
    $stmt->bindvalue('rc_list_flg', $Reportcate->getRcListFlg(), PDO::PARAM_INT);
    $stmt->bindvalue('rc_order', $Reportcate->getRcOrder(), PDO::PARAM_INT);
    $result = $stmt->execute();
    if ($result) {
      $ReportcateId = $this->db->lastInsertId();
    } else {
      $ReportcateId = -1;
    }
    return $ReportcateId;
  }
  /**
   * 作業種類更新
   * @param Reportcate
   * @return boolean
   */
  public function update(Reportcate $Reportcate): bool
  {
    $sqlUpdate = " UPDATE Reportcates SET rc_name = :rc_name, rc_note = :rc_note,rc_list_flg = :rc_list_flg, rc_order = :rc_order WHERE id = :id";
    $stmt = $this->db->prepare($sqlUpdate);
    $stmt->bindvalue('rc_name', $Reportcate->getRcName(), PDO::PARAM_STR);
    $stmt->bindvalue('rc_note', $Reportcate->getRcNote(), PDO::PARAM_STR);
    $stmt->bindvalue('rc_list_flg', $Reportcate->getRcListFlg(), PDO::PARAM_INT);
    $stmt->bindvalue('rc_order', $Reportcate->getRcOrder(), PDO::PARAM_INT);
    $result = $stmt->execute();
    return $result;
  }
  /**
   * 作業種類削除
   * @param integer 作業種類ID
   * @return boolean 登録が成功したかどうか
   */
  public function delete(int $id): bool
  {
    $sql = "DELETE FROM Reportcates WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $result = $stmt->execute();
    return $result;
  }
}
