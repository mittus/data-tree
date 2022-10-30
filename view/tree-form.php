<form method="post">
    <fieldset>
        <div>
            <label><?=$this->i18n('form name')?>
                <input type="text" name="name" value="<?=$form['name']?>">
            </label>
        </div>
        <div>
            <label><?=$this->i18n('form description')?>
                <input type="text" name="description" value="<?=$form['description']?>">
            </label>
        </div>
        <div>
            <label><?=$this->i18n('form parent')?>
                <input type="text" name="parent" value="<?=$form['parent']?>">
            </label>
        </div>
        <div>
            <input type="hidden" name="id" value="<?=$form['id']?>">
            <input type="submit" name="save" value="Сохранить">
        </div>
    </fieldset>
</form>    