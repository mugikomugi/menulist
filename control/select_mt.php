<?php
//素材option
$mt_item = ['肉','魚','野菜・果物','乳製品','その他'];
?>
<?php for($i=0; $i < count($mt_item); $i++): ?>
  <?php if(isset($select_mt)){
  if($select_mt === $mt_item[$i]){
    $selected = 'selected="selected"';
    } else {
      $selected = '';
    }
  }
  ?>
  <option value="<?php echo $mt_item[$i]; ?>" <?php echo $selected; ?>><?php echo $mt_item[$i]; ?></option>
<?php endfor; ?>