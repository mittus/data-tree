<li data-id="<?=$value['id']?>">
<?php if(empty($this->authorized)) { ?>
    <div class="out-controls">
        <?php if(!empty($parents)) { ?>
            <a class="toggle" href="#toggle"></a>
        <?php } ?>
    </div>
    <div>
        <a href="#show">
            <h2><?=$value['name']?></h2>
        </a>
    </div>
<?php } else { ?>
    <div class="id">#<?=$value['id']?></div>
    <div class="out-controls">
        <a href="#add">✚</a>
        <?php if(!empty($parents)) { ?>
            <a class="toggle expand" href="#toggle"></a>
        <?php } ?>
    </div>
    <div class="controls">
        <a href="#edit">✎</a>
        <a href="#remove">✖</a>
    </div>
    <div>
        <h2><?=$value['name']?></h2>
        <p><?=$value['description']?></p>
    </div>
<?php } ?>
</li>