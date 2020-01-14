<?php self::renderView('partial/header', $model); ?>
<div class="container">
    <h1>List of Categories</h1>
    <p>
        <ul class="nav nav-pills">
            <?php foreach ($model['categories'] as $cat) { ?>
                <li role="presentation">
                    <?php self::actionLink($cat->getName(), 'Index', 'Subreddits', array('cid' => $cat->getId())); ?>
                </li>
            <?php } ?>
        </ul>
    </p>
</div>
<?php self::renderView('partial/footer', $model); ?>