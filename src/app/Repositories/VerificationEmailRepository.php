<?php

namespace App\Repositories;

use App\Models\VerificationEmail;

class VerificationEmailRepository extends BaseRepository
{

    public function getModel()
    {
        return VerificationEmail::class;
    }

    public function findByToken($token) {
        return $this->model->where('token', $token)->first();
    }
}
