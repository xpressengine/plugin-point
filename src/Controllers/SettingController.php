<?php
/**
 *  This file is part of the Xpressengine package.
 *
 * PHP version 5
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Plugins\Point\Controllers;

use App\Http\Controllers\Controller as Origin;
use Symfony\Component\HttpKernel\Exception\HttpException;
use XePresenter;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Point\Handler;
use Xpressengine\Plugins\Point\Models\Log;
use Xpressengine\Plugins\Point\Models\Point;
use Xpressengine\Plugins\Point\Plugin;
use Xpressengine\Plugins\Point\Sections\PointSection;

/**
 * @category    Point
 * @package     Xpressengine\Plugins\Point\Controllers
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class SettingController extends Origin
{
    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * SocialLoginController constructor.
     *
     * @param \Xpressengine\Plugins\Point\Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function index(Request $request)
    {
        $plugin = $this->plugin;

        app('xe.frontend')->js('assets/vendor/bootstrap/js/bootstrap.min.js')->appendTo('head')->load();
        app('xe.frontend')->js(
            [
                'assets/core/xe-ui-component/js/xe-page.js',
                'assets/core/xe-ui-component/js/xe-form.js'
            ]
        )->load();

        $actions = [
            'user_login' => [
                'title' => '로그인',
                'default' => 10,
            ],
            'user_register' => [
                'title' => '가입',
                'default' => 50,
            ],
        ];

        $section = new PointSection($actions);

        return XePresenter::make($this->plugin->view('views.index'), compact('plugin', 'section'));
    }

    public function show(Request $request, $userId)
    {
        $user = app('xe.user')->find($userId);

        if ($user === null) {
            throw new HttpException('404', '해당 회원을 찾을 수 없습니다');
        }

        $config = app('xe.config');

        $actions = [];
        $root = $config->get('point');
        foreach (app('xe.config')->children($root) as $action) {
            $actions[$action->name] = $action;
        }

        $record = Point::find($userId);
        $logs = Log::where('userId', $userId)->orderBy('createdAt', 'desc')->paginate(10);

        return XePresenter::make($this->plugin->view('views.show'), compact('user', 'record', 'logs', 'actions'));
    }

    public function updateSection(Request $request, Handler $handler)
    {
        $actions = $request->except('_method', '_token');

        $handler->storeActionPoint($actions);

        return XePresenter::makeApi(['type'=>'success','message'=>'저장했습니다.']);
    }
}
