//管理画面用js
jQuery(function(){
  
  jQuery('#update,#deleteForm,.notice2').css('display','none');
  jQuery('.notice2').css('display','none');

  jQuery('.formChange li').on('click',function(){
    //clickしたid名を取得してformを表示
    let clickId = jQuery(this).attr('data-id');
    jQuery('.changeBox').css('display','none');
    jQuery('#'+clickId).css('display','block');
    //clickのliにactを付け替える
    jQuery(this).addClass('act').siblings().removeClass('act');
    //説明文を入れ替え
    if(jQuery('.formChange li[data-id="insart"]').hasClass('act')){
      jQuery('.notice1').css('display','block');
      jQuery('.notice2').css('display','none');
    } else {
      jQuery('.notice1').css('display','none');
      jQuery('.notice2').css('display','block');
    }
  });

  //一覧カテゴリーボタン
  jQuery('.topNav li').on('click',function(){
    let catName = jQuery(this).attr('id');
    jQuery('#stockList tr:nth-of-type(n+2)').css('display','none');
    jQuery('#stockList .'+catName).css('display','block');
    let showCat = jQuery('#stockList .'+catName).length;
    jQuery('#catShow').text('カテゴリー別登録数'+showCat+'個');
  });
  jQuery('#all').on('click',function(){
    jQuery('#stockList tr').css('display','block');
    jQuery('#catShow').text('');
  });

});