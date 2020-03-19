<?php
return [
    'pointSetup' => [
        'ko' => '포인트 설정',
        'en' => 'Point Setup',
    ],
    'baseConfig' => [
        'ko' => '기본 설정',
        'en' => 'Base Config',
    ],
    'instanceConfig' => [
        'ko' => '인스턴스 설정',
        'en' => 'Instance Config',
    ],
    'userPointList' => [
        'ko' => '사용자 포인트 목록',
        'en' => 'User Point List',
    ],
    'levelSetup' => [
        'ko' => '레벨 설정',
        'en' => 'Level Setup',
    ],
    'pointEarnUseLog' => [
        'ko' => '포인트 적립/사용 내역',
        'en' => 'Point Earn/Use Log',
    ],
    'logForUser' => [
        'ko' => ':user_name 님의 포인트 사용 내역 ',
        'en' => 'Point use log for :user_name ',
    ],
    'point' => [
        'ko' => '포인트',
        'en' => 'Point',
    ],
    'articleStore' => [
        'ko' => '게시물 등록',
        'en' => 'Article Store',
    ],
    'articleDestroy' => [
        'ko' => '게시물 삭제',
        'en' => 'Article Destroy',
    ],
    'commentStore' => [
        'ko' => '댓글 등록',
        'en' => 'Comment Store',
    ],
    'commentDestroy' => [
        'ko' => '댓글 삭제',
        'en' => 'Comment Destroy',
    ],
    'uploadFile' => [
        'ko' => '파일 업로드',
        'en' => 'File Upload',
    ],
    'downloadFile' => [
        'ko' => '파일 다운로',
        'en' => 'File Download',
    ],
    'readDocument' => [
        'ko' => '게시글 조회',
        'en' => 'Read Document',
    ],
    'receiveAssentDocument' => [
        'ko' => '추천 받음',
        'en' => 'Receive Assent',
    ],
    'receiveDissentDocument' => [
        'ko' => '비추천 받음',
        'en' => 'Receive Dissent',
    ],
    'userPointLogButton' => [
        'ko' => '포인트(:point) 내역',
        'en' => 'Point(:point) Log',
    ],
    'pointFunctionOn' => [
        'ko' => '포인트 기능 켜기',
        'en' => 'Point Function On',
    ],
    'pointFunctionOnDescription' => [
        'ko' => '체크 하면 포인트 기능을 켤 수 있습니다. 포인트 기능을 끌 경우, 포인트 적립 기능이 삭제됩니다. 포인트 기록은 유지되지만 새로 기록 되지는 않습니다.',
        'en' => 'If checked, the point function can be turned on. When you turn off the point function, the point accumulation function is deleted. Point records are maintained, but not new.',
    ],
    'maxLevel' => [
        'ko' => '최고 레벨',
        'en' => 'Max Level',
    ],
    'maxLevelDescription' => [
        'ko' => '최고레벨을 지정할 수 있습니다. 레벨 아이콘을 염두에 두어야 하고 최고 레벨은 1000이 한계입니다.',
        'en' => 'You can specify the highest level. You should keep in mind the level icon and the top level is 1000.',
    ],
    'pointName' => [
        'ko' => '포인트 이름',
        'en' => 'Point Name',
    ],
    'pointNameDescription' => [
        'ko' => '포인트 이름이나 단위를 정할 수 있습니다.',
        'en' => 'You can choose a point name or unit.',
    ],
    'levelIcon' => [
        'ko' => '레벨 아이콘',
        'en' => 'Level Icon',
    ],
    'levelIconDescription' => [
        'ko' => '레벨 아이콘은 ./plugins/point/assets/images/icons/레벨.gif 로 지정되며 최고레벨과 아이콘셋이 다를 수 있으니 주의해주세요!',
        'en' => 'The level icon is specified as ./plugins/point/assets/images/icons/level.gif. Please note that the top level and icon set may be different!',
    ],
    'disableDownload' => [
        'ko' => '다운로드 금지',
        'en' => 'Disable Download',
    ],
    'disableDownloadDescription' => [
        'ko' => '포인트가 부족할 경우 다운로드를 금지 합니다. (이미지 파일, 동영상 파일등 직접 링크가 가능한 파일들은 예외입니다.)',
        'en' => 'If there are not enough points, download is prohibited. (Except for files that can be directly linked, such as image files and video files.)',
    ],
    'disableReadBoard' => [
        'ko' => '글 열람 금지',
        'en' => 'Disable Read Board',
    ],
    'disableReadBoardDescription' => [
        'ko' => '포인트가 부족할 경우 글 열람을 금지 합니다.',
        'en' => 'If there are not enough points, reading of articles is prohibited.',
    ],
    'groupInterlock' => [
        'ko' => '그룹 연동',
        'en' => 'Group Interlock',
    ],
    'groupInterlockDescription' => [
        'ko' => '그룹에 원하는 레벨을 지정하면, 회원의 포인트가 해당 레벨의 포인트에 도달할 때 그룹이 변경됩니다.',
        'en' => 'If you assign a desired level to a group, the group will change when a member\'s point reaches that level\'s point.',
    ],
];
