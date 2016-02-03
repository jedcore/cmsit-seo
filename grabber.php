<form action="" method="POST">
    <input type="text" name="url" size="65" placeholder="http://yoururl"/>
    <input type="submit" name="submit" value="Go!">
</form>

<?php
if(isset($_POST['submit']))
{
    $tags = get_meta_tags($_POST['url']);
    if($tags['generator'])
         echo "CMS: <b>" . $tags['generator'] . "</b><br>";
    else
         echo "No CMS info available.<br>";
}
?>