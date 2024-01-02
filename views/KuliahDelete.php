<?php

namespace PHPMaker2021\project1;

// Page object
$KuliahDelete = &$Page;
?>
<script>
var currentForm, currentPageID;
var fkuliahdelete;
loadjs.ready("head", function () {
    var $ = jQuery;
    // Form object
    currentPageID = ew.PAGE_ID = "delete";
    fkuliahdelete = currentForm = new ew.Form("fkuliahdelete", "delete");
    loadjs.done("fkuliahdelete");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<script>
if (!ew.vars.tables.kuliah) ew.vars.tables.kuliah = <?= JsonEncode(GetClientVar("tables", "kuliah")) ?>;
</script>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<form name="fkuliahdelete" id="fkuliahdelete" class="form-inline ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="kuliah">
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
<?php if ($Page->id_kuliah->Visible) { // id_kuliah ?>
        <th class="<?= $Page->id_kuliah->headerCellClass() ?>"><span id="elh_kuliah_id_kuliah" class="kuliah_id_kuliah"><?= $Page->id_kuliah->caption() ?></span></th>
<?php } ?>
<?php if ($Page->id_mk->Visible) { // id_mk ?>
        <th class="<?= $Page->id_mk->headerCellClass() ?>"><span id="elh_kuliah_id_mk" class="kuliah_id_mk"><?= $Page->id_mk->caption() ?></span></th>
<?php } ?>
<?php if ($Page->tahun_ajaran->Visible) { // tahun_ajaran ?>
        <th class="<?= $Page->tahun_ajaran->headerCellClass() ?>"><span id="elh_kuliah_tahun_ajaran" class="kuliah_tahun_ajaran"><?= $Page->tahun_ajaran->caption() ?></span></th>
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
<?php if ($Page->id_kuliah->Visible) { // id_kuliah ?>
        <td <?= $Page->id_kuliah->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_kuliah_id_kuliah" class="kuliah_id_kuliah">
<span<?= $Page->id_kuliah->viewAttributes() ?>>
<?= $Page->id_kuliah->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->id_mk->Visible) { // id_mk ?>
        <td <?= $Page->id_mk->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_kuliah_id_mk" class="kuliah_id_mk">
<span<?= $Page->id_mk->viewAttributes() ?>>
<?= $Page->id_mk->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->tahun_ajaran->Visible) { // tahun_ajaran ?>
        <td <?= $Page->tahun_ajaran->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_kuliah_tahun_ajaran" class="kuliah_tahun_ajaran">
<span<?= $Page->tahun_ajaran->viewAttributes() ?>>
<?= $Page->tahun_ajaran->getViewValue() ?></span>
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
