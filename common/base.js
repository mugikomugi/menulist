jQuery(function(){
  jQuery('#orderBox').css('display','none');

  //同一ページスクロール
  jQuery('.topNav a[data-id]').on ('click',function() {
		let dataID= jQuery(this).attr('data-id');
		let position = jQuery('#'+dataID).offset().top-20;
		 // スムーススクロール
		jQuery('body,html').animate({scrollTop:position}, 500, 'swing');
	});

  //検索ボタン
  jQuery('#sarchBox').css('display','block');
  jQuery('#sarch a').on('click',function(){
    if(jQuery('#sarchBox').css('display') == 'block'){
      jQuery('#sarchBox').slideUp('first');
      jQuery(this).css('background','#FA9600 url(/menulist/image/icon_plus_wh.svg) 338px center no-repeat');
      return false;
    } else {
      jQuery('#sarchBox').slideDown('first');
      jQuery(this).css('background','#FA9600 url(/menulist/image/icon_minus_wh.svg) 338px center no-repeat');
      return false;
    }
  });

  //検索件数表示
  let sarchLi = jQuery('#sarchMenu .menuBox').find('li');
  let liNone = jQuery('#sarchMenu ul.none').find('li');
  let sarchCount = sarchLi.length - liNone.length;
  if(sarchCount != 0){
    jQuery('#sarchCount').text(sarchCount+'件見つかりました');
  } else {
    jQuery('#sarchCount').text('該当メニューは見つかりませんでした');
  }

  //submitで持っていく値の個数と番号の変数
  let count;
  //商品をクリックしたら注文リストへ
  jQuery('.selectOrder').on('click',function(){
    //選んだ商品データを変数に入れておく
    const orderName = jQuery('.menuName span',this).text();
    const orderImg = jQuery('.img img',this).attr('src');
    const orderPlice = jQuery('.orderPlic',this).text();
    //カウントを取得しておかないとおかしくなる
    count = jQuery('#count').attr('value');
    //count位置重要
    ++count;

    //注文リスト表示
    jQuery('#orderBox').css('display','block');
    jQuery('#header,#main').css('display','none');
    //変更ページ
    jQuery('#orderChangeBox').css('display','block');

    //注文の商品名、値段、画像、個数を書き出し、カウント数と合わせておく
    jQuery('#orderMenu').append('<tr><th class="orderName">'+orderName+'</th><td class="orderImg"><img src="'+orderImg+'" alt=""></td><td class="orderOneplice">'+orderPlice+'円<span class="smallText">(税込)</span><img src="image/icon_x.svg" alt=""></td><td class="num"><input type="number" name="num'+count+'" min="1" max="50" value="1">個</td><td><p class="deleteMenu">削除<img src="image/icon_x.svg" alt=""></p></td></tr>');

    jQuery('#changeMenu').append('<tr><th class="orderName">'+orderName+'</th><td class="orderImg"><img src="'+orderImg+'" alt=""></td><td class="orderOneplice">'+orderPlice+'円<span class="smallText">(税込)</span><img src="image/icon_x.svg" alt=""></td><td class="num"><input type="number" name="num'+count+'" min="1" max="50" value="1">個</td><td><p class="cancel">削除<img src="image/icon_x.svg" alt=""></p></td></tr>');
    
    //name属性+カウント数でgetで持っていく値と数を指定
    jQuery('#count').before('<input type="hidden" name="title'+count+'" value="'+orderName+'"><input type="hidden" name="plice'+count+'" value="'+orderPlice+'"><input type="hidden" name="img'+count+'" value="'+orderImg+'">');
    jQuery('#count').attr('value',count);
  });

  //MENU削除、意外と大変
  jQuery(document).on('click', '.deleteMenu', function(){
    //デリートボタンを押したtrのみを削除
    jQuery(this).parents('tr').remove();
    //現在のカウントを取得
    count = jQuery('#count').attr('value');

    //削除によって揃わなくなった個数と順番の連動を合わせる
     for(let i=0; i < count; ++i){
       jQuery('input[name^="title"]').eq(i).attr('name','title'+i);
       jQuery('input[name^="plice"]').eq(i).attr('name','plice'+i);
       jQuery('input[name^="num"]').eq(i).attr('name','num'+i);
       jQuery('input[name^="img"]').eq(i).attr('name','img'+i);
       }

    //1個ずつ持っていく数を減らす
    --count; 
    jQuery('#count').attr('value',count);

    //確認リストから商品がなくなったら注文リストへ移行
    if(count < 0){
      jQuery('#orderBox').css('display','none');
      jQuery('#header,#main').css('display','block');
    }
  });

  /*
jQueryでappendメソッドで後から追加した要素に、clickイベントを効かせるようにする方法は、要素に対してではなく、documentに対してイベントを設定すること
https://qumeru.com/magazine/401
  */
 
});