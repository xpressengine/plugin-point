<?php

return [
    'specific_group' => false, // false 면 사용안함 , true 면 사용함 // 사용할 경우 특정 회원그룹을 지정해 해당 그룹인 경우에만 포인트 동작 함
    'comment_limit_hour' => false, //false 면 사용 안함, true 면 사용함 // 사용할 경우 입력된 시간안에 작성된 글에대한 댓글작성 포인트 획득 가능
    'comment_limit_count' => false, //false 면 사용 안함, true 면 사용함 // 사용할 경우 게시글 하나에 1명의 유저가 댓글 작성으로 포인트를 얻는 횟수 제한
];
