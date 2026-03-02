<?php

namespace app\DTO;
readonly class DatabaseInfoDTO{
    public string $dbConnection;
    public string $dbPort;
    public string $dbDatabase;
    public function __construct()
    {
        $this->dbConnection = env('DB_CONNECTION');
        $this->dbPort       = env('DB_PORT');
        $this->dbDatabase   = env('DB_DATABASE');
    }
};

?>