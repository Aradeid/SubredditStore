<?php self::renderView('partial/header', $model); ?>
<div class="container">
    <h1><?php if (isset($model['subreddit'])) { self::htmlOut("Edit Subreddit"); } else { self::htmlOut("Register a new Subreddit"); } ?></h1>

    <?php self::beginActionForm('Register', 'Subreddits', null, 'post'); ?>
        <div class="form-group">
            <label for="url">Name or url of subreddit.</label>
            <input class="form-control" id="url" name="url" <?php if (isset($model['subreddit'])) { echo("readonly value=\"" . $model['subreddit']->getUrl() . "\""); } ?>>
        </div>
        <?php 
            $contents = array();
            foreach ($model['categories'] as $cat) {
                $val = "<option value=". $cat->getId();
                if (isset($model['subreddit']) && ($model['subreddit']->getCategoryId() == $cat->getId())) {
                    $val .= ' selected ';
                }
                $val .= ">" . $cat->getName() . "</option>";
                $contents[] = $val;
            }
            self::formField("Select to which category your subreddit belongs.", "categoryid", "cid", "select", $contents); 
        ?>
        <?php self::formField("Subreddit description.", "description", "desc", "textarea", array(isset($model['subreddit']) ? $model['subreddit']->getDescription() : null)); ?>
        <div class="form-group">
            <label for="multiplier">Multiplier in percents over number of readers.</label>
            <input class="form-control" type="number" id="multiplier" name="mult" <?php if (isset($model['subreddit'])) { echo("value=\"" . $model['subreddit']->getMultiplier() . "\""); } ?>>
        </div>
        <button class="btn btn-primary"><?php if (isset($model['subreddit'])) { self::htmlOut("Edit"); } else { self::htmlOut("Register"); } ?></button>
    <?php self::endActionForm(); ?> 
</div>
<?php self::renderView('partial/footer', $model); ?>