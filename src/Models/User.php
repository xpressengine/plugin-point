<?php
/**
 * User.php
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

namespace Xpressengine\Plugins\Point\Models;

use Xpressengine\User\Models\User as OriginUser;

/**
 * Class User
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point\Controllers
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */
class User extends OriginUser
{
    public function point_logs()
    {
        return $this->hasMany(Log::class, 'user_id');
    }

    public function point()
    {
        return $this->hasOne(Point::class, 'user_id');
    }
}
