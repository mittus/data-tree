<?php

$objects = $this->authorized ? $this->getAllObjects() : $this->getObjects();

$title = $this->i18n($objects ? 'tree data' : 'no data');

?>

<h1><?=$title?></h1>

<ul class="tree">
    <?=$this->getAddButton();?>
    <?=$objects?>
</ul>

<div id="fixed" class="fixed"></div>