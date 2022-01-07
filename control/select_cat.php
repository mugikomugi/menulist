<?php
//カテゴリーoption
//db_join.phpより$cat_listの配列から
$selected = '';
?>
<?php for($i=0; $i < count($cat_list); $i++): ?>
  <?php if(isset($select_cat)){
      if($select_cat === $cat_list[$i]){
        $selected = 'selected="selected"';
        } else {
          $selected = '';
        }
      }
  ?>
  <option value="<?php echo $cat_list[$i]; ?>" <?php echo $selected; ?>><?php echo $cat_list[$i]; ?></option>
<?php endfor; ?>
