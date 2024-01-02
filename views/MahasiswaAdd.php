<?php

namespace PHPMaker2021\project1;

// Page object
$MahasiswaAdd = &$Page;
?>
<script>
var currentForm, currentPageID;
var fmahasiswaadd;
loadjs.ready("head", function () {
    var $ = jQuery;
    // Form object
    currentPageID = ew.PAGE_ID = "add";
    fmahasiswaadd = currentForm = new ew.Form("fmahasiswaadd", "add");

    // Add fields
    var currentTable = <?= JsonEncode(GetClientVar("tables", "mahasiswa")) ?>,
        fields = currentTable.fields;
    if (!ew.vars.tables.mahasiswa)
        ew.vars.tables.mahasiswa = currentTable;
    fmahasiswaadd.addFields([
        ["nim", [fields.nim.visible && fields.nim.required ? ew.Validators.required(fields.nim.caption) : null], fields.nim.isInvalid],
        ["nama_mhs", [fields.nama_mhs.visible && fields.nama_mhs.required ? ew.Validators.required(fields.nama_mhs.caption) : null], fields.nama_mhs.isInvalid],
        ["jurusan", [fields.jurusan.visible && fields.jurusan.required ? ew.Validators.required(fields.jurusan.caption) : null], fields.jurusan.isInvalid]
    ]);

    // Set invalid fields
    $(function() {
        var f = fmahasiswaadd,
            fobj = f.getForm(),
            $fobj = $(fobj),
            $k = $fobj.find("#" + f.formKeyCountName), // Get key_count
            rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1,
            startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
        for (var i = startcnt; i <= rowcnt; i++) {
            var rowIndex = ($k[0]) ? String(i) : "";
            f.setInvalid(rowIndex);
        }
    });

    // Validate form
    fmahasiswaadd.validate = function () {
        if (!this.validateRequired)
            return true; // Ignore validation
        var fobj = this.getForm(),
            $fobj = $(fobj);
        if ($fobj.find("#confirm").val() == "confirm")
            return true;
        var addcnt = 0,
            $k = $fobj.find("#" + this.formKeyCountName), // Get key_count
            rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1,
            startcnt = (rowcnt == 0) ? 0 : 1, // Check rowcnt == 0 => Inline-Add
            gridinsert = ["insert", "gridinsert"].includes($fobj.find("#action").val()) && $k[0];
        for (var i = startcnt; i <= rowcnt; i++) {
            var rowIndex = ($k[0]) ? String(i) : "";
            $fobj.data("rowindex", rowIndex);

            // Validate fields
            if (!this.validateFields(rowIndex))
                return false;

            // Call Form_CustomValidate event
            if (!this.customValidate(fobj)) {
                this.focus();
                return false;
            }
        }

        // Process detail forms
        var dfs = $fobj.find("input[name='detailpage']").get();
        for (var i = 0; i < dfs.length; i++) {
            var df = dfs[i],
                val = df.value,
                frm = ew.forms.get(val);
            if (val && frm && !frm.validate())
                return false;
        }
        return true;
    }

    // Form_CustomValidate
    fmahasiswaadd.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fmahasiswaadd.validateRequired = <?= Config("CLIENT_VALIDATE") ? "true" : "false" ?>;

    // Dynamic selection lists
    loadjs.done("fmahasiswaadd");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<form name="fmahasiswaadd" id="fmahasiswaadd" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="mahasiswa">
<?php if ($Page->isConfirm()) { // Confirm page ?>
<input type="hidden" name="action" id="action" value="insert">
<input type="hidden" name="confirm" id="confirm" value="confirm">
<?php } else { ?>
<input type="hidden" name="action" id="action" value="confirm">
<?php } ?>
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-add-div"><!-- page* -->
<?php if ($Page->nim->Visible) { // nim ?>
    <div id="r_nim" class="form-group row">
        <label id="elh_mahasiswa_nim" for="x_nim" class="<?= $Page->LeftColumnClass ?>"><?= $Page->nim->caption() ?><?= $Page->nim->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->nim->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_mahasiswa_nim">
<input type="<?= $Page->nim->getInputTextType() ?>" data-table="mahasiswa" data-field="x_nim" name="x_nim" id="x_nim" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->nim->getPlaceHolder()) ?>" value="<?= $Page->nim->EditValue ?>"<?= $Page->nim->editAttributes() ?> aria-describedby="x_nim_help">
<?= $Page->nim->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->nim->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_mahasiswa_nim">
<span<?= $Page->nim->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->nim->getDisplayValue($Page->nim->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="mahasiswa" data-field="x_nim" data-hidden="1" name="x_nim" id="x_nim" value="<?= HtmlEncode($Page->nim->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->nama_mhs->Visible) { // nama_mhs ?>
    <div id="r_nama_mhs" class="form-group row">
        <label id="elh_mahasiswa_nama_mhs" for="x_nama_mhs" class="<?= $Page->LeftColumnClass ?>"><?= $Page->nama_mhs->caption() ?><?= $Page->nama_mhs->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->nama_mhs->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_mahasiswa_nama_mhs">
<input type="<?= $Page->nama_mhs->getInputTextType() ?>" data-table="mahasiswa" data-field="x_nama_mhs" name="x_nama_mhs" id="x_nama_mhs" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->nama_mhs->getPlaceHolder()) ?>" value="<?= $Page->nama_mhs->EditValue ?>"<?= $Page->nama_mhs->editAttributes() ?> aria-describedby="x_nama_mhs_help">
<?= $Page->nama_mhs->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->nama_mhs->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_mahasiswa_nama_mhs">
<span<?= $Page->nama_mhs->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->nama_mhs->getDisplayValue($Page->nama_mhs->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="mahasiswa" data-field="x_nama_mhs" data-hidden="1" name="x_nama_mhs" id="x_nama_mhs" value="<?= HtmlEncode($Page->nama_mhs->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->jurusan->Visible) { // jurusan ?>
    <div id="r_jurusan" class="form-group row">
        <label id="elh_mahasiswa_jurusan" for="x_jurusan" class="<?= $Page->LeftColumnClass ?>"><?= $Page->jurusan->caption() ?><?= $Page->jurusan->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->jurusan->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_mahasiswa_jurusan">
<input type="<?= $Page->jurusan->getInputTextType() ?>" data-table="mahasiswa" data-field="x_jurusan" name="x_jurusan" id="x_jurusan" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->jurusan->getPlaceHolder()) ?>" value="<?= $Page->jurusan->EditValue ?>"<?= $Page->jurusan->editAttributes() ?> aria-describedby="x_jurusan_help">
<?= $Page->jurusan->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->jurusan->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_mahasiswa_jurusan">
<span<?= $Page->jurusan->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->jurusan->getDisplayValue($Page->jurusan->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="mahasiswa" data-field="x_jurusan" data-hidden="1" name="x_jurusan" id="x_jurusan" value="<?= HtmlEncode($Page->jurusan->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
</div><!-- /page* -->
<?php if (!$Page->IsModal) { ?>
<div class="form-group row"><!-- buttons .form-group -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
<?php if (!$Page->isConfirm()) { // Confirm page ?>
<button class="btn btn-primary ew-btn" name="btn-action" id="btn-action" type="submit" onclick="this.form.action.value='confirm';"><?= $Language->phrase("AddBtn") ?></button>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" data-href="<?= HtmlEncode(GetUrl($Page->getReturnUrl())) ?>"><?= $Language->phrase("CancelBtn") ?></button>
<?php } else { ?>
<button class="btn btn-primary ew-btn" name="btn-action" id="btn-action" type="submit"><?= $Language->phrase("ConfirmBtn") ?></button>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="submit" onclick="this.form.action.value='cancel';"><?= $Language->phrase("CancelBtn") ?></button>
<?php } ?>
    </div><!-- /buttons offset -->
</div><!-- /buttons .form-group -->
<?php } ?>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<script>
// Field event handlers
loadjs.ready("head", function() {
    ew.addEventHandlers("mahasiswa");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
