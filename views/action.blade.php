{{ \XeFrontend::js(['https://unpkg.com/@lottiefiles/lottie-player@0.4.0/dist/lottie-player.js'])->appendTo('head')->load() }}

<div class="xf-visit-popup">
    <div class="xf-visit-popup__modal">
        <lottie-player
            autoplay
            loop
            mode="normal"
            src="{{ $jsonPath }}"
            class="xf-visit-popup__modal-animation"
            style="width: 320px; margin-top: -32px">
        </lottie-player>
        <div class="xf-visit-popup__modal-content">
            <h1 class="xf-visit-popup__title">
                <span>방문</span>
                <span class="xf-visit-popup__point-text">+{{ number_format($receivedPoint) }} 포인트</span>
                <span>지급</span>
            </h1>
            <p class="xf-visit-popup__description">
                <span>포럼게시판에서 사용할 수 있는 포인트입니다.</span>
            </p>
        </div>
        <div class="xf-visit-popup__modal-footer">
            <button class="xf-visit-popup__button">확인</button>
        </div>
    </div>
</div>
<style>
    .xf-visit-popup__modal-header,
    .xf-visit-popup__modal-content,
    .xf-visit-popup__modal-footer {
        position: relative;
        z-index: 1;
    }
    .xf-visit-popup__modal-content {
        width: 100%;
        margin-top: 160px;
        text-align: center;
    }
    .xf-visit-popup,
    .xf-visit-popup::before {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        content: '';
        background: rgba(0,0,0,0.25);
    }
    .xf-visit-popup {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 999;
        display: none;
        align-items: center;
        justify-content: center;
        background: transparent;

    }
    .xf-visit-popup.xf-visit-popup--active {
        display: flex;
    }
    .xf-visit-popup__modal {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 320px;
        height: 320px;
        background: #fff;
        border-radius: 12px;
        z-index: 1;
    }
    .xf-visit-popup__modal-footer {
        display: flex;
        justify-content: center;
    }
    .xf-visit-popup__modal-content {
        overflow: hidden;
    }
    .xf-visit-popup__modal-animation {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
    }
    .xf-visit-popup__description {
        margin-top: 6px;
        font-size: 15px;
        color: #8C8C8C;
    }
    .xf-visit-popup__title {
        font-size: 24px;
        margin-bottom: 0;
    }
    .xf-visit-popup__point-text { color: #345BD9 }
    .xf-visit-popup__button:hover {
        cursor: pointer;
        background: #2749B7;
        transition: background 0.25s ease-in-out;
    }
    .xf-visit-popup__button {
        padding: 12px 24px;
        color: #fff;
        font-size: 15px;
        font-weight: bold;
        border: none;
        background: #345BD9;
        border-radius: 999px;
        box-sizing: border-box;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        transition: background 0.25s ease-in-out;
    }
</style>
<script>
  $(function () {
    $('.xf-visit-popup').addClass('xf-visit-popup--active')
    $('.xf-visit-popup').on('click', function () {
      $(this).removeClass('xf-visit-popup--active');
    })
    $('.xf-visit-popup__modal').on('click', function (event) {
      event.stopPropagation();
    });
    $('.xf-visit-popup__button').on('click', function () {
      $('.xf-visit-popup').removeClass('xf-visit-popup--active');
    });
  });
</script>