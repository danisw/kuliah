<?php

namespace PHPMaker2021\project1;

// Page object
$PenggunaDelete = &$Page;
?>
<script>
var currentForm, currentPageID;
var fpenggunadelete;
loadjs.ready("head", function () {
    var $ = jQuery;
    // Form object
    currentPageID = ew.PAGE_ID = "delete";
    fpenggunadelete = currentForm = new ew.Form("fpenggunadelete", "delete");
    loadjs.done("fpenggunadelete");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<script>
if (!ew.vars.tables.pengguna) ew.vars.tables.pengguna = <?= JsonEncode(GetClientVar("tables", "pengguna")) ?>;
</script>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<form name="fpenggunadelete" id="fpenggunadelete" class="form-inline ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="pengguna">
<input type="hidden" name="action" id="action" value="delete">
<?php foreach ($Page->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode(Config("COMPOSITE_KEY_SEPARATOR"), $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?= HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="card ew-card ew-grid">
<div class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<table class="table ew-table">
    <thead>
    <tr class="ew-table-header">
<?php if ($Page->id_pengguna->Visible) { // id_pengguna ?>
        <th class="<?= $Page->id_pengguna->headerCellClass() ?>"><span id="elh_pengguna_id_pengguna" class="pengguna_id_pengguna"><?= $Page->id_pengguna->caption() ?></span></th>
<?php } ?>
<?php if ($Page->nomor_induk->Visible) { // nomor_induk ?>
        <th class="<?= $Page->nomor_induk->headerCellClass() ?>"><span id="elh_pengguna_nomor_induk" class="pengguna_nomor_induk"><?= $Page->nomor_induk->caption() ?></span></th>
<?php } ?>
<?php if ($Page->nama_pengguna->Visible) { // nama_pengguna ?>
        <th class="<?= $Page->nama_pengguna->headerCellClass() ?>"><span id="elh_pengguna_nama_pengguna" class="pengguna_nama_pengguna"><?= $Page->nama_pengguna->caption() ?></span></th>
<?php } ?>
<?php if ($Page->role->Visible) { // role ?>
        <th class="<?= $Page->role->headerCellClass() ?>"><span id="elh_pengguna_role" class="pengguna_role"><?= $Page->role->caption() ?></span></th>
<?php } ?>
<?php if ($Page->_username->Visible) { // username ?>
        <th class="<?= $Page->_username->headerCellClass() ?>"><span id="elh_pengguna__username" class="pengguna__username"><?= $Page->_username->caption() ?></span></th>
<?php } ?>
<?php if ($Page->_password->Visible) { // password ?>
        <th class="<?= $Page->_password->headerCellClass() ?>"><span id="elh_pengguna__password" class="pengguna__password"><?= $Page->_password->caption() ?></span></th>
<?php } ?>
    </tr>
    </thead>
    <tbody>
<?php
$Page->RecordCount = 0;
$i = 0;
while (!$Page->Recordset->EOF) {
    $Page->RecordCount++;
    $Page->RowCount++;

    // Set row properties
    $Page->resetAttributes();
    $Page->RowType = ROWTYPE_VIEW; // View

    // Get the field contents
    $Page->loadRowValues($Page->Recordset);

    // Render row
    $Page->renderRow();
?>
    <tr <?= $Page->rowAttributes() ?>>
<?php if ($Page->id_pengguna->Visible) { // id_pengguna ?>
        <td <?= $Page->id_pengguna->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_pengguna_id_pengguna" class="pengguna_id_pengguna">
<span<?= $Page->id_pengguna->viewAttributes() ?>>
<?= $Page->id_pengguna->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->nomor_induk->Visible) { // nomor_induk ?>
        <td <?= $Page->nomor_induk->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_pengguna_nomor_induk" class="pengguna_nomor_induk">
<span<?= $Page->nomor_induk->viewAttributes() ?>>
<?= $Page->nomor_induk->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->nama_pengguna->Visible) { // nama_pengguna ?>
        <td <?= $Page->nama_pengguna->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_pengguna_nama_pengguna" class="pengguna_nama_pengguna">
<span<?= $Page->nama_pengguna->viewAttributes() ?>>
<?= $Page->nama_pengguna->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->role->Visible) { // role ?>
        <td <?= $Page->role->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_pengguna_role" class="pengguna_role">
<span<?= $Page->role->viewAttributes() ?>>
<?= $Page->role->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->_username->Visible) { // username ?>
        <td <?= $Page->_username->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_pengguna__username" class="pengguna__username">
<span<?= $Page->_username->viewAttributes() ?>>
<?= $Page->_username->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->_password->Visible) { // password ?>
        <td <?= $Page->_password->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_pengguna__password" class="pengguna__password">
<span<?= $Page->_password->viewAttributes() ?>>
<?= $Page->_password->getViewValue() ?></span>
</span>
</td>
<?php } ?>
    </tr>
<?php
    $Page->Recordset->moveNext();
}
$Page->Recordset->close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ew-btn" name="btn-action" id="btn-action" type="submit"><?= $Language->phrase("DeleteBtn") ?></button>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" data-href="<?= HtmlEncode(GetUrl($Page->getReturnUrl())) ?>"><?= $Language->phrase("CancelBtn") ?></button>
</div>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
