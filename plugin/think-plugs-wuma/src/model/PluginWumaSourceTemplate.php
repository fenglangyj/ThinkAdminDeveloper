<?php

// +----------------------------------------------------------------------
// | Wuma Plugin for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2022~2024 ThinkAdmin [ thinkadmin.top ]
// +----------------------------------------------------------------------
// | 官方网站: https://thinkadmin.top
// +----------------------------------------------------------------------
// | 免责声明 ( https://thinkadmin.top/disclaimer )
// | 收费插件 ( https://thinkadmin.top/fee-introduce.html )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/think-plugs-wuma
// | github 代码仓库：https://github.com/zoujingli/think-plugs-wuma
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace plugin\wuma\model;

/**
 * 溯源模板模块
 * @class PluginWumaSourceTemplate
 * @package plugin\wuma\model
 */
class PluginWumaSourceTemplate extends AbstractPrivate
{
    /**
     * 样式数据格式化
     * @param mixed $value
     * @return mixed
     */
    public function getStylesAttr($value)
    {
        return json_decode($value ?: '{}', true);
    }

    /**
     * 内容数据格式化
     * @param mixed $value
     * @return mixed
     */
    public function getContentAttr($value)
    {
        return json_decode($value ?: '[]', true);
    }

    /**
     * 查询指定规则的数据列表
     * @param mixed $map
     * @return array
     */
    public static function lists($map = []): array
    {
        $query = static::mk()->where(['deleted' => 0])->where($map);
        return $query->order('sort desc,id desc')->column('*', 'code');
    }
}