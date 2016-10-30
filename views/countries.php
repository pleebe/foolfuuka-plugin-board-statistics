<?php $data_array = json_decode($data, true); ?>
<table class="table table-hover">
    <thead>
    <tr>
        <th class="span8"><?= _i('Country') ?></th>
        <th class="span2"><?= _i('Total Posts') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data_array as $d) : ?>
        <tr>
            <td>
                <?php
                $poster_country_name = '';
                if ($d['poster_country'] !== null) {
                    $poster_country_name = $this->config->get('foolz/foolfuuka', 'geoip_codes', 'codes.'.strtoupper($d['poster_country']));
                }
                ?>
                <a href="<?= $this->uri->create([$this->radix->shortname, 'search', 'country', urlencode($d['poster_country'])]) ?>">
                    <span class="country"><span class="flag flag-<?= strtolower($d['poster_country']) ?>"></span> <?= htmlentities($d['poster_country']) ?><?php if($poster_country_name !== null) : ?> - <?= $poster_country_name ?><?php endif; ?></span>
                </a>
            </td>
            <td><?= $d['countries'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>