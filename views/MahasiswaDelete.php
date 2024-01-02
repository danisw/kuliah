<?php

namespace PHPMaker2021\project1;

// Page object
$MahasiswaDelete = &$Page;
?>
<script>
var currentForm, currentPageID;
var fmahasiswadelete;
loadjs.ready("head", function () {
    var $ = jQuery;
    // Form object
    currentPageID = ew.PAGE_ID = "delete";
    fmahasiswadelete = currentForm = new ew.Form("fmahasiswadelete", "delete");
    loadjs.done("fmahasiswadelete");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<script>
if (!ew.vars.tables.mahasiswa) ew.vars.tables.mahasiswa = <?= JsonEncode(GetClientVar("tables", "mahasiswa")) ?>;
</script>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<form name="fmahasiswadelete" id="fmahasiswadelete" class="form-inline ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="mahasiswa">
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
<?php if ($Page->id_mhs->Visible) { // id_mhs ?>
        <th class="<?= $Page->id_mhs->headerCellClass() ?>"><span id="elh_mahasiswa_id_mhs" class="mahasiswa_id_mhs"><?= $Page->id_mhs->caption() ?></span></th>
<?php } ?>
<?php if ($Page->nim->Visible) { // nim ?>
        <th class="<?= $Page->nim->headerCellClass() ?>"><span id="elh_mahasiswa_nim" class="mahasiswa_nim"><?= $Page->nim->caption() ?></span></th>
<?php } ?>
<?php if ($Page->nama_mhs->Visible) { // nama_mhs ?>
        <th class="<?= $Page->nama_mhs->headerCellClass() ?>"><span id="elh_mahasiswa_nama_mhs" class="mahasiswa_nama_mhs"><?= $Page->nama_mhs->caption() ?></span></th>
<?php } ?>
<?php if ($Page->jurusan->Visible) { // jurusan ?>
        <th class="<?= $Page->jurusan->headerCellClass() ?>"><span id="elh_mahasiswa_jurusan" class="mahasiswa_jurusan"><?= $Page->jurusan->caption() ?></span></th>
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
<?php if ($Page->id_mhs->Visible) { // id_mhs ?>
        <td <?= $Page->id_mhs->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_mahasiswa_id_mhs" class="mahasiswa_id_mhs">
<span<?= $Page->id_mhs->viewAttributes() ?>>
<?= $Page->id_mhs->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->nim->Visible) { // nim ?>
        <td <?= $Page->nim->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_mahasiswa_nim" class="mahasiswa_nim">
<span<?= $Page->nim->viewAttributes() ?>>
<?= $Page->nim->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->nama_mhs->Visible) { // nama_mhs ?>
        <td <?= $Page->nama_mhs->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_mahasiswa_nama_mhs" class="mahasiswa_nama_mhs">
<span<?= $Page->nama_mhs->viewAttributes() ?>>
<?= $Page->nama_mhs->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->jurusan->Visible) { // jurusan ?>
        <td <?= $Page->jurusan->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_mahasiswa_jurusan" class="mahasiswa_jurusan">
<span<?= $Page->jurusan->viewAttributes() ?>>
<?= $Page->jurusan->getViewValue() ?></span>
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
