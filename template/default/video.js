$('#youtubeModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // モーダルをトリガーしたボタン
  var videoId = button.data('video-id'); // ボタンのdata-video-id属性からビデオIDを取得
  var iframeSrc = 'https://www.youtube.com/embed/' + videoId; // YouTubeの埋め込みURLを作成
  var modal = $(this);
  modal.find('#youtubeIframe').attr('src', iframeSrc); // iframeのsrc属性を設定
});

// モーダルが閉じられたときにiframeのsrcをクリアする
$('#youtubeModal').on('hidden.bs.modal', function () {
  $(this).find('#youtubeIframe').attr('src', '');
});
