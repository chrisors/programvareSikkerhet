<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Patent;
use tdt4237\webapp\models\PatentCollection;

class PatentRepository
{
    const SEARCH_COMPANY = "SELECT * FROM patent WHERE company='%s' OR title='%s'";
    const SELECT_ALL = "SELECT * FROM patent";

const INSERT_QUERY   = "INSERT INTO patent (company, title, description, date, file) VALUES('%s', '%s', '%s' , '%s' , '%s')";
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
        $patent = new Patent($row['patentId'], $row['company'], $row['title'], $row['description'], $row['date'], $row['file']);
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
        $sql  = "SELECT * FROM patent WHERE patentId = :patentId ";
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
      $query = sprintf(self::SEARCH_COMPANY, $company, $title);
      $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
      $row = $result->fetchAll();

      if ($row === false) {
          return false;
      }

      return new PatentCollection(array_map([$this, 'makePatentFromRow'], $row));
    }

    public function all()
    {
        $sql   = self::SELECT_ALL;
        $results = $this->pdo->query($sql);

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
      $sql = "DELETE FROM patent WHERE patentid= :patentId ";
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
      $company        = $patent->getCompany();
       $title          = $patent->getTitle();
        $description    = $patent->getDescription();
        $date           = $patent->getDate();
        $file           = $patent->getFile();


          //$sql = "INSERT INTO patent (company, title, description, date, file) VALUES (:company, :title, :description, :date, :file)";
          //$sqlp = array( ':company' => $patent->getCompany(), ':title' => $patent->getTitle(), ':description' => $patent->getDescription(), ':date' => $patent->getDate(), ':file' => $patent->getFile());

          if ($patent->getPatentId() === null) {
            $query = "INSERT INTO patent (company, date, title, description, file) "
          . "VALUES ('$company', '$date', '$title', '$description', '$file')";

            //  $query = sprintf(
              //            self::INSERT_QUERY, $patent->getCompany(), $patent->getTitle(), $patent->getDescription(), $patent->getDate(), $patent->getFile()
                //      );

                  //    return $this->pdo->exec($query);

          }
          $this->pdo->exec($query);
              return $this->pdo->lastInsertId();


    }
}
