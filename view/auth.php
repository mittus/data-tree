<div>
    <h1><?=$this->i18n['authorization']?></h1>
    <div style="width: 250px;">
        <form method="post">
            <div>
                <input type="text" placeholder="<?=$this->i18n('login')?>" name="name" value="<?=$this->auth['user']?>">
            </div>
            <div>
                <input type="password" placeholder="<?=$this->i18n['password']?>" name="password">
            </div>
            <div>
                <div class="error"><?=$this->error?></div>
                <input type="submit" name="login" value="<?=$this->i18n('enter')?>">
            </div>
            <div>
                <input type="submit" name="register" value="<?=$this->i18n('register')?>">
            </div>
        </form>
    </div>
</div>