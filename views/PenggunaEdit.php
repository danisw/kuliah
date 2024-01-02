<?php

namespace PHPMaker2021\project1;

// Page object
$PenggunaEdit = &$Page;
?>
<script>
var currentForm, currentPageID;
var fpenggunaedit;
loadjs.ready("head", function () {
    var $ = jQuery;
    // Form object
    currentPageID = ew.PAGE_ID = "edit";
    fpenggunaedit = currentForm = new ew.Form("fpenggunaedit", "edit");

    // Add fields
    var currentTable = <?= JsonEncode(GetClientVar("tables", "pengguna")) ?>,
        fields = currentTable.fields;
    if (!ew.vars.tables.pengguna)
        ew.vars.tables.pengguna = currentTable;
    fpenggunaedit.addFields([
        ["id_pengguna", [fields.id_pengguna.visible && fields.id_pengguna.required ? ew.Validators.required(fields.id_pengguna.caption) : null], fields.id_pengguna.isInvalid],
        ["nomor_induk", [fields.nomor_induk.visible && fields.nomor_induk.required ? ew.Validators.required(fields.nomor_induk.caption) : null], fields.nomor_induk.isInvalid],
        ["nama_pengguna", [fields.nama_pengguna.visible && fields.nama_pengguna.required ? ew.Validators.required(fields.nama_pengguna.caption) : null], fields.nama_pengguna.isInvalid],
        ["role", [fields.role.visible && fields.role.required ? ew.Validators.required(fields.role.caption) : null], fields.role.isInvalid],
        ["_username", [fields._username.visible && fields._username.required ? ew.Validators.required(fields._username.caption) : null], fields._username.isInvalid],
        ["_password", [fields._password.visible && fields._password.required ? ew.Validators.required(fields._password.caption) : null], fields._password.isInvalid]
    ]);

    // Set invalid fields
    $(function() {
        var f = fpenggunaedit,
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
    fpenggunaedit.validate = function () {
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
    fpenggunaedit.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fpenggunaedit.validateRequired = <?= Config("CLIENT_VALIDATE") ? "true" : "false" ?>;

    // Dynamic selection lists
    fpenggunaedit.lists.role = <?= $Page->role->toClientList($Page) ?>;
    loadjs.done("fpenggunaedit");
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
<form name="fpenggunaedit" id="fpenggunaedit" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="pengguna">
<?php if ($Page->isConfirm()) { // Confirm page ?>
<input type="hidden" name="action" id="action" value="update">
<input type="hidden" name="confirm" id="confirm" value="confirm">
<?php } else { ?>
<input type="hidden" name="action" id="action" value="confirm">
<?php } ?>
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-edit-div"><!-- page* -->
<?php if ($Page->id_pengguna->Visible) { // id_pengguna ?>
    <div id="r_id_pengguna" class="form-group row">
        <label id="elh_pengguna_id_pengguna" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_pengguna->caption() ?><?= $Page->id_pengguna->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->id_pengguna->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_pengguna_id_pengguna">
<span<?= $Page->id_pengguna->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_pengguna->getDisplayValue($Page->id_pengguna->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="pengguna" data-field="x_id_pengguna" data-hidden="1" name="x_id_pengguna" id="x_id_pengguna" value="<?= HtmlEncode($Page->id_pengguna->CurrentValue) ?>">
<?php } else { ?>
<span id="el_pengguna_id_pengguna">
<span<?= $Page->id_pengguna->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_pengguna->getDisplayValue($Page->id_pengguna->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="pengguna" data-field="x_id_pengguna" data-hidden="1" name="x_id_pengguna" id="x_id_pengguna" value="<?= HtmlEncode($Page->id_pengguna->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->nomor_induk->Visible) { // nomor_induk ?>
    <div id="r_nomor_induk" class="form-group row">
        <label id="elh_pengguna_nomor_induk" for="x_nomor_induk" class="<?= $Page->LeftColumnClass ?>"><?= $Page->nomor_induk->caption() ?><?= $Page->nomor_induk->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->nomor_induk->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_pengguna_nomor_induk">
<input type="<?= $Page->nomor_induk->getInputTextType() ?>" data-table="pengguna" data-field="x_nomor_induk" name="x_nomor_induk" id="x_nomor_induk" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->nomor_induk->getPlaceHolder()) ?>" value="<?= $Page->nomor_induk->EditValue ?>"<?= $Page->nomor_induk->editAttributes() ?> aria-describedby="x_nomor_induk_help">
<?= $Page->nomor_induk->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->nomor_induk->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_pengguna_nomor_induk">
<span<?= $Page->nomor_induk->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->nomor_induk->getDisplayValue($Page->nomor_induk->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="pengguna" data-field="x_nomor_induk" data-hidden="1" name="x_nomor_induk" id="x_nomor_induk" value="<?= HtmlEncode($Page->nomor_induk->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->nama_pengguna->Visible) { // nama_pengguna ?>
    <div id="r_nama_pengguna" class="form-group row">
        <label id="elh_pengguna_nama_pengguna" for="x_nama_pengguna" class="<?= $Page->LeftColumnClass ?>"><?= $Page->nama_pengguna->caption() ?><?= $Page->nama_pengguna->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->nama_pengguna->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_pengguna_nama_pengguna">
<input type="<?= $Page->nama_pengguna->getInputTextType() ?>" data-table="pengguna" data-field="x_nama_pengguna" name="x_nama_pengguna" id="x_nama_pengguna" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->nama_pengguna->getPlaceHolder()) ?>" value="<?= $Page->nama_pengguna->EditValue ?>"<?= $Page->nama_pengguna->editAttributes() ?> aria-describedby="x_nama_pengguna_help">
<?= $Page->nama_pengguna->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->nama_pengguna->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_pengguna_nama_pengguna">
<span<?= $Page->nama_pengguna->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->nama_pengguna->getDisplayValue($Page->nama_pengguna->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="pengguna" data-field="x_nama_pengguna" data-hidden="1" name="x_nama_pengguna" id="x_nama_pengguna" value="<?= HtmlEncode($Page->nama_pengguna->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->role->Visible) { // role ?>
    <div id="r_role" class="form-group row">
        <label id="elh_pengguna_role" for="x_role" class="<?= $Page->LeftColumnClass ?>"><?= $Page->role->caption() ?><?= $Page->role->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->role->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<?php if (!$Security->isAdmin() && $Security->isLoggedIn()) { // Non system admin ?>
<span id="el_pengguna_role">
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->role->getDisplayValue($Page->role->EditValue))) ?>">
</span>
<?php } else { ?>
<span id="el_pengguna_role">
    <select
        id="x_role"
        name="x_role"
        class="form-control ew-select<?= $Page->role->isInvalidClass() ?>"
        data-select2-id="pengguna_x_role"
        data-table="pengguna"
        data-field="x_role"
        data-value-separator="<?= $Page->role->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->role->getPlaceHolder()) ?>"
        <?= $Page->role->editAttributes() ?>>
        <?= $Page->role->selectOptionListHtml("x_role") ?>
    </select>
    <?= $Page->role->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->role->getErrorMessage() ?></div>
<script>
loadjs.ready("head", function() {
    var el = document.querySelector("select[data-select2-id='pengguna_x_role']"),
        options = { name: "x_role", selectId: "pengguna_x_role", language: ew.LANGUAGE_ID, dir: ew.IS_RTL ? "rtl" : "ltr" };
    options.data = ew.vars.tables.pengguna.fields.role.lookupOptions;
    options.dropdownParent = $(el).closest("#ew-modal-dialog, #ew-add-opt-dialog")[0];
    Object.assign(options, ew.vars.tables.pengguna.fields.role.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php } else { ?>
<span id="el_pengguna_role">
<span<?= $Page->role->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->role->getDisplayValue($Page->role->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="pengguna" data-field="x_role" data-hidden="1" name="x_role" id="x_role" value="<?= HtmlEncode($Page->role->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->_username->Visible) { // username ?>
    <div id="r__username" class="form-group row">
        <label id="elh_pengguna__username" for="x__username" class="<?= $Page->LeftColumnClass ?>"><?= $Page->_username->caption() ?><?= $Page->_username->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->_username->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_pengguna__username">
<input type="<?= $Page->_username->getInputTextType() ?>" data-table="pengguna" data-field="x__username" name="x__username" id="x__username" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->_username->getPlaceHolder()) ?>" value="<?= $Page->_username->EditValue ?>"<?= $Page->_username->editAttributes() ?> aria-describedby="x__username_help">
<?= $Page->_username->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->_username->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_pengguna__username">
<span<?= $Page->_username->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->_username->getDisplayValue($Page->_username->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="pengguna" data-field="x__username" data-hidden="1" name="x__username" id="x__username" value="<?= HtmlEncode($Page->_username->FormValue) ?>">
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->_password->Visible) { // password ?>
    <div id="r__password" class="form-group row">
        <label id="elh_pengguna__password" for="x__password" class="<?= $Page->LeftColumnClass ?>"><?= $Page->_password->caption() ?><?= $Page->_password->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div <?= $Page->_password->cellAttributes() ?>>
<?php if (!$Page->isConfirm()) { ?>
<span id="el_pengguna__password">
<input type="<?= $Page->_password->getInputTextType() ?>" data-table="pengguna" data-field="x__password" name="x__password" id="x__password" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->_password->getPlaceHolder()) ?>" value="<?= $Page->_password->EditValue ?>"<?= $Page->_password->editAttributes() ?> aria-describedby="x__password_help">
<?= $Page->_password->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->_password->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el_pengguna__password">
<span<?= $Page->_password->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->_password->getDisplayValue($Page->_password->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="pengguna" data-field="x__password" data-hidden="1" name="x__password" id="x__password" value="<?= HtmlEncode($Page->_password->FormValue) ?>">
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
    ew.addEventHandlers("pengguna");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
