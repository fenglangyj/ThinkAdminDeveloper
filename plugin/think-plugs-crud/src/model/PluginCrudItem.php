<?php

declare (strict_types=1);

namespace plugin\crud\model;

use think\admin\Model;

/**
 * 商城代理等级数据
 * @class PluginWemallConfigAgent
 * @package plugin\wemall\model
 */
class PluginCrudItem extends Model
{
    /**
     * 获取代理等级
     * @param ?string $first 增加首项内容
     * @param string $fields 指定查询字段
     * @return array
     */
    public static function items(string $first = null, string $fields = 'name,number as prefix,number'): array
    {
        $items = $first ? [-1 => ['name' => $first, 'prefix' => '-', 'number' => -1]] : [];
        $query = static::mk()->where(['status' => 1])->withoutField('id,utime,status,update_time,create_time');
        return array_merge($items, $query->order('number asc')->column($fields, 'number'));
    }
}