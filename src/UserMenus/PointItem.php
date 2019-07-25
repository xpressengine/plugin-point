<?php
/**
 * PointItem.php
 *
 * PHP version 7
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Plugins\Point\UserMenus;

use App\ToggleMenus\User\UserToggleMenu;

/**
 * Class PointItem
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point\Controllers
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */
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
