<?php self::renderView('partial/header', $model); ?>
<div class="container">
    <h1>Register</h1>

    <?php self::beginActionForm('Register', 'User', null, 'post'); ?>
        <div class="form-group">
            <label for="userName">Username</label>
            <input class="form-control" id="userName" name="un" value="<?php self::htmlOut($model['userName']); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" type="password" id="password" name="pwd">
        </div>
        <div class="form-group">
            <label for="password-confirm">Confirm Password</label>
            <input class="form-control" type="password" id="password-confirm" name="pwd-conf">
        </div>
        <button class="btn btn-primary">Register</button>
    <?php self::endActionForm(); ?> 
</div>
<?php self::renderView('partial/footer', $model); ?>