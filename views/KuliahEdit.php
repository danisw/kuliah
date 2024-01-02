<?php

namespace PHPMaker2021\project1;

// Page object
$KuliahEdit = &$Page;
?>
<script>
var currentForm, currentPageID;
var fkuliahedit;
loadjs.ready("head", function () {
    var $ = jQuery;
    // Form object
    currentPageID = ew.PAGE_ID = "edit";
    fkuliahedit = currentForm = new ew.Form("fkuliahedit", "edit");

    // Add fields
    var currentTable = <?= JsonEncode(GetClientVar("tables", "kuliah")) ?>,
        fields = currentTable.fields;
    if (!ew.vars.tables.kuliah)
        ew.vars.tables.kuliah = currentTable;
    fkuliahedit.addFields([
        ["id_kuliah", [fields.id_kuliah.visible && fields.id_kuliah.required ? ew.Validators.required(fields.id_kuliah.caption) : null], fields.id_kuliah.isInvalid],
        ["id_mk", [fields.id_mk.visible && fields.id_mk.required ? ew.Validators.required(fields.id_mk.caption) : null, ew.Validators.integer], fields.id_mk.isInvalid],
        ["id_mhs", [fields.id_mhs.visible && fields.id_mhs.required ? ew.Validators.required(fields.id_mhs.caption) : null, ew.Validators.integer], fields.id_mhs.isInvalid],
        ["tahun_ajaran", [fields.tahun_ajaran.visible && fields.tahun_ajaran.required ? ew.Validators.required(fields.tahun_ajaran.caption) : null], fields.tahun_ajaran.isInvalid]
    ]);

    // Set invalid fields
    $(function() {
        var f = fkuliahedit,
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
    fkuliahedit.validate = function () {
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
    fkuliahedit.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fkuliahedit.validateRequired = <?= Config("CLIENT_VALIDATE") ? "true" : "false" ?>;

    // Dynamic selection lists
    fkuliahedit.lists.id_mk = <?= $Page->id_mk->toClientList($Page) ?>;
    fkuliahedit.lists.id_mhs = <?= $Page->id_mhs->toClientList($Page) ?>;
    loadjs.done("fkuliahedit");
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
<form name="fkuliahedit" id="fkuliahedit" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="kuliah">
<?php if ($Page->isConfirm()) { // Confirm page ?>
<input type="hidden" name="action" id="action" value="update">
<input type="hidden" name="confirm" id="confirm" value="confirm">
<?php } else { ?>
<input type="hidden" name="action" id="action" value="confirm">
<?php } ?>
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<?php if ($Page->getCurrentMasterTable() == "v_mhs") { ?>
<input type="hidden" name="<?= Config("TABLE_SHOW_MASTER") ?>" value="v_mhs">
<input type="hidden" name="fk_id_mhs" value="<?= HtmlEncode($Page->id_mhs->getSessionValue()) ?>">
<?php } ?>
<div class="ew-edit-div"><!-- page* -->
<?php if ($Page->id_kuliah->Visible) { // id_kuliah ?>
    <div id="r_id_kuliah" class="form-group row">
        <label id="elh_kuliah_id_kuliah" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_kuliah->caption() ?><?= $Page->id_kuliah->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->id_kuliah->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_kuliah_id_kuliah">
<span<?= $Page->id_kuliah->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_kuliah->getDisplayValue($Page->id_kuliah->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="kuliah" data-field="x_id_kuliah" data-hidden="1" name="x_id_kuliah" id="x_id_kuliah" value="<?= HtmlEncode($Page->id_kuliah->CurrentValue) ?>">
<?php } else { ?>
<span id="el_kuliah_id_kuliah">
<span<?= $Page->id_kuliah->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_kuliah->getDisplayValue($Page->id_kuliah->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="kuliah" data-field="x_id_kuliah" data-hidden="1" name="x_id_kuliah" id="x_id_kuliah" value="<?= HtmlEncode($Page->id_kuliah->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->id_mk->Visible) { // id_mk ?>
    <div id="r_id_mk" class="form-group row">
        <label id="elh_kuliah_id_mk" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_mk->caption() ?><?= $Page->id_mk->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->id_mk->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_kuliah_id_mk">
<?php
$onchange = $Page->id_mk->EditAttrs->prepend("onchange", "");
$onchange = ($onchange) ? ' onchange="' . JsEncode($onchange) . '"' : '';
$Page->id_mk->EditAttrs["onchange"] = "";
?>
<span id="as_x_id_mk" class="ew-auto-suggest">
    <div class="input-group flex-nowrap">
        <input type="<?= $Page->id_mk->getInputTextType() ?>" class="form-control" name="sv_x_id_mk" id="sv_x_id_mk" value="<?= RemoveHtml($Page->id_mk->EditValue) ?>" size="30" placeholder="<?= HtmlEncode($Page->id_mk->getPlaceHolder()) ?>" data-placeholder="<?= HtmlEncode($Page->id_mk->getPlaceHolder()) ?>"<?= $Page->id_mk->editAttributes() ?> aria-describedby="x_id_mk_help">
        <div class="input-group-append">
            <button type="button" title="<?= HtmlEncode(str_replace("%s", RemoveHtml($Page->id_mk->caption()), $Language->phrase("LookupLink", true))) ?>" onclick="ew.modalLookupShow({lnk:this,el:'x_id_mk',m:0,n:10,srch:true});" class="ew-lookup-btn btn btn-default"<?= ($Page->id_mk->ReadOnly || $Page->id_mk->Disabled) ? " disabled" : "" ?>><i class="fas fa-search ew-icon"></i></button>
        </div>
    </div>
</span>
<input type="hidden" is="selection-list" class="form-control" data-table="kuliah" data-field="x_id_mk" data-input="sv_x_id_mk" data-type="text" data-multiple="0" data-lookup="1" data-value-separator="<?= $Page->id_mk->displayValueSeparatorAttribute() ?>" name="x_id_mk" id="x_id_mk" value="<?= HtmlEncode($Page->id_mk->CurrentValue) ?>"<?= $onchange ?>>
<?= $Page->id_mk->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->id_mk->getErrorMessage() ?></div>
<script>
loadjs.ready(["fkuliahedit"], function() {
    fkuliahedit.createAutoSuggest(Object.assign({"id":"x_id_mk","forceSelect":false}, ew.vars.tables.kuliah.fields.id_mk.autoSuggestOptions));
});
</script>
<?= $Page->id_mk->Lookup->getParamTag($Page, "p_x_id_mk") ?>
</span>
<?php } else { ?>
<span id="el_kuliah_id_mk">
<span<?= $Page->id_mk->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_mk->getDisplayValue($Page->id_mk->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="kuliah" data-field="x_id_mk" data-hidden="1" name="x_id_mk" id="x_id_mk" value="<?= HtmlEncode($Page->id_mk->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->id_mhs->Visible) { // id_mhs ?>
    <div id="r_id_mhs" class="form-group row">
        <label id="elh_kuliah_id_mhs" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_mhs->caption() ?><?= $Page->id_mhs->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->id_mhs->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<?php if ($Page->id_mhs->getSessionValue() != "") { ?>
<span id="el_kuliah_id_mhs">
<span<?= $Page->id_mhs->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_mhs->getDisplayValue($Page->id_mhs->ViewValue))) ?>"></span>
</span>
<input type="hidden" id="x_id_mhs" name="x_id_mhs" value="<?= HtmlEncode($Page->id_mhs->CurrentValue) ?>" data-hidden="1">
<?php } else { ?>
<span id="el_kuliah_id_mhs">
<?php
$onchange = $Page->id_mhs->EditAttrs->prepend("onchange", "");
$onchange = ($onchange) ? ' onchange="' . JsEncode($onchange) . '"' : '';
$Page->id_mhs->EditAttrs["onchange"] = "";
?>
<span id="as_x_id_mhs" class="ew-auto-suggest">
    <div class="input-group flex-nowrap">
        <input type="<?= $Page->id_mhs->getInputTextType() ?>" class="form-control" name="sv_x_id_mhs" id="sv_x_id_mhs" value="<?= RemoveHtml($Page->id_mhs->EditValue) ?>" size="30" placeholder="<?= HtmlEncode($Page->id_mhs->getPlaceHolder()) ?>" data-placeholder="<?= HtmlEncode($Page->id_mhs->getPlaceHolder()) ?>"<?= $Page->id_mhs->editAttributes() ?> aria-describedby="x_id_mhs_help">
        <div class="input-group-append">
            <button type="button" title="<?= HtmlEncode(str_replace("%s", RemoveHtml($Page->id_mhs->caption()), $Language->phrase("LookupLink", true))) ?>" onclick="ew.modalLookupShow({lnk:this,el:'x_id_mhs',m:0,n:10,srch:true});" class="ew-lookup-btn btn btn-default"<?= ($Page->id_mhs->ReadOnly || $Page->id_mhs->Disabled) ? " disabled" : "" ?>><i class="fas fa-search ew-icon"></i></button>
        </div>
    </div>
</span>
<input type="hidden" is="selection-list" class="form-control" data-table="kuliah" data-field="x_id_mhs" data-input="sv_x_id_mhs" data-type="text" data-multiple="0" data-lookup="1" data-value-separator="<?= $Page->id_mhs->displayValueSeparatorAttribute() ?>" name="x_id_mhs" id="x_id_mhs" value="<?= HtmlEncode($Page->id_mhs->CurrentValue) ?>"<?= $onchange ?>>
<?= $Page->id_mhs->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->id_mhs->getErrorMessage() ?></div>
<script>
loadjs.ready(["fkuliahedit"], function() {
    fkuliahedit.createAutoSuggest(Object.assign({"id":"x_id_mhs","forceSelect":false}, ew.vars.tables.kuliah.fields.id_mhs.autoSuggestOptions));
});
</script>
<?= $Page->id_mhs->Lookup->getParamTag($Page, "p_x_id_mhs") ?>
</span>
<?php } ?>
<?php } else { ?>
<span id="el_kuliah_id_mhs">
<span<?= $Page->id_mhs->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_mhs->getDisplayValue($Page->id_mhs->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="kuliah" data-field="x_id_mhs" data-hidden="1" name="x_id_mhs" id="x_id_mhs" value="<?= HtmlEncode($Page->id_mhs->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->tahun_ajaran->Visible) { // tahun_ajaran ?>
    <div id="r_tahun_ajaran" class="form-group row">
        <label id="elh_kuliah_tahun_ajaran" for="x_tahun_ajaran" class="<?= $Page->LeftColumnClass ?>"><?= $Page->tahun_ajaran->caption() ?><?= $Page->tahun_ajaran->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->tahun_ajaran->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_kuliah_tahun_ajaran">
<input type="<?= $Page->tahun_ajaran->getInputTextType() ?>" data-table="kuliah" data-field="x_tahun_ajaran" name="x_tahun_ajaran" id="x_tahun_ajaran" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->tahun_ajaran->getPlaceHolder()) ?>" value="<?= $Page->tahun_ajaran->EditValue ?>"<?= $Page->tahun_ajaran->editAttributes() ?> aria-describedby="x_tahun_ajaran_help">
<?= $Page->tahun_ajaran->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->tahun_ajaran->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_kuliah_tahun_ajaran">
<span<?= $Page->tahun_ajaran->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->tahun_ajaran->getDisplayValue($Page->tahun_ajaran->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="kuliah" data-field="x_tahun_ajaran" data-hidden="1" name="x_tahun_ajaran" id="x_tahun_ajaran" value="<?= HtmlEncode($Page->tahun_ajaran->FormValue) ?>">
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
    ew.addEventHandlers("kuliah");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
