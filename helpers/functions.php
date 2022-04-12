<?php 

function Clean($input)
{

    return stripslashes(strip_tags(trim($input)));
}

?>