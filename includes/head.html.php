<?php
if (!is_included()) die('Tried to run an include by itself! This kills the script.');
?>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta charset="utf8" />
        <?php
        if ($USE_BOOTSTRAP)
        {
            echo '<script src="'.BOOTSTRAP_CURRENT_JS.'"></script>'.n;
            echo ind(2).'<link rel="stylesheet" href="'.BOOTSTRAP_CURRENT_CSS.'" />'.n;
            if (isset($BOOTSTRAP_THEME))
            {
                echo ind(2).'<link rel="stylesheet" href="'.$BOOTSTRAP_THEME.'" />'.n;
            }
        }
        if (isset($ADDITIONAL_HEAD))
        {
            echo $ADDITIONAL_HEAD;
        }
        ?>
        <link rel="stylesheet" href="custom.css" />
        <title><?=$PAGE_TITLE?></title>
    </head>
