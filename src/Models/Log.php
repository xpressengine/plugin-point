<?php
/**
 * Log.php
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

use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point\Controllers
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */
class Log extends Model
{
    protected $table = 'point_log';

    public $timestamps = true;

    protected $casts = [
        'content' => 'array',
        'point' => 'integer'
    ];

    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }

    public function getTitleAttribute($value)
    {
        $actions = app('point::handler')->getAllActions();
        if (array_has($actions, 'point.'.$this->action)) {
            return $actions['point.'.$this->action]['title'];
        } else {
            $name = substr('point.'.$this->action, 0, strrpos('point.'.$this->action, '.'));
            if(array_has($actions, $name)) {
                return array_get($actions, $name)['title'];
            }
        }
        return $this->action;
    }

}
