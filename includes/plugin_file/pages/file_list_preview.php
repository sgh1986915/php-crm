<div class="file_<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 9809 f200f46c2a19bb98d112f2d32a8de0c4
  * Envato: 4ffca17e-861e-4921-86c3-8931978c40ca
  * Package Date: 2015-11-25 02:55:20 
  * IP Address: 67.79.165.254
  */ echo $owner_table;?>_<?php echo $owner_id;?>">
    <?php
    foreach($file_items as $file_item){
        $file_item = self::get_file($file_item['file_id']);
        ?>
        
        <div style="width:110px; height:90px; overflow:hidden; ">

            <?php
            // /display a thumb if its supported.
            if(preg_match('/\.(\w\w\w\w?)$/',$file_item['file_name'],$matches)){
                switch(strtolower($matches[1])){
                    case 'jpg':
                    case 'jpeg':
                    case 'gif':
                    case 'png':
                        ?>
                            <img src="<?php echo _BASE_HREF . nl2br(htmlspecialchars($file_item['file_path']));?>" width="100" alt="preview" border="0">
                        <?php
                        break;
                    default:
                        echo 'n/a';
                }
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>

