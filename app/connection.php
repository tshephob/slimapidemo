<?php

declare(strict_types=1);

class Database {
  private $cHost = '127.0.0.1';
  private $cUser = 'root';
  private $cPassword = '';
  private $cDatabasename = 'currencyapi';

  public function connect() {
    $cConnstr = "mysql:host=$this->cHost;dbname=$this->cDatabasename";
    $conn = new PDO($cConnstr, $this->cUser, $this->cPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $conn;
  }


  public function connectpostgres() {
    $cConnstr = "pgsql:host=$this->cHost;dbname=$this->cDatabasename";
    $conn = new PDO($cConnstr, $this->cUser, $this->cPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $conn;
  }
}



?>