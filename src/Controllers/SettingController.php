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
use Carbon\Carbon;
use Xpressengine\User\UserHandler;
use Xpressengine\Config\ConfigManager;

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

    public function index(Request $request, Handler $handler, UserHandler $userHandler)
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

        $actions = $handler->getUserActions();
        $section['user'] = new PointSection($actions);

        $actions = $handler->getBoardActions();
        $section['board'] = new PointSection($actions);

        $config = $handler->getConfig();
        $baseConfig = $config->all();

        $levelIconOptions = $handler->getIconOptions();

        $groupConfig = $handler->getGroupConfig();
        $groupList = $userHandler->groups()->all();
        $defaultGroupId = app('xe.config')->get('user.register')->get('joinGroup');
        $groupByLevel = [];
        foreach ($groupList as $group) {
            if (isset($groupConfig[$group->id]) == false) {
                $groupConfig[$group->id] = '';
            }

            if ($groupConfig[$group->id] != '') {
                $groupByLevel[$groupConfig[$group->id]] = $group->name;
            }
        }

        $iconPath = $handler->getIconPath();
        $levelPointConfig = $handler->getLevelPointConfig();
        $levels = $handler->getLevelList();
        foreach ($levels as $level) {
            if (isset($levelPointConfig[$level]) == false) {
                $levelPointConfig[$level] = '';
            }
        }

        return XePresenter::make($this->plugin->view('views.index'), compact(
            'plugin',
            'section',
            'baseConfig',
            'levelIconOptions',
            'groupConfig',
            'groupList',
            'defaultGroupId',
            'groupByLevel',
            'iconPath',
            'levelPointConfig',
            'levels'
        ));
    }

    public function instance(Request $request, Handler $handler, ConfigManager $configManager)
    {

        $instances = $handler->getInstanceList();
        $sectionGroup = [];
        $actions = $handler->getBoardActions();
        $sections = [];
        foreach ($instances['board'] as $menuItem) {
            $newActions = [];
            foreach ($actions as $key => $value) {
                $key = sprintf('%s.%s', $key, $menuItem->id);
                $newActions[$key] = ['default' => ''];
            }

            $section = [
                'instanceId' => $menuItem->id,
                'instanceName' => $menuItem->title,
                'section' => new PointSection($newActions),
            ];
            $sections[] = $section;
        }

        $sectionGroup['board'] = $sections;

        return XePresenter::make($this->plugin->view('views.instance'), compact(
            'sectionGroup',
            'section'
        ));
    }

    public function instanceAll(Request $request, Handler $handler, ConfigManager $configManager)
    {
        $instances = $handler->getInstanceList();

        return XePresenter::make($this->plugin->view('views.instance'), compact('instances'));
    }


    public function user(Request $request, Handler $handler, UserHandler $userHandler)
    {
        $query = Point::query();

        $current = Carbon::now();
        //기간 검색
        if ($endDate = $request->get('end_date', $current->format('Y-m-d'))) {
            $query = $query->where('created_at', '<=', $endDate . ' 23:59:59');
        }
        if ($startDate = $request->get('start_date', $current->subDay(7)->format('Y-m-d'))) {
            $query = $query->where('created_at', '>=', $startDate . ' 00:00:00');
        }

        if ($userEmail = $request->get('user_email')) {
            $writers = \XeUser::where(
                'email',
                'like',
                '%' . $userEmail . '%'
            )->selectRaw('id')->get();

            $writerIds = [];
            foreach ($writers as $writer) {
                $writerIds[] = $writer['id'];
            }
            $query = $query->whereIn('user_id', $writerIds);
        }

        $userPoints = $query->orderBy('point', 'desc')->paginate(20);

        return XePresenter::make(
            $this->plugin->view('views.user'),
            compact('userPoints', 'startDate', 'endDate')
        );
    }

    public function logs(Request $request)
    {
        $query = Log::query();

        $current = Carbon::now();
        //기간 검색
        if ($endDate = $request->get('end_date', $current->format('Y-m-d'))) {
            $query = $query->where('created_at', '<=', $endDate . ' 23:59:59');
        }
        if ($startDate = $request->get('start_date', $current->subDay(7)->format('Y-m-d'))) {
            $query = $query->where('created_at', '>=', $startDate . ' 00:00:00');
        }

        if ($userEmail = $request->get('user_email')) {
            $writers = \XeUser::where(
                'email',
                'like',
                '%' . $userEmail . '%'
            )->selectRaw('id')->get();

            $writerIds = [];
            foreach ($writers as $writer) {
                $writerIds[] = $writer['id'];
            }
            $query = $query->whereIn('user_id', $writerIds);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return XePresenter::make(
            $this->plugin->view('views.logs'),
            compact('logs', 'startDate', 'endDate')
        );
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

    public function updateConfig(Request $request, Handler $handler)
    {
        $options = $request->except('_method', '_token');

        $handler->storeConfig($options);

        return XePresenter::makeApi(['type'=>'success','message'=>'저장했습니다.']);
    }

    public function updateGroup(Request $request, Handler $handler)
    {
        $options = $request->except('_method', '_token');

        $handler->storeGroup($options);

        return XePresenter::makeApi(['type'=>'success','message'=>'저장했습니다.']);
    }

    public function updateLevelPoint(Request $request, Handler $handler)
    {
        $options = $request->except('_method', '_token');

        $handler->storeLevelPoint($options);

        return XePresenter::makeApi(['type'=>'success','message'=>'저장했습니다.']);
    }

    public function updateUserPoint(Request $request, Handler $handler, UserHandler $userHandler)
    {
        $params = $request->except('_method', '_token');

        //$user, $point, $content = [], $action = nul
        $user = $userHandler->find($params['user_id']);
        if ($user == null) {
            return XePresenter::makeApi(['type'=>'warning','message'=>'회원을 찾을 수 없습니다.']);
        }

        $point = $params['point'];
        $check = substr((string)$point, 0, 1);

        $action = 'admin-force';
        $content = ['point' => $point];
        if ($check != '+' && $check != '-') {
            $action = 'admin-force-set';
            $content = ['set_point' => $point];

            $userPoint = $handler->getPointObj($user->getId());
            $current = $userPoint->point;
            if ($current == $point) {
                return XePresenter::makeApi(['type'=>'warning','message'=>'이전과 동일한 값 입니다.']);
            } else {
                $point = $point - $current;
            }
        }

        $handler->addUserPoint($user, $point, $content, $action);

        $userPoint = $handler->getPointObj($user->getId());

        return XePresenter::makeApi(['type'=>'success','message'=>'저장했습니다.', 'user_id'=>$userPoint->user_id, 'point'=>$userPoint->point]);
    }
}
