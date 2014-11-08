<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Error <?= htmlspecialchars($status); ?></title>
    <?php $self->send('mrbavii.helper.error.stylesheet'); ?>
</head>

<body>
    <h2><?= htmlspecialchars($status); ?> - <?= htmlspecialchars($reason); ?></h2>

