<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\menu;

use xutl\wechat\Api;

/**
 * 自定义菜单
 * @package xutl\wechat\menu
 */
class Menu extends Api
{
    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delete';
    const API_QUERY = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info';
    const API_CONDITIONAL_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional';
    const API_CONDITIONAL_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional';
    const API_CONDITIONAL_TEST = 'https://api.weixin.qq.com/cgi-bin/menu/trymatch';

    /**
     * Get all menus.
     * @return array
     */
    public function all()
    {
        return $this->get(self::API_GET);
    }

    /**
     * Get current menus.
     * @return array
     */
    public function current()
    {
        return $this->get(self::API_QUERY);
    }

    /**
     * 添加菜单
     * @param array $buttons
     * @param array $matchRule
     * @return array
     */
    public function add(array $buttons, array $matchRule = [])
    {
        if (!empty($matchRule)) {
            return $this->json(self::API_CONDITIONAL_CREATE,  [
                'button' => $buttons,
                'matchrule' => $matchRule,
            ]);
        }
        return $this->json(self::API_CREATE,['button' => $buttons]);
    }

    /**
     * Destroy menu.
     * @param int $menuId
     * @return array
     */
    public function destroy($menuId = null)
    {
        if ($menuId !== null) {
            return $this->json(self::API_CONDITIONAL_DELETE,['menuid' => $menuId]);
        }
        return $this->get(self::API_DELETE);
    }

    /**
     * Test conditional menu.
     * @param string $userId
     * @return array
     */
    public function test($userId)
    {
        return $this->json(self::API_CONDITIONAL_TEST,['user_id' => $userId]);
    }
}