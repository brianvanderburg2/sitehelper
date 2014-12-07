<table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th class="i">&nbsp;</th>
            <th class="n"><a href="<?= htmlspecialchars($links['n']); ?>">Name</a></th>
            <th class="m"><a href="<?= htmlspecialchars($links['m']); ?>">Modified</a></th>
            <th class="s"><a href="<?= htmlspecialchars($links['s']); ?>">Size</a></th>
            <th class="t"><a href="<?= htmlspecialchars($links['t']); ?>">Type</a></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $even = FALSE;
            foreach($contents as $e)
            {
                $link = htmlspecialchars($e['l']);
                $name = htmlspecialchars($e['n']);
                $modified = ($e['m'] === FALSE) ? '&nbsp;' : htmlspecialchars($e['m']);
                $size = ($e['s'] === FALSE) ? '&nbsp;' : htmlspecialchars($e['s']);
                $type = htmlspecialchars($e['t']);
                $icon = ($e['i'] === FALSE) ? '&nbsp;' : '<a href="' . $link . '"><img src="' . htmlspecialchars($e['i']) . '" alt="" /></a>';
        ?>
        <tr class="<?= $even ? 'e' : 'o'; ?>">
            <td class="i"><?= $icon; ?></td>
            <td class="n"><a href="<?= $link; ?>"><?= $name; ?></a></td>
            <td class="m"><?= $modified; ?></td>
            <td class="s"><?= $size; ?></td>
            <td class="t"><?= $type; ?></td>
        </tr>

        <?php $even = !$even; } ?>
    </tbody>
</table>
