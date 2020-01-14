<?php self::beginActionForm('Rate', 'Subreddits', null, 'post'); ?>
    <input type="hidden" name="sid" value="<?php echo($model['subreddit']->getId()) ?>">
    <select class="form-control" id="ratingValue" name="rval">
        <?php for ($i = 1; $i <= 5; $i++) { ?>
            <option value="<?php echo($i) ?>" <?php if ($model['ownRating'] === $i) { echo('selected'); } ?>><?php echo($i) ?></option>
        <?php } ?>
    </select>
    <button class="btn btn-primary"><?php if(isset($model['ownRating'])) {echo('Update');} else {echo('Send');} ?></button>
<?php self::endActionForm(); ?>