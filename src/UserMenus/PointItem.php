<?php
/**
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Plugins\Point\UserMenus;

use App\ToggleMenus\User\UserToggleMenu;

class PointItem extends UserToggleMenu
{
    public function getText()
    {
        $point = app('point::handler')->getPoint($this->identifier);

        return "포인트({$point}) 내역";
    }

    public function getType()
    {
        return static::MENUTYPE_LINK;
    }

    public function getAction()
    {
        return route('point::setting.show', ['user_id' => $this->identifier]);
    }

    public function getScript()
    {
    }

    public function allows()
    {
        return auth()->user()->isAdmin();
    }
}
