<?php if(!class_exists('Rain\Tpl')){exit;}?><?php $counter1=-1;  if( isset($icon_url) && ( is_array($icon_url) || $icon_url instanceof Traversable ) && sizeof($icon_url) ) foreach( $icon_url as $key1 => $value1 ){ $counter1++; ?>

  <div <div class ='col-xs-12 col-sm-4 col-md-4 col-lg-4'>
    <center><img class="ico" src=<?php echo htmlspecialchars( $value1, ENT_COMPAT, 'UTF-8', FALSE ); ?>>
    <br><br>
    <span class="icons-text"><?php echo htmlspecialchars( $key1, ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
    <center>
  </div>
<?php } ?>

