<?php

namespace app\DTO;


readonly class ServerInfoDTO{
    public string $phpVersion;
    public string $timeZone;
    public function __construct()
    {
        $this->phpVersion = phpversion();
        $this->timeZone = $_ENV['APP_TIMEZONE'];
    }
    
};

?>