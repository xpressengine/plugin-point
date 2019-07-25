<?php
/**
 * Point.php
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
 * Class Point
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point\Controllers
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */
class Point extends Model
{
    protected $table = 'point';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    protected $casts = [
        'point' => 'integer'
    ];

    protected $fillable = [
        'user_id', 'point'
    ];

    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }

    

}
