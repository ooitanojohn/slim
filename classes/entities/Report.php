<?php

/**
 * レポートエンティティクラス
 * @author syuto niimi
 *
 * name=Report.php
 * dir=/ph35/sharereorts
 */

namespace PH35slim\ShareReports\Classes\entities;

class Report
{
  // レポートID
  private ?int $id = null;
  // 作業日
  private ?string $rpDate = '';
  // 作業開始時間
  private ?string $rpTimeFrom = '';
  // 作業終了時間
  private ?string $rpTimeTo = '';
  // 作業内容
  private ?string $rpContent = '';
  // 登録日時
  private ?string $rpCreatedAt = '';
  // 作業種類ID
  private ?int $reportcateId = null;
  // 報告者ID
  private ?int $userId = null;

  // アクセサメソッド
  public function getId(): ?int
  {
    return $this->id;
  }
  public function setId(int $id): void
  {
    $this->id = $id;
  }
  public function getRpDate(): ?string
  {
    return $this->rpDate;
  }
  public function setRpDate(string $rpDate): void
  {
    $this->rpDate = $rpDate;
  }
  public function getRpTimeFrom(): ?string
  {
    return $this->rpTimeFrom;
  }
  public function setRpTimeFrom(string $rpTimeFrom): void
  {
    $this->rpTimeFrom = $rpTimeFrom;
  }
  public function getRpTimeTo(): ?string
  {
    return $this->rpTimeTo;
  }
  public function setRpTimeTo(string $rpTimeTo): void
  {
    $this->rpTimeTo = $rpTimeTo;
  }
  public function getRpContent(): ?string
  {
    return $this->rpContent;
  }
  public function setRpContent(string $rpContent): void
  {
    $this->rpContent = $rpContent;
  }
  public function getRpCreatedAt(): ?string
  {
    return $this->rpCreatedAt;
  }
  public function setRpCreatedAt(string $rpCreatedAt): void
  {
    $this->rpCreatedAt = $rpCreatedAt;
  }
  public function getReportcateId(): ?int
  {
    return $this->reportcateId;
  }
  public function setReportcateId(int $reportcateId): void
  {
    $this->reportcateId = $reportcateId;
  }
  public function getUserId(): ?int
  {
    return $this->userId;
  }
  public function setUserId(int $userId): void
  {
    $this->userId = $userId;
  }

  /**
   * 雇用日を分割した配列を返す
   * @return array 未入力 = nullの場合 空配列を返す
   */
  public function explodeRpDate(): array
  {
    if ($this->rpDate === '') {
      return ['', '', ''];
    } else {
      return explode('-', $this->rpDate);
    }
  }
  /**
   * 年を返す関数
   * @return string 4桁の文字列
   */
  public function getRpDateYear(): ?int
  {
    $date = self::explodeRpDate();
    return (int)$date[0];
  }
  /**
   * 月を返す関数
   * @return string 2桁の文字列
   */
  public function getRpDateMonth(): ?int
  {
    $date = self::explodeRpDate();
    return (int)$date[1];
  }
  /**
   * 年月を返す関数
   * @return string 4桁の文字列
   */
  public function getRpDateDay(): ?int
  {
    $date = self::explodeRpDate();
    return (int)$date[2];
  }


  /**
   * 作業開始時間を分割した配列を返す
   * @return array 未入力 = nullの場合 空配列を返す
   */
  public function explodeRpTimeFrom(): array
  {
    if ($this->rpTimeFrom === '') {
      return ['', '', ''];
    } else {
      return explode('-', $this->rpTimeFrom);
    }
  }
  /**
   * 時を返す関数
   * @return string 4桁の文字列
   */
  public function getRpTimeFromHour(): ?int
  {
    $time = self::explodeRpTimeFrom();
    return (int)$time[0];
  }
  /**
   * 分を返す関数
   * @return string 2桁の文字列
   */
  public function getRpTimeFromTime(): ?int
  {
    $time = self::explodeRpTimeFrom();
    return (int)$time[1];
  }

  /**
   * 作業開始時間を分割した配列を返す
   * @return array 未入力 = nullの場合 空配列を返す
   */
  public function explodeRpTimeTo(): array
  {
    if ($this->rpTimeTo === '') {
      return ['', '', ''];
    } else {
      return explode('-', $this->rpTimeTo);
    }
  }
  /**
   * 時を返す関数
   * @return string 4桁の文字列
   */
  public function getRpTimeToHour(): ?int
  {
    $time = self::explodeRpTimeTo();
    return (int)$time[0];
  }
  /**
   * 分を返す関数
   * @return string 2桁の文字列
   */
  public function getRpTimeToTime(): ?int
  {
    $time = self::explodeRpTimeTo();
    return (int)$time[1];
  }
}
