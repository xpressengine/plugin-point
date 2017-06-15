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
class Point extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $table = 'point';
    protected $primaryKey = 'userId';
    public $timestamps = true;

    protected $casts = [
        'point' => 'integer'
    ];

    protected $fillable = [
        'userId', 'point'
    ];

    public function user()
    {
        $this->belongsTo(User::class, 'userId');
    }

    

}
