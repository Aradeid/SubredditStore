<?php self::renderView('partial/header', $model); ?>
<div class="container">
    <?php if (!isset($model['subreddit'])) { ?>
        <p>No valid subreddit selected. Please select a valid subreddit.</p>
    <?php } else { ?>
        <h1>Currently viewing <a href="https://www.reddit.com/r/<?php self::htmlOut(($model['subreddit']->getUrl())) ?>/">/r/<?php self::htmlOut(($model['subreddit']->getUrl())) ?></a></h1>
        <h3>Description:</h3>
        <p><?php self::htmlOut($model['subreddit']->getDescription()) ?></p>
        <h3>Costs:</h3>
        <p>The page has roughly <?php self::htmlOut(ceil($model['subreddit']->getPrice() / $model['subreddit']->getMultiplier() * 10000000)) ?> with a price multiplier of <?php self::htmlOut($model['subreddit']->getMultiplier()) ?>%, having a price of <?php self::htmlOut($model['subreddit']->getPrice()) ?>USD.</p>
        <h3>Rating:</h3>
        <?php if (!isset($model['rating'])) { ?>
            <p>This product has not been rated yet. You can rate it below.</p>
        <?php } else { ?>
            <p>Its current rating is <?php self::htmlOut($model['rating']) ?>.</p>
        <?php } ?>
        <?php self::renderView('partial/rating', $model); ?>
        <h3>Comments:</h3>
        <?php $exists = false; if (sizeof($model['comments']) <= 0) { ?>
            <p>This product has no comments so far. You can leave one below.</p>
        <?php } else { ?>
            <table class=".table">
                <tr>
                    <th>Creator</th>
                    <th>Content</th>
                </tr>
                <?php foreach($model['comments'] as $comment) { ?>
                    <tr>
                        <td><?php echo($comment->getCreator()->getUserName()) ?></td>
                        <td>
                            <?php if ($comment->getCreator()->getId() == $model['user']->getId()) {
                                $exists = true;
                                $model['comment'] = $comment;
                                self::renderView('partial/comment', $model);
                            } else {
                                echo($comment->getContent());
                            } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
        <?php if (!$exists) { self::renderView('partial/comment', $model); } ?>
    <?php } ?>
</div>
<?php self::renderView('partial/footer', $model); ?>