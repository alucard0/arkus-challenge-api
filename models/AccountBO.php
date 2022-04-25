<?php
include "AccountDO.php";

class AccountBO
{
  private $conn;
  private $table = 'account';

  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function getAllAccounts()
  {
    $query = 'SELECT m.id, m.name, m.client_name, m.manager_name, m.email, m.team_name FROM (
        SELECT a.id, u.name as manager_name, ac.email, ac.account_id, a.name, a.client_name, t.name as team_name 
        FROM accountmanager as ac, account as a, user as u, team as t 
        WHERE ac.account_id = a.id AND ac.email = u.email AND t.account_id = a.id
      ) as m';
    $statement = $this->conn->prepare($query);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_ASSOC);

    return $statement;
  }

  public function getAccount($account_id)
  {
    $query = 'SELECT m.id, m.name, m.client_name, m.manager_name, m.email, m.team_name FROM (
        SELECT a.id, u.name as manager_name, ac.email, ac.account_id, a.name, a.client_name, t.name as team_name 
        FROM accountmanager as ac, account as a, user as u, team as t 
        WHERE ac.account_id = :current_account_id AND ac.email = u.email AND t.account_id = :current_account_id
      ) as m';
    $statement = $this->conn->prepare($query);
    $statement->bindParam(':current_account_id', $account_id);
    $statement->execute();
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    $account = AccountDO::constructNewFullAccount($row);

    return  $account;
  }

  public function createAccount($account)
  {
    $query = 'INSERT INTO '. $this->table . '
    (name, client_name)
    VALUES
    (:name, :client_name)';

    $statement = $this->conn->prepare($query);
    
    $name =  htmlspecialchars(strip_tags($account->account_name));
    $client_name = htmlspecialchars(strip_tags($account->client_name));

    $statement->bindParam(':name', $name);
    $statement->bindParam(':client_name', $client_name);
    
    return $statement->execute();
  }

  public function updateAccount($account){
    $query = 'UPDATE ' . $this->table . 
    ' SET name = :name,
      client_name = :client_name
      WHERE id = :id';

      $statement = $this->conn->prepare($query);

      $name =  htmlspecialchars(strip_tags($account->account_name));
      $client_name = htmlspecialchars(strip_tags($account->client_name));
      $id = htmlspecialchars(strip_tags($account->id));
  
      $statement->bindParam(':name', $name);
      $statement->bindParam(':client_name', $client_name);
      $statement->bindParam(':id', $id);

      if($statement->execute()) {
        return true;
      }

      printf("Error: %s.\n", $statement->error);

      return false;
  }
}