<?php self::renderView('partial/header', $model); ?>
<div class="container">
    <h1>List of Subreddits</h1>
    <br>
    <p>Please select a category.</p>
    <p>
        <ul class="nav nav-pills" role="tablist">
            <?php foreach ($model['categories'] as $cat) { ?>
                <li role="presentation">
                    <?php self::actionLink($cat->getName(), 'Index', 'Subreddits', array('cid' => $cat->getId())); ?>
                </li>
            <?php } ?>
            <li role="presentation">
                <?php self::actionLink('Register a new subreddit!', 'Register', 'Subreddits'); ?>
            </li>
        </ul>
    </p>

    <?php if(sizeof($model['subreddits']) > 0) {
        self::renderView('partial/subreddits', $model);
    } else { ?>
        <p>No subreddits found.</p>
    <?php } ?>

</div>
<?php self::renderView('partial/footer', $model); ?>