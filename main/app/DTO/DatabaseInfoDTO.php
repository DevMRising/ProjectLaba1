<?php

namespace app\DTO;
class DatabaseInfoDTO{
    public function databaseInfo(){
        return dd(config('database.default'));
    }
};

?>