<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Listing for <?= htmlspecialchars($uripath); ?></title>
    <?php $self->send('mrbavii.helper.listdir.stylesheet'); ?>
</head>

<body>
    <h2>Listing of <span><?= htmlspecialchars($uripath); ?></span></h2>

