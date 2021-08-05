<?php

namespace Xpressengine\Plugins\Point\EventListeners;

use Xpressengine\Plugins\Point\Handler as PointHandler;

abstract class UserActions
{
    /**
     * 회원가입 시 포인트를 지급하는 리스너 등록
     * 
     * @return void
     */
    public static function listenRegister()
    {
        intercept(
            'XeUser@create',
            'point@create',
            function ($target, $data, $token = null) {
                $user = $target($data, $token);
                $action = 'user_register';

                if(app('config')->get('point')['specific_group']) {
                    if ($user->specific !== true) {
                        return $user;
                    }
                }

                return $user;
            }
        );
    }

    /**
     * 로그인 시 포인트를 지급하는 리스너 등록
     *
     * @retyrn void
     */
    public static function listenLogin()
    {
        app('events')->listen('Illuminate\Auth\Events\Login', function ($login) {
            $user = $login->user;
            $action = 'user_login';
            $pointHandler = app(PointHandler::class);

            if ($user->login_at === null) {
                return $user;
            }

            if ($pointHandler->isReceivedTodayPoint($user, $action)) {
                return $user;
            }

            if(app('config')->get('point')['specific_group']) {
                if ($user->specific !== true) {
                    return $user;
                }
            }

            if ($pointHandler->checkAction($action, $user) == false) {
                $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException([], 500);
                $exception->setMessage('[포인트 부족] login 할 수 없습니다.');

                throw $exception;
            }


            if (app('point::handler')->executeAction($action, $user)) {
                request()->session()->flash('received_point', ['action' => $action, 'point' => app('point::handler')->getActionPoint($action)]);
            }

            $pointHandler->executeAction($action, $user);
            return $user;
        });
    }
}