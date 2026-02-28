<?php

namespace app\DTO;


class ServerInfoDTO{
    public function __construct(
        public string $phpVersion
    )
    {}
    
};

?>