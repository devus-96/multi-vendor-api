<?php

namespace App\Contracts;

interface UserRepositoryInterface
{
    public function CreateStore (array $storeData, array $address);

    public function uploadImages($data, $channel, $type = 'logo');
}
