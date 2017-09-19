<?php
/**
 * User
 *
 * PHP version 5
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Plugins\Point\Models;

use Xpressengine\User\Models\User as OriginUser;

/**
 * @category    Point
 * @package     Xpressengine\Plugins\Point
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
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
