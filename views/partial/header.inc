<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title> SCR4 Subreddit Store</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-theme.min.css" rel="stylesheet">
        <link href="css/SubredditStore.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="?">SCR4 Subredditstore</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><?php self::actionLink('Home', 'Index', 'Home'); ?></li>
                        <li><?php self::actionLink('List', 'Index', 'Subreddits'); ?></li>
                        <li><?php self::actionLink('Search', 'Search', 'Subreddits'); ?></li>
                        <li><?php self::actionLink('Checkout', 'Create', 'Order'); ?></li>
                    </ul>
                    <div class="navbar-right">
                        <?php self::renderView('partial/user', $model['user']); ?>
                    </div>
                </div>
            </div>
        </nav>
        <?php if(isset($model['errors'])) {
            self::renderView('partial/errors', $model['errors']);
        } ?>