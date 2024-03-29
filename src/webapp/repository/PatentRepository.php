<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Patent;
use tdt4237\webapp\models\PatentCollection;

class PatentRepository
{
    const SEARCH_COMPANY = "SELECT * FROM patent WHERE company=:company OR title=:title";
    const SELECT_ALL = "SELECT * FROM patent";
    const SAVE_PATENT = "INSERT INTO patent(company, title, file, description, date) VALUES(:company, :title, :file, :description, :date)";
    const FIND_PATENT = "SELECT * FROM patent WHERE patentId = :patentId";
    const DELETE_PATENT = "DELETE FROM patent WHERE patentid = :patentId";

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makePatentFromRow(array $row)
    {
        $patent = new Patent($row['patentId'], $row['company'], $row['title'], $row['file'], $row['description'], $row['date']);
        $patent->setPatentId($row['patentId']);
        $patent->setCompany($row['company']);
        $patent->setTitle($row['title']);
        $patent->setDescription($row['description']);
        $patent->setDate($row['date']);
        $patent->setFile($row['file']);

        return $patent;
    }

    public function find($patentId)
    {
        $sql  = self::FIND_PATENT;
        $sqlp = array( ':patentId' => $patentId);
        try {
          $result = $this->pdo->prepare($sql);
          $result->execute($sqlp);
          $row = $result->fetch();
            if($row === false) {
              return false;
              }
          return $this->makePatentFromRow($row);

        }
        catch (PDOException $e) {die("Error during sql query: " . $e->getMessage());
        }

    }

    public function searchPatents($company, $title)
    {
      $stmt = $this->pdo->prepare(self::SEARCH_COMPANY);
      $stmt->bindParam(':company', $company);
      $stmt->bindParam(':title', $title);
      $stmt->execute();
      $row = $stmt->fetchAll();

      if ($row === false) {
          return false;
      }

      return new PatentCollection(array_map([$this, 'makePatentFromRow'], $row));
    }

    public function all()
    {
        $stmt   = self::SELECT_ALL;
        $results = $this->pdo->query($stmt);

        if($results === false) {
            return [];
            throw new \Exception('PDO error in patent all()');
        }

        $fetch = $results->fetchAll();
        if(count($fetch) == 0) {
            return false;
        }

        return new PatentCollection(
            array_map([$this, 'makePatentFromRow'], $fetch)
        );
    }

    public function deleteByPatentid($patentId)
    {
      $sql = self::DELETE_PATENT;
      $sqlp = array( ':patentId' => $patentId);
        try {
          $result = $this->pdo->prepare($sql);
          $result->execute($sqlp);
          return 1;
        }
        catch(PDOException $e){ die("Error during sql query: " . $e->getMessage()); }

      //  return $this->pdo->exec(
        //    sprintf("DELETE FROM patent WHERE patentid='%s';", $patentId));
    }


    public function save(Patent $patent)
    {
      $stmt = $this->pdo->prepare(self::SAVE_PATENT);
      $company        = $patent->getCompany();
      $title          = $patent->getTitle();
      $file           = $patent->getFile();
      $description    = $patent->getDescription();
      $date           = $patent->getDate();
      $stmt->bindParam(':company', $company);
      $stmt->bindParam(':title', $title);
      $stmt->bindParam(':file', $file);
      $stmt->bindParam(':description' ,$description);
      $stmt->bindParam(':date',$date);
      $stmt->execute();
      return $this->pdo->lastInsertId();

    }
}
