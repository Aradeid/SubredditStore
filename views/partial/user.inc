<ul class="nav navbar-nav">
<?php if(!isset($model)): ?>
    <li><?php self::actionLink('Login', 'LogIn', 'User'); ?></li>
<?php else: ?>
    <li>
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Welcome, <?php self::htmlOut($model->getUserName()); ?>
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li>
                <?php self::beginActionForm('LogOut', 'User', null, 'post', 'navbar-form'); ?>
                    <button class="btn btn-link">Log out</button>
                <?php self::endActionForm(); ?>
            </li>
            <li><?php self::actionLink('Profile', 'Index', 'User'); ?></li>
            <li><?php self::actionLink('Cart', 'Index', 'Home'); ?></li>
        </ul>        
    </li>
    <li>
        
    </li>
<?php endif; ?>
</ul>