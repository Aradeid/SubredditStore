<?php self::beginActionForm('Comment', 'Subreddits', null, 'post'); ?>
    <input type="hidden" name="sid" value="<?php echo($model['subreddit']->getId()) ?>">
    <?php self::formField("Write your comment.", "comment", "cval", "textarea", array(isset($model['comment']) ? $model['comment']->getContent() : null)) ?>
    <button class="btn btn-primary"><?php if(isset($model['comments'])) {echo('Update');} else {echo('Send');} ?></button>
<?php self::endActionForm(); ?>