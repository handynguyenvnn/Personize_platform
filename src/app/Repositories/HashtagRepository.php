<?php

namespace App\Repositories;

use App\Models\Hashtag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HashtagRepository extends BaseRepository
{
    protected $perPage = 5;

    public function getModel()
    {
        return Hashtag::class;
    }

    public function list($request)
    {
        $query = $this->model;
        if ($request->create_stream) {
            return $query->whereNull('is_admin')->get();
        } else {
            return $query->all();
        }
    }

    public function createHashtags($hashtagStr)
    {
        try {
            $hashtagArray = explode(',', $hashtagStr);

            $func = function ($value) {
                $value = trim($value);
                return strtolower(substr($value, 1, strlen($value) - 1));
            };

            $hashtagArrayMapping = array_map($func, $hashtagArray);

            $arrayCheck = $this->model->whereIn('hashtag', $hashtagArrayMapping)->get();

            $arrayCheckMapping = collect($arrayCheck)->map(function ($item) {
                $item->hashtag = strtolower($item->hashtag);
                return $item;
            })->all();

            $hashFiltered = collect($hashtagArrayMapping)->filter(function ($item, $key) use ($arrayCheckMapping) {
                return !collect($arrayCheckMapping)->contains('hashtag', $item);
            });

            if ($hashFiltered->count() > 0) {
                DB::beginTransaction();
                foreach ($hashFiltered as $item) {
                    $arg = [
                        "hashtag" => $item,
                        "created_at" => now()
                    ];
                    $this->create($arg);
                   
                }

                DB::commit();
            }
            $arrayIds = collect([]);

            foreach($hashtagArrayMapping as $item) {
                $findedItem = $this->model->where('hashtag', $item)->first();

                if($findedItem) {
                    $arrayIds->push($findedItem->id);
                }
            }
            return [
                'status' => true,
                'hashtagIds' => $arrayIds->sort(),
            ];

            
        } catch (\Exception $exception) {
            dd($exception->getMessage());
            return [
                'status' => false,
                'hashtagIds' => null,
            ];
        }
    }

    public function searchHashtag($request) {
        return Hashtag::where('hashtag', 'like', '%' . $request->s . '%')
        ->orderBy('id', 'asc')
        ->paginate(isset($request->limit) ? $request->limit : $this->perPage);
    }
}
