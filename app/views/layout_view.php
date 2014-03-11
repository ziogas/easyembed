<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo locale::translate ( 'title' ); ?></title>
    </head>
    <body>
        <?php if ( isset ( $__page ) ): ?><?php EE::view ( $__page ); ?><?php endif; ?>
    </body>
</html>
