<?php

namespace App\Repositories;

use App\Models\Configuration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfigurationRepository extends BaseRepository {
    public function getModel() {
        return Configuration::class;
    }

    public function updateConfigurations($data) {
        try {
            DB::beginTransaction();
            foreach($data as $key => $value) {
                $this->model->where('key', $key)->update(['value' => $value]);
            }
            DB::commit();

            return $this->model->all();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
