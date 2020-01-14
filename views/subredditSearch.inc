<?php self::renderView('partial/header', $model); ?>
<div class="container">
    <h1>Search</h1>
    <p>
        <?php self::beginActionForm('Search', 'Subreddits', null, 'get', 'form-inline'); ?>
            <div class="form-group">
                <label for="title">Title</label>
                <input class="form-control" id="url" name="url" value="<?php self::htmlOut($model['url']); ?>">
            </div>
            <button class="btn btn-primary">Search</button>
        <?php self::endActionForm(); ?>
    </p>
    <?php if ($model['subreddits'] !== null): ?>
        <?php if(sizeof($model['subreddits']) > 0) {
            self::renderView('partial/subreddits', array('subreddits' => $model['subreddits'], 'cart' => $model['cart'], 'context' => $model['context']));
        } else { ?>
            <p>No matching subreddits found.</p>
        <?php } ?>
    <?php endif; ?>
</div>
<?php self::renderView('partial/footer', $model); ?>