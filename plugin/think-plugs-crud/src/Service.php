<?php

declare (strict_types=1);

namespace plugin\crud;

use plugin\payment\Service as PaymentService;
use plugin\crud\command\Clear;
use plugin\crud\command\Trans;
use plugin\crud\command\Users;
use think\admin\Plugin;


/**
 * 插件服务注册
 * @class Service
 * @package plugin\crud
 */
class Service extends Plugin
{
    /**
     * 定义插件名称
     * @var string
     */
    protected $appName = 'CRUD';

    /**
     * 定义安装包名
     * @var string
     */
    protected $package = 'fenglangyj/think-plugs-crud';

    /**
     * 插件服务注册
     * @return void
     */
    public function register(): void
    {
        $this->commands([Clear::class, Trans::class, Users::class]);
    }

    /**
     * 定义插件菜单
     * @return array[]
     */
    public static function menu(): array
    {
        $code = self::getAppCode();
        return [
            [
                'name' => 'CRUD配置',
                'subs' => [
                    ['name' => '密钥 管理', 'icon' => 'layui-icon layui-icon-set', 'node' => "{$code}/base.config/index"],
                    ['name' => 'CRUD管理', 'icon' => 'layui-icon layui-icon-theme', 'node' => "{$code}/base.report/index"],
                ],
            ],
            /*[
                'name' => '用户管理',
                'subs' => [
                    ['name' => '会员等级管理', 'icon' => 'layui-icon layui-icon-water', 'node' => "{$code}/base.level/index"],
                    ['name' => '会员折扣方案', 'icon' => 'layui-icon layui-icon-engine', 'node' => "{$code}/base.discount/index"],
                    ['name' => '会员用户管理', 'icon' => 'layui-icon layui-icon-user', 'node' => "{$code}/user.admin/index"],
                    // ['name' => '用户卡券管理', 'icon' => 'layui-icon layui-icon-tabs', 'node' => "{$code}/user.coupon/index"],
                    ['name' => '创建会员用户', 'icon' => 'layui-icon layui-icon-tabs', 'node' => "{$code}/user.create/index"],
                    ['name' => '用户余额充值', 'icon' => 'layui-icon layui-icon-rmb', 'node' => "{$code}/user.recharge/index"],
                ],
            ],
            [
                'name' => '商城管理',
                'subs' => [
                    ['name' => '商品数据管理', 'icon' => 'layui-icon layui-icon-star', 'node' => "{$code}/shop.goods/index"],
                    ['name' => '订单数据管理', 'icon' => 'layui-icon layui-icon-template', 'node' => "{$code}/shop.order/index"],
                    ['name' => '订单发货管理', 'icon' => 'layui-icon layui-icon-transfer', 'node' => "{$code}/shop.sender/index"],
                    ['name' => '售后订单管理', 'icon' => 'layui-icon layui-icon-util', 'node' => "{$code}/shop.refund/index"],
                    ['name' => '商品评论管理', 'icon' => 'layui-icon layui-icon-util', 'node' => "{$code}/shop.reply/index"],
                ],
            ],
            [
                'name' => '代理管理',
                'subs' => [
                    ['name' => '代理等级管理', 'icon' => 'layui-icon layui-icon-water', 'node' => "{$code}/base.agent/index"],
                    ['name' => '代理返佣管理', 'icon' => 'layui-icon layui-icon-transfer', 'node' => "{$code}/user.rebate/index"],
                    ['name' => '代理提现管理', 'icon' => 'layui-icon layui-icon-diamond', 'node' => "{$code}/user.transfer/index"],
                    // ['name' => '活动签到管理', 'icon' => 'layui-icon layui-icon-engine', 'node' => "{$code}/user.checkin/index"],
                ]
            ],
            [
                'name' => '帮助咨询',
                'subs' => [
                    ['name' => '常见问题管理', 'icon' => 'layui-icon layui-icon-star', 'node' => "{$code}/help.problem/index"],
                    ['name' => '意见反馈管理', 'icon' => 'layui-icon layui-icon-template', 'node' => "{$code}/help.feedback/index"],
                    // ['name' => '工单提问管理', 'icon' => 'layui-icon layui-icon-service', 'node' => "{$code}/help.question/index"],
                ],
            ],*/
        ];
    }
}