<?php

declare (strict_types=1);

namespace plugin\crud\command;

use plugin\crud\service\CrudConfigService;
use think\admin\Command;
use think\admin\Exception;
use think\console\Input;
use think\console\Output;

/**
 * CRUD批量生成服务
 * @class Clear
 * @package app\data\command
 */
class Clear extends Command
{
    /**
     *  当前配置参数
     * @var array
     */
    protected $config;

    /**
     * 指令参数配置
     * @return void
     */
    protected function configure()
    {
        $this->setName('xdata:crud:run');
        $this->setDescription('CRUD批量生成服务');
    }

    /**
     * 业务指令执行
     * @param Input $input
     * @param Output $output
     * @return void
     * @throws Exception
     */
    protected function execute(Input $input, Output $output)
    {
        $this->config = CrudConfigService::get();//获取配置信息
        //$this->_生成表();//生成表
        //$this->_autoRemoveOrder();//生成模型
        //$this->_autoCancelOrder();//生成控制器
        //$this->_autoConfirmOrder();//生成视图
        //$this->_autoCommentOrder();//生成api
    }

    /**
     * 取消30分钟未支付订单
     * @return void
     * @throws Exception
     */
    private function _autoCancelOrder()
    {
        if (empty($this->config['cancel_auto'])) {
            $this->queue->message(0, 0, '未启用订单自动取消功能！');
        } else try {
            $time = time() - intval(floatval($this->config['cancel_time']) * 3600);
            $remark = $this->config['cancel_text'] ?? '自动取消未完成支付';
            $where = [['status', 'in', [1, 2, 3]], ['create_time', '<', date('Y-m-d H:i:s', $time)]];
            [$count, $total] = [0, ($items = PluginWemallOrder::mk()->where($where)->select())->count()];
            $items->map(function (PluginWemallOrder $order) use ($total, &$count, $remark) {
                if ($order->payment()->findOrEmpty()->isExists()) {
                    $this->queue->message($total, ++$count, "订单 {$order->getAttr('order_no')} 存在支付记录");
                } else {
                    $this->queue->message($total, ++$count, "开始取消订单 {$order->getAttr('order_no')}");
                    $order->save(['status' => 0, 'cancel_status' => 1, 'cancel_time' => date('Y-m-d H:i:s'), 'cancel_remark' => $remark]);
                    UserOrder::stock($order->getAttr('order_no'));
                    $this->queue->message($total, $count, "完成取消订单 {$order->getAttr('order_no')}", 1);
                }
            });
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
    }

    /**
     * 清理已取消的订单
     * @return void
     * @throws Exception
     */
    private function _autoRemoveOrder()
    {
        if (empty($this->config['remove_auto'])) {
            $this->queue->message(0, 0, '未启用订单自动清理功能！');
        } else try {
            $time = time() - intval(floatval($this->config['remove_time']) * 3600);
            $remark = $this->config['remove_text'] ?? "系统自动清理已取消的订单！";
            $where = [['status', '=', 0], ['deleted_status', '=', 0], ['create_time', '<', date('Y-m-d H:i:s', $time)]];
            [$count, $total] = [0, ($items = PluginWemallOrder::mk()->where($where)->select())->count()];
            $items->map(function (PluginWemallOrder $order) use ($total, &$count, $remark) {
                if ($order->payment()->findOrEmpty()->isExists()) {
                    $this->queue->message($total, ++$count, "订单 {$order->getAttr('order_no')} 存在支付记录");
                } else {
                    $this->queue->message($total, ++$count, "开始清理订单 {$order->getAttr('order_no')}");
                    $order->save([
                        'status' => 0,
                        'deleted_time' => date('Y-m-d H:i:s'),
                        'deleted_status' => 1,
                        'deleted_remark' => $remark,
                    ]);
                    // 触发订单删除事件
                    $this->app->event->trigger('PluginWemallOrderRemove', $order);
                    $this->queue->message($total, $count, "完成清理订单 {$order->getAttr('order_no')}", 1);
                }
            });
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
    }

    /**
     * 自动完成订单评论
     * @return void
     * @throws Exception
     */
    protected function _autoCommentOrder()
    {
        if (empty($this->config['comment_auto'])) {
            $this->queue->message(0, 0, '未启用订单自动评论功能！');
        } else try {
            $time = time() - intval(floatval($this->config['comment_time']) * 3600);
            $remark = $this->config['comment_text'] ?? '系统默认好评！';
            $where = [['status', '=', 6], ['create_time', '<', date('Y-m-d H:i:s', $time)]];
            [$count, $total] = [0, ($items = PluginWemallOrder::mk()->where($where)->select())->count()];
            $items->map(function (PluginWemallOrder $order) use ($total, &$count, $remark) {
                $this->queue->message($total, ++$count, "开始评论订单 {$order->getAttr('order_no')}");
                $order->save(['status' => 7]);
                $order->items()->select()->map(function (PluginWemallOrderItem $item) use ($remark) {
                    UserAction::comment($item, '5.0', $remark, '');
                });
                $this->queue->message($total, $count, "完成评论订单 {$order->getAttr('order_no')}", 1);
            });
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
    }

    /**
     * 自动完成签收订单
     * @return void
     * @throws Exception
     */
    protected function _autoConfirmOrder()
    {
        if (empty($this->config['receipt_auto'])) {
            $this->queue->message(0, 0, '未启用订单自动签收功能！');
        } else try {
            $time = time() - intval(floatval($this->config['receipt_time']) * 3600);
            $where = [['status', '=', 5], ['create_time', '<', date('Y-m-d H:i:s', $time)]];
            $remark = $this->config['receipt_text'] ?? '系统自动签收订单！';
            [$count, $total] = [0, ($items = PluginWemallOrder::mk()->where($where)->select())->count()];
            $items->map(function (PluginWemallOrder $order) use ($total, &$count, $remark) {
                $this->queue->message($total, ++$count, "开始签收订单 {$order->getAttr('order_no')}");
                $order->save(['status' => 6, 'confirm_time' => date('Y-m-d H:i:s'), 'confirm_remark' => $remark]);
                $this->app->event->trigger('PluginWemallOrderConfirm', $order);
                $this->queue->message($total, $count, "完成签收订单 {$order->getAttr('order_no')}", 1);
            });
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
    }
}