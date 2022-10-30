<?php

if(empty($this->authorized)) {
    $objects = $this->getObjects(null);
} else {
    $objects = $this->getAllObjects();
}

$title = $this->i18n($objects ? 'tree data' : 'no data');

?>

<h1><?=$title?></h1>

<?php if(empty($this->authorized)) { ?>
<ul class="tree">
<?php } else { ?>
<ul class="tree all">
    <li>
        <a class="button" href="#add"><?=$this->i18n('add object')?></a>
    </li>
<?php } ?>
    <?php echo $objects; ?>
</ul>

<div id="fixed" class="fixed"></div>