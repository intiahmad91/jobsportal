<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'category',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value by key
     */
    public static function set($key, $value, $type = 'string', $category = 'general', $description = null)
    {
        // Convert value based on type before saving
        $processedValue = $value;
        if ($type === 'json' && is_array($value)) {
            $processedValue = json_encode($value);
        } elseif ($type === 'boolean') {
            $processedValue = $value ? '1' : '0';
        } elseif ($type === 'integer') {
            $processedValue = (string)$value;
        }
        
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $processedValue,
                'type' => $type,
                'category' => $category,
                'description' => $description
            ]
        );
    }

    /**
     * Cast value based on type
     */
    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Get all settings by category
     */
    public static function getByCategory($category)
    {
        return static::where('category', $category)->get();
    }

    /**
     * Get all settings as key-value pairs
     */
    public static function getAllAsArray()
    {
        $settings = static::all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = static::castValue($setting->value, $setting->type);
        }
        
        return $result;
    }
}