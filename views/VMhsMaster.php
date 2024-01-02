<?php

namespace PHPMaker2021\project1;

// Table
$v_mhs = Container("v_mhs");
?>
<?php if ($v_mhs->Visible) { ?>
<div class="ew-master-div">
<table id="tbl_v_mhsmaster" class="table ew-view-table ew-master-table ew-vertical">
    <tbody>
<?php if ($v_mhs->id_mhs->Visible) { // id_mhs ?>
        <tr id="r_id_mhs">
            <td class="<?= $v_mhs->TableLeftColumnClass ?>"><?= $v_mhs->id_mhs->caption() ?></td>
            <td <?= $v_mhs->id_mhs->cellAttributes() ?>>
<span id="el_v_mhs_id_mhs">
<span<?= $v_mhs->id_mhs->viewAttributes() ?>>
<?= $v_mhs->id_mhs->getViewValue() ?></span>
</span>
</td>
        </tr>
<?php } ?>
<?php if ($v_mhs->nim->Visible) { // nim ?>
        <tr id="r_nim">
            <td class="<?= $v_mhs->TableLeftColumnClass ?>"><?= $v_mhs->nim->caption() ?></td>
            <td <?= $v_mhs->nim->cellAttributes() ?>>
<span id="el_v_mhs_nim">
<span<?= $v_mhs->nim->viewAttributes() ?>>
<?= $v_mhs->nim->getViewValue() ?></span>
</span>
</td>
        </tr>
<?php } ?>
<?php if ($v_mhs->nama_mhs->Visible) { // nama_mhs ?>
        <tr id="r_nama_mhs">
            <td class="<?= $v_mhs->TableLeftColumnClass ?>"><?= $v_mhs->nama_mhs->caption() ?></td>
            <td <?= $v_mhs->nama_mhs->cellAttributes() ?>>
<span id="el_v_mhs_nama_mhs">
<span<?= $v_mhs->nama_mhs->viewAttributes() ?>>
<?= $v_mhs->nama_mhs->getViewValue() ?></span>
</span>
</td>
        </tr>
<?php } ?>
<?php if ($v_mhs->jurusan->Visible) { // jurusan ?>
        <tr id="r_jurusan">
            <td class="<?= $v_mhs->TableLeftColumnClass ?>"><?= $v_mhs->jurusan->caption() ?></td>
            <td <?= $v_mhs->jurusan->cellAttributes() ?>>
<span id="el_v_mhs_jurusan">
<span<?= $v_mhs->jurusan->viewAttributes() ?>>
<?= $v_mhs->jurusan->getViewValue() ?></span>
</span>
</td>
        </tr>
<?php } ?>
    </tbody>
</table>
</div>
<?php } ?>
