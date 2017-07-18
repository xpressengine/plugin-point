<?php
/**
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */
namespace Xpressengine\Plugins\Point\Sections;

use App\Http\Sections\Section;
use View;
use Xpressengine\Plugins\Point\Plugin;

class PointSection extends Section
{
    /**
     * @var array
     */
    private $actions;

    public static $sequence = 0;

    public function __construct($actions)
    {
        $this->actions = $actions;
    }

    /**
     * get sequence number
     *
     * @return int
     */
    public static function seq()
    {
        return ++self::$sequence;
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        app('xe.frontend')->js('assets/vendor/bootstrap/js/bootstrap.min.js')->appendTo('head')->load();
        app('xe.frontend')->js(
            [
                'assets/core/xe-ui-component/js/xe-page.js',
                'assets/core/xe-ui-component/js/xe-form.js'
            ]
        )->load();

        app('xe.frontend')->html('point.section.form')->content("<script>
            window.showMessage = function(data) {
                XE.toast(data.type, data.message);
            }
        </script>")->load();

        $handler = app('point::handler');

        $actions = $this->actions;

        foreach ($actions as $name => &$action) {
            $action['name'] = $name;
            $action['title'] = $handler->getActionTitle($name, array_get($action, 'title'));
            $action['point'] = $handler->getActionPoint($name, array_get($action, 'default'));
        }

        $seq = $this->seq();

        return view(
            Plugin::view('views.section'),
            compact('actions', 'seq')
        );
    }
}
