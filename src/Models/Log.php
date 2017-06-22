<?php
/**
 *  This file is part of the Xpressengine package.
 *
 * PHP version 5
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point
 * @author      XE Team (khongchi) <khongchi@xpressengine.com>
 * @copyright   2000-2014 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Plugins\Point\Models;

use Illuminate\Database\Eloquent\Model;

/**
     * @category    Point
     * @package     Xpressengine\Plugins\Point
     * @author      XE Team (khongchi) <khongchi@xpressengine.com>
     * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
     * @link        http://www.xpressengine.com
     */
class Log extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $table = 'point_log';

    public $timestamps = true;

    protected $casts = [
        'content' => 'array',
        'point' => 'integer'
    ];

    public function user()
    {
        $this->belongsTo(User::class, 'userId');
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
