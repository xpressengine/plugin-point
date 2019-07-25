<?php
/**
 * SettingController.php
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
 * Class SettingController
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point\Controllers
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */
class SettingController extends Origin
{
    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * constructor.
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

        $section = [];

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
        $section['user'] = new PointSection($actions);

        $actions = [
            'board.write-document' => [
                'default' => 10
            ],
            'board.delete-document' => [
                'default' => 10
            ],
            'board.write-comment' => [
                'default' => 10
            ],
            'board.delete-comment' => [
                'default' => 10
            ],
        ];
        $section['board'] = new PointSection($actions);



        return XePresenter::make($this->plugin->view('views.index'), compact('plugin', 'section'));
    }

    public function show(Request $request, $userId)
    {
        $user = app('xe.user')->find($userId);

        if ($user === null) {
            throw new HttpException('404', '해당 회원을 찾을 수 없습니다');
        }

        $record = Point::findOrNew($userId);
        $logs = Log::where('user_id', $userId)->orderBy('created_at', 'desc')->paginate(10);

        return XePresenter::make($this->plugin->view('views.show'), compact('user', 'record', 'logs'));
    }

    public function updateSection(Request $request, Handler $handler)
    {
        $actions = $request->except('_method', '_token');

        $handler->storeActionPoint($actions);

        return XePresenter::makeApi(['type'=>'success','message'=>'저장했습니다.']);
    }
}
