<?php
//Class for database connection
class Dbc
{
    /* Values are set in configfile*/
    private $host = DBHOST;
    private $user = DBUSER;
    private $password = DBPASS;
    private $dbName = DBDATABASE;
    private $charset = 'utf8mb4';

    protected function connect()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName . ';charset=' . $this->charset;
        $options = [
            PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];

        try {
            /* Try the database connectoin on errer the catch sends out a errormessage*/
            $pdo = new PDO($dsn, $this->user, $this->password, $options);
            //$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (Exception $e) {
            error_log($e->getMessage());
            exit('Fel vid databasanslutning');
        }
    }
}