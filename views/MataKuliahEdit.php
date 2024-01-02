<?php

namespace PHPMaker2021\project1;

// Page object
$MataKuliahEdit = &$Page;
?>
<script>
var currentForm, currentPageID;
var fmata_kuliahedit;
loadjs.ready("head", function () {
    var $ = jQuery;
    // Form object
    currentPageID = ew.PAGE_ID = "edit";
    fmata_kuliahedit = currentForm = new ew.Form("fmata_kuliahedit", "edit");

    // Add fields
    var currentTable = <?= JsonEncode(GetClientVar("tables", "mata_kuliah")) ?>,
        fields = currentTable.fields;
    if (!ew.vars.tables.mata_kuliah)
        ew.vars.tables.mata_kuliah = currentTable;
    fmata_kuliahedit.addFields([
        ["id_mk", [fields.id_mk.visible && fields.id_mk.required ? ew.Validators.required(fields.id_mk.caption) : null], fields.id_mk.isInvalid],
        ["nama_mk", [fields.nama_mk.visible && fields.nama_mk.required ? ew.Validators.required(fields.nama_mk.caption) : null], fields.nama_mk.isInvalid],
        ["kurikulum", [fields.kurikulum.visible && fields.kurikulum.required ? ew.Validators.required(fields.kurikulum.caption) : null], fields.kurikulum.isInvalid],
        ["tahun_diambil", [fields.tahun_diambil.visible && fields.tahun_diambil.required ? ew.Validators.required(fields.tahun_diambil.caption) : null, ew.Validators.integer], fields.tahun_diambil.isInvalid]
    ]);

    // Set invalid fields
    $(function() {
        var f = fmata_kuliahedit,
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
    fmata_kuliahedit.validate = function () {
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
    fmata_kuliahedit.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fmata_kuliahedit.validateRequired = <?= Config("CLIENT_VALIDATE") ? "true" : "false" ?>;

    // Dynamic selection lists
    loadjs.done("fmata_kuliahedit");
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
<form name="fmata_kuliahedit" id="fmata_kuliahedit" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="mata_kuliah">
<?php if ($Page->isConfirm()) { // Confirm page ?>
<input type="hidden" name="action" id="action" value="update">
<input type="hidden" name="confirm" id="confirm" value="confirm">
<?php } else { ?>
<input type="hidden" name="action" id="action" value="confirm">
<?php } ?>
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-edit-div"><!-- page* -->
<?php if ($Page->id_mk->Visible) { // id_mk ?>
    <div id="r_id_mk" class="form-group row">
        <label id="elh_mata_kuliah_id_mk" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_mk->caption() ?><?= $Page->id_mk->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->id_mk->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_mata_kuliah_id_mk">
<span<?= $Page->id_mk->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_mk->getDisplayValue($Page->id_mk->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="mata_kuliah" data-field="x_id_mk" data-hidden="1" name="x_id_mk" id="x_id_mk" value="<?= HtmlEncode($Page->id_mk->CurrentValue) ?>">
<?php } else { ?>
<span id="el_mata_kuliah_id_mk">
<span<?= $Page->id_mk->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_mk->getDisplayValue($Page->id_mk->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="mata_kuliah" data-field="x_id_mk" data-hidden="1" name="x_id_mk" id="x_id_mk" value="<?= HtmlEncode($Page->id_mk->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->nama_mk->Visible) { // nama_mk ?>
    <div id="r_nama_mk" class="form-group row">
        <label id="elh_mata_kuliah_nama_mk" for="x_nama_mk" class="<?= $Page->LeftColumnClass ?>"><?= $Page->nama_mk->caption() ?><?= $Page->nama_mk->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->nama_mk->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_mata_kuliah_nama_mk">
<input type="<?= $Page->nama_mk->getInputTextType() ?>" data-table="mata_kuliah" data-field="x_nama_mk" name="x_nama_mk" id="x_nama_mk" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->nama_mk->getPlaceHolder()) ?>" value="<?= $Page->nama_mk->EditValue ?>"<?= $Page->nama_mk->editAttributes() ?> aria-describedby="x_nama_mk_help">
<?= $Page->nama_mk->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->nama_mk->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_mata_kuliah_nama_mk">
<span<?= $Page->nama_mk->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->nama_mk->getDisplayValue($Page->nama_mk->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="mata_kuliah" data-field="x_nama_mk" data-hidden="1" name="x_nama_mk" id="x_nama_mk" value="<?= HtmlEncode($Page->nama_mk->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->kurikulum->Visible) { // kurikulum ?>
    <div id="r_kurikulum" class="form-group row">
        <label id="elh_mata_kuliah_kurikulum" for="x_kurikulum" class="<?= $Page->LeftColumnClass ?>"><?= $Page->kurikulum->caption() ?><?= $Page->kurikulum->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->kurikulum->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_mata_kuliah_kurikulum">
<input type="<?= $Page->kurikulum->getInputTextType() ?>" data-table="mata_kuliah" data-field="x_kurikulum" name="x_kurikulum" id="x_kurikulum" size="30" maxlength="7" placeholder="<?= HtmlEncode($Page->kurikulum->getPlaceHolder()) ?>" value="<?= $Page->kurikulum->EditValue ?>"<?= $Page->kurikulum->editAttributes() ?> aria-describedby="x_kurikulum_help">
<?= $Page->kurikulum->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->kurikulum->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_mata_kuliah_kurikulum">
<span<?= $Page->kurikulum->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->kurikulum->getDisplayValue($Page->kurikulum->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="mata_kuliah" data-field="x_kurikulum" data-hidden="1" name="x_kurikulum" id="x_kurikulum" value="<?= HtmlEncode($Page->kurikulum->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->tahun_diambil->Visible) { // tahun_diambil ?>
    <div id="r_tahun_diambil" class="form-group row">
        <label id="elh_mata_kuliah_tahun_diambil" for="x_tahun_diambil" class="<?= $Page->LeftColumnClass ?>"><?= $Page->tahun_diambil->caption() ?><?= $Page->tahun_diambil->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->tahun_diambil->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_mata_kuliah_tahun_diambil">
<input type="<?= $Page->tahun_diambil->getInputTextType() ?>" data-table="mata_kuliah" data-field="x_tahun_diambil" name="x_tahun_diambil" id="x_tahun_diambil" size="30" placeholder="<?= HtmlEncode($Page->tahun_diambil->getPlaceHolder()) ?>" value="<?= $Page->tahun_diambil->EditValue ?>"<?= $Page->tahun_diambil->editAttributes() ?> aria-describedby="x_tahun_diambil_help">
<?= $Page->tahun_diambil->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->tahun_diambil->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_mata_kuliah_tahun_diambil">
<span<?= $Page->tahun_diambil->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->tahun_diambil->getDisplayValue($Page->tahun_diambil->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="mata_kuliah" data-field="x_tahun_diambil" data-hidden="1" name="x_tahun_diambil" id="x_tahun_diambil" value="<?= HtmlEncode($Page->tahun_diambil->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
</div><!-- /page* -->
<?php if (!$Page->IsModal) { ?>
<div class="form-group row"><!-- buttons .form-group -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
<?php if (!$Page->isConfirm()) { // Confirm page ?>
<button class="btn btn-primary ew-btn" name="btn-action" id="btn-action" type="submit" onclick="this.form.action.value='confirm';"><?= $Language->phrase("SaveBtn") ?></button>
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
    ew.addEventHandlers("mata_kuliah");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
