<?php

namespace App\Repositories;

use  App\Contracts\StoreRepositoryInterface;
use  App\Models\Store;

class StoreRepository implements StoreRepositoryInterface
{
    public function __construct(Store $model)
    {
        $this->model = $model;
    }

    public function CreateStore (array $storeData, array $address)
    {
        DB::beginTransaction();

        try {
            $store =  $this->model->create($storeData);

            $store->address->create($address);

            if (isset($storeData['logo'])) {
                $this->uploadImages($storeData, $store);
            }

            if (isset($storeData['baniere'])) {
                $this->uploadImages($storeData, $store, 'baniere');
            }

            BD::commit();

            return $store;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function UpdateStore ($storeData)
    {
        $store =  $this->model->update($storeData);

        if (isset($storeData['logo'])) {
            $this->uploadImages($storeData, $store);
        }

        if (isset($storeData['baniere'])) {
            $this->uploadImages($storeData, $store, 'baniere');
        }

        return $store;
    }

    public function uploadImages($data, $store, $type = 'logo')
    {
        if (request()->hasFile($type)) {
            $store->{$type} = current(request()->file($type))->store('store/'.$store->id);

            $store->save();
        } else {
            if (! isset($data[$type])) {
                if (! empty($data[$type])) {
                    Storage::delete($store->{$type});
                }

                $store->{$type} = null;

                $store->save();
            }
        }
    }
}

?>
