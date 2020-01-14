<table class="table">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Price</th>
        <th>Description</th>
        <th></th>
    </tr>
    <?php foreach($model['subreddits'] as $subreddit): 
        $inCart = in_array($subreddit->getId(), $model['cart'])?>
        <tr <?php if($inCart) { ?> class="info" <?php } ?> >
            <td><?php self::actionLink($subreddit->getUrl(), 'View', 'Subreddits', array('sid' => $subreddit->getId())); ?></a></td>
            <td><?php self::actionLink($subreddit->getSubmitter()->getUserName(), 'Index', 'Home', array('uid' => $subreddit->getSubmitter()->getId())); ?></td>
            <td><?php self::htmlOut($subreddit->getPrice()) ?>$</td>
            <td><?php echo($subreddit->getDescription()) ?></td>
            <td>
                <?php if (isset($model['user']) && ($subreddit->getSubmitter()->getId() == $model['user']->getId())) {
                    self::actionLink('Edit', 'Register', 'Subreddits', array('sid' => $subreddit->getId()));
                } else if($inCart) { ?>
                    <?php self::beginActionForm('Remove', 'Cart', array('sid' => $subreddit->getId(), 'ctx' => $model['context']), 'post'); ?>
                        <button class="btn btn-link">Remove from cart</button>
                    <?php self::endActionForm(); ?>
                <?php } else { ?>
                    <?php self::beginActionForm('Add', 'Cart', array('sid' => $subreddit->getId(), 'ctx' => $model['context']), 'post'); ?>
                        <button class="btn btn-link">Add to cart</button>
                    <?php self::endActionForm(); ?>
                <?php } ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>    