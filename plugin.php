<?php
namespace Xpressengine\Plugins\Point;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Route;
use Schema;
use Xpressengine\Plugin\AbstractPlugin;

class Plugin extends AbstractPlugin
{

    public function register()
    {
        app()->singleton(
            ['point::handler' => Handler::class],
            function ($app) {
                $proxyClass = app('xe.interception')->proxy(Handler::class, 'Point');
                return new $proxyClass($this, app('xe.config'));
            }
        );
    }

    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEvent();

        $this->route();
    }

    /**
     * 플러그인의 설정페이지 주소를 반환한다.
     * 플러그인 목록에서 플러그인의 '관리' 버튼을 누를 경우 이 페이지에서 반환하는 주소로 연결된다.
     *
     * @return string
     */
    public function getSettingsURI()
    {
        return route('point::setting.index');
    }

    protected function registerEvent()
    {
        intercept(
            'XeUser@create',
            'point@create',
            function ($target, $data, $token = null) {

                $user = $target($data, $token);

                app('point::handler')->executeAction('user_register', $user);

                return $user;
            }
        );

        app('events')->listen('auth.login', function ($user) {
            if ($user->loginAt === null) {
                return $user;
            }
            if ($user->loginAt->isSameDay(Carbon::now())) {
                return $user;
            }
            app('point::handler')->executeAction('user_login', $user);
        });
    }

    protected function route()
    {
        Route::settings(
            $this->getId(),
            function () {
                Route::group(
                    ['namespace' => 'Xpressengine\Plugins\Point\Controllers'],
                    function () {
                        Route::get(
                            '/',
                            [
                                'as' => 'point::setting.index',
                                'uses' => 'SettingController@index',
                            ]
                        );
                        Route::get(
                            '{userId}',
                            [
                                'as' => 'point::setting.show',
                                'uses' => 'SettingController@show',
                            ]
                        );

                        Route::put(
                            'section',
                            [
                                'as' => 'point::section.update',
                                'uses' => 'SettingController@updateSection',
                            ]
                        );
                    }
                );
            }
        );
    }

    /**
     * 플러그인이 활성화될 때 실행할 코드를 여기에 작성한다.
     *
     * @param string|null $installedVersion 현재 XpressEngine에 설치된 플러그인의 버전정보
     *
     * @return void
     */
    public function activate($installedVersion = null)
    {
        app('point::handler')->storeActionInfo('user_login', ['title'=>'로그인']);
        app('point::handler')->storeActionInfo('user_register', ['title'=>'가입']);
    }

    /**
     * 플러그인을 설치한다. 플러그인이 설치될 때 실행할 코드를 여기에 작성한다
     *
     * @return void
     */
    public function install()
    {
        if (!Schema::hasTable('point')) {
            Schema::create(
                'point',
                function (Blueprint $table) {
                    $table->engine = "InnoDB";

                    $table->string('userId', 36);
                    $table->bigInteger('point');
                    $table->timestamp('createdAt')->index();
                    $table->timestamp('updatedAt')->index();
                    $table->primary('userId');
                }
            );
        }

        if (!Schema::hasTable('point_log')) {
            Schema::create(
                'point_log',
                function (Blueprint $table) {
                    $table->engine = "InnoDB";

                    $table->increments('id');
                    $table->string('userId', 36);
                    $table->string('action', 20);
                    $table->bigInteger('point');
                    $table->string('content');
                    $table->timestamp('createdAt')->index();
                    $table->timestamp('updatedAt');
                    $table->index('userId');
                }
            );
        }
    }

    /**
     * 해당 플러그인이 설치된 상태라면 true, 설치되어있지 않다면 false를 반환한다.
     * 이 메소드를 구현하지 않았다면 기본적으로 설치된 상태(true)를 반환한다.
     *
     * @return boolean 플러그인의 설치 유무
     */
    public function checkInstalled()
    {
        // implement code

        return parent::checkInstalled();
    }

    /**
     * 플러그인을 업데이트한다.
     *
     * @return void
     */
    public function update()
    {
        // implement code
    }

    /**
     * 해당 플러그인이 최신 상태로 업데이트가 된 상태라면 true, 업데이트가 필요한 상태라면 false를 반환함.
     * 이 메소드를 구현하지 않았다면 기본적으로 최신업데이트 상태임(true)을 반환함.
     *
     * @return boolean 플러그인의 설치 유무,
     */
    public function checkUpdated()
    {
        // implement code

        return parent::checkUpdated();
    }
}
