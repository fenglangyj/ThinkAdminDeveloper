<?php

declare (strict_types=1);

namespace plugin\crud\controller\base;

use plugin\account\service\Account;
use plugin\crud\service\CrudConfigService;
use think\admin\Controller;
use think\admin\Exception;

/**
 * 应用参数配置
 * @class Config
 * @package plugin\wemall\controller\base
 */
class Config extends Controller
{

    /**
     * 商城参数配置
     * @auth true
     * @menu true
     * @return void
     * @throws Exception
     */
    public function index()
    {
        $this->title = '商城参数配置';
        $this->data = CrudConfigService::get();
        $this->pages = CrudConfigService::$pageTypes;
        $this->fetch();
    }

    /**
     * 修改参数配置
     * @auth true
     * @return void
     * @throws Exception
     */
    public function params()
    {
        $this->vo = CrudConfigService::get();
        if ($this->request->isGet()) {
            $this->enableAndroid = !!Account::field(Account::ANDROID);
            $this->fetch();
        } else {
            CrudConfigService::set(array_merge($this->vo, $this->request->post()));
            $this->success('配置更新成功！');
        }
    }

    /**
     * 修改订单配置
     * @auth true
     * @return void
     * @throws Exception
     */
    public function order()
    {
        $this->params();
    }

    /**
     * 修改协议内容
     * @auth true
     * @return void
     * @throws Exception
     */
    public function content()
    {
        $input = $this->_vali(['code.require' => '编号不能为空！']);
        $title = CrudConfigService::$pageTypes[$input['code']] ?? null;
        if (empty($title)) $this->error('无效的内容编号！');
        if ($this->request->isGet()) {
            $this->title = "编辑{$title}";
            $this->data = CrudConfigService::getPage($input['code']);
            $this->fetch('index_content');
        } elseif ($this->request->isPost()) {
            CrudConfigService::setPage($input['code'], $this->request->post());
            $this->success('内容保存成功！', 'javascript:history.back()');
        }
    }
}