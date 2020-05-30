<?php

namespace App\Models;

use App\Constants\ConfigConstants;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'key', 'value', 'type'
    ];

    public function createOrUpdate($key, $value, $type)
    {
        if (!$value) {
            return;
        }
        $find = $this->where('key', $key)->first();
        if (!$find) {
            $rs = $this->create([
                'key' => $key,
                'value' => $value,
                'type' => $type
            ]);

            return $rs != null;
        }
        return $this->where('key', $key)->update(
            ['value' => $value]
        );
    }

    public function getSiteConfigs()
    {
        $data = config('site');
        $dbConfig = $this->where('type', ConfigConstants::TYPE_CONFIG)->get();
        if (empty($dbConfig)) {
            return $data;
        }
        foreach ($dbConfig as $config) {
            if (!isset($data[$config->key])) {
                continue;
            }
            $data[$config->key]['value'] = $config->value;
        }
        return $data;
    }

    public function getToc()
    {
        return $this->get(ConfigConstants::GUIDE_TOC);
    }

    public function get($key)
    {
        $dbConfig = $this->where('key', $key)->first();
        if ($dbConfig) {
            return $dbConfig->value;
        }
        $fileConfig = config('site.' . $key);
        if ($fileConfig) {
            return $fileConfig['value'];
        }
        return null;
    }
}
