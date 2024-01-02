<?php

namespace PHPMaker2021\project1;

// Page object
$MataKuliahDelete = &$Page;
?>
<script>
var currentForm, currentPageID;
var fmata_kuliahdelete;
loadjs.ready("head", function () {
    var $ = jQuery;
    // Form object
    currentPageID = ew.PAGE_ID = "delete";
    fmata_kuliahdelete = currentForm = new ew.Form("fmata_kuliahdelete", "delete");
    loadjs.done("fmata_kuliahdelete");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<script>
if (!ew.vars.tables.mata_kuliah) ew.vars.tables.mata_kuliah = <?= JsonEncode(GetClientVar("tables", "mata_kuliah")) ?>;
</script>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<form name="fmata_kuliahdelete" id="fmata_kuliahdelete" class="form-inline ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="mata_kuliah">
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
<?php if ($Page->id_mk->Visible) { // id_mk ?>
        <th class="<?= $Page->id_mk->headerCellClass() ?>"><span id="elh_mata_kuliah_id_mk" class="mata_kuliah_id_mk"><?= $Page->id_mk->caption() ?></span></th>
<?php } ?>
<?php if ($Page->nama_mk->Visible) { // nama_mk ?>
        <th class="<?= $Page->nama_mk->headerCellClass() ?>"><span id="elh_mata_kuliah_nama_mk" class="mata_kuliah_nama_mk"><?= $Page->nama_mk->caption() ?></span></th>
<?php } ?>
<?php if ($Page->kurikulum->Visible) { // kurikulum ?>
        <th class="<?= $Page->kurikulum->headerCellClass() ?>"><span id="elh_mata_kuliah_kurikulum" class="mata_kuliah_kurikulum"><?= $Page->kurikulum->caption() ?></span></th>
<?php } ?>
<?php if ($Page->tahun_diambil->Visible) { // tahun_diambil ?>
        <th class="<?= $Page->tahun_diambil->headerCellClass() ?>"><span id="elh_mata_kuliah_tahun_diambil" class="mata_kuliah_tahun_diambil"><?= $Page->tahun_diambil->caption() ?></span></th>
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
<?php if ($Page->id_mk->Visible) { // id_mk ?>
        <td <?= $Page->id_mk->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_mata_kuliah_id_mk" class="mata_kuliah_id_mk">
<span<?= $Page->id_mk->viewAttributes() ?>>
<?= $Page->id_mk->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->nama_mk->Visible) { // nama_mk ?>
        <td <?= $Page->nama_mk->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_mata_kuliah_nama_mk" class="mata_kuliah_nama_mk">
<span<?= $Page->nama_mk->viewAttributes() ?>>
<?= $Page->nama_mk->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->kurikulum->Visible) { // kurikulum ?>
        <td <?= $Page->kurikulum->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_mata_kuliah_kurikulum" class="mata_kuliah_kurikulum">
<span<?= $Page->kurikulum->viewAttributes() ?>>
<?= $Page->kurikulum->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->tahun_diambil->Visible) { // tahun_diambil ?>
        <td <?= $Page->tahun_diambil->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_mata_kuliah_tahun_diambil" class="mata_kuliah_tahun_diambil">
<span<?= $Page->tahun_diambil->viewAttributes() ?>>
<?= $Page->tahun_diambil->getViewValue() ?></span>
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
