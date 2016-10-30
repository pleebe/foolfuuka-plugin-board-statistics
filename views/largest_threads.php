<?php
$data_array = json_decode($data); ?>
    <table class="table table-hover">
    <thead>
    <tr>
        <th class="span1"></th>
        <th class="span4"><?= _i('Subject') ?></th>
        <th class="span1"><?= _i('Thread') ?></th>
        <th class="span1"><?= _i('Posts') ?></th>
        <th class="span1"><?= _i('Images') ?></th>
        <th class="span3"><?= _i('Started') ?></th>
        <th class="span3"><?= _i('Ended') ?></th>
    </tr>
    </thead>
    <tbody>
<?php foreach ($data_array as $key => $item) :
    ?>
    <tr>
        <td>#<?= $key+1 ?></td>
        <td>
            <?php if ($item->title != '') : ?>
                <?php if (mb_strlen($item->title) > 25) : ?>
                    <span class="subject" rel="tooltip" title="<?= htmlspecialchars(strip_tags($item->title)) ?>">
                        <?= mb_substr(htmlspecialchars(strip_tags($item->title)), 0, 19, 'utf-8') . ' (...)' ?>
                    </span>
                <?php else: ?>
                    <?= htmlspecialchars(strip_tags($item->title)) ?>
                <?php endif; ?>
            <?php else: ?>
                <?php if (mb_strlen($item->comment) > 25) : ?>
                    <span class="subject" rel="tooltip" title="<?= htmlspecialchars(strip_tags($item->comment)) ?>">
                        <?= mb_substr(htmlspecialchars(strip_tags($item->comment)), 0, 19, 'utf-8') . ' (...)' ?>
                    </span>
                <?php else: ?>
                    <?= htmlspecialchars(strip_tags($item->comment)) ?>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <td><a href="<?= $this->uri->create([$this->radix->shortname, 'thread', $item->thread_num]) ?>">
                &gt;&gt;<?= $item->thread_num  ?></a></td>
        <td><?= $item->nreplies ?></td>
        <td><?= $item->nimages ?></td>
        <td><?= date('M d Y H:i:s', $item->time_op) ?></td>
        <td><?= date('M d Y H:i:s', $item->time_last) ?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
    </table>
