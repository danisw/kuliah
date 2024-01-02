<?php

namespace PHPMaker2021\project1;

// Set up and run Grid object
$Grid = Container("KuliahGrid");
$Grid->run();
?>
<?php if (!$Grid->isExport()) { ?>
<script>
var currentForm, currentPageID;
var fkuliahgrid;
loadjs.ready("head", function () {
    var $ = jQuery;
    // Form object
    fkuliahgrid = new ew.Form("fkuliahgrid", "grid");
    fkuliahgrid.formKeyCountName = '<?= $Grid->FormKeyCountName ?>';

    // Add fields
    var currentTable = <?= JsonEncode(GetClientVar("tables", "kuliah")) ?>,
        fields = currentTable.fields;
    if (!ew.vars.tables.kuliah)
        ew.vars.tables.kuliah = currentTable;
    fkuliahgrid.addFields([
        ["id_kuliah", [fields.id_kuliah.visible && fields.id_kuliah.required ? ew.Validators.required(fields.id_kuliah.caption) : null], fields.id_kuliah.isInvalid],
        ["id_mk", [fields.id_mk.visible && fields.id_mk.required ? ew.Validators.required(fields.id_mk.caption) : null, ew.Validators.integer], fields.id_mk.isInvalid],
        ["tahun_ajaran", [fields.tahun_ajaran.visible && fields.tahun_ajaran.required ? ew.Validators.required(fields.tahun_ajaran.caption) : null], fields.tahun_ajaran.isInvalid]
    ]);

    // Set invalid fields
    $(function() {
        var f = fkuliahgrid,
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
    fkuliahgrid.validate = function () {
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
            var checkrow = (gridinsert) ? !this.emptyRow(rowIndex) : true;
            if (checkrow) {
                addcnt++;

            // Validate fields
            if (!this.validateFields(rowIndex))
                return false;

            // Call Form_CustomValidate event
            if (!this.customValidate(fobj)) {
                this.focus();
                return false;
            }
            } // End Grid Add checking
        }
        return true;
    }

    // Check empty row
    fkuliahgrid.emptyRow = function (rowIndex) {
        var fobj = this.getForm();
        if (ew.valueChanged(fobj, rowIndex, "id_mk", false))
            return false;
        if (ew.valueChanged(fobj, rowIndex, "tahun_ajaran", false))
            return false;
        return true;
    }

    // Form_CustomValidate
    fkuliahgrid.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fkuliahgrid.validateRequired = <?= Config("CLIENT_VALIDATE") ? "true" : "false" ?>;

    // Dynamic selection lists
    fkuliahgrid.lists.id_mk = <?= $Grid->id_mk->toClientList($Grid) ?>;
    loadjs.done("fkuliahgrid");
});
</script>
<?php } ?>
<?php
$Grid->renderOtherOptions();
?>
<?php if ($Grid->TotalRecords > 0 || $Grid->CurrentAction) { ?>
<div class="card ew-card ew-grid<?php if ($Grid->isAddOrEdit()) { ?> ew-grid-add-edit<?php } ?> kuliah">
<div id="fkuliahgrid" class="ew-form ew-list-form form-inline">
<div id="gmp_kuliah" class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<table id="tbl_kuliahgrid" class="table ew-table"><!-- .ew-table -->
<thead>
    <tr class="ew-table-header">
<?php
// Header row
$Grid->RowType = ROWTYPE_HEADER;

// Render list options
$Grid->renderListOptions();

// Render list options (header, left)
$Grid->ListOptions->render("header", "left");
?>
<?php if ($Grid->id_kuliah->Visible) { // id_kuliah ?>
        <th data-name="id_kuliah" class="<?= $Grid->id_kuliah->headerCellClass() ?>"><div id="elh_kuliah_id_kuliah" class="kuliah_id_kuliah"><?= $Grid->renderSort($Grid->id_kuliah) ?></div></th>
<?php } ?>
<?php if ($Grid->id_mk->Visible) { // id_mk ?>
        <th data-name="id_mk" class="<?= $Grid->id_mk->headerCellClass() ?>"><div id="elh_kuliah_id_mk" class="kuliah_id_mk"><?= $Grid->renderSort($Grid->id_mk) ?></div></th>
<?php } ?>
<?php if ($Grid->tahun_ajaran->Visible) { // tahun_ajaran ?>
        <th data-name="tahun_ajaran" class="<?= $Grid->tahun_ajaran->headerCellClass() ?>"><div id="elh_kuliah_tahun_ajaran" class="kuliah_tahun_ajaran"><?= $Grid->renderSort($Grid->tahun_ajaran) ?></div></th>
<?php } ?>
<?php
// Render list options (header, right)
$Grid->ListOptions->render("header", "right");
?>
    </tr>
</thead>
<tbody>
<?php
$Grid->StartRecord = 1;
$Grid->StopRecord = $Grid->TotalRecords; // Show all records

// Restore number of post back records
if ($CurrentForm && ($Grid->isConfirm() || $Grid->EventCancelled)) {
    $CurrentForm->Index = -1;
    if ($CurrentForm->hasValue($Grid->FormKeyCountName) && ($Grid->isGridAdd() || $Grid->isGridEdit() || $Grid->isConfirm())) {
        $Grid->KeyCount = $CurrentForm->getValue($Grid->FormKeyCountName);
        $Grid->StopRecord = $Grid->StartRecord + $Grid->KeyCount - 1;
    }
}
$Grid->RecordCount = $Grid->StartRecord - 1;
if ($Grid->Recordset && !$Grid->Recordset->EOF) {
    // Nothing to do
} elseif (!$Grid->AllowAddDeleteRow && $Grid->StopRecord == 0) {
    $Grid->StopRecord = $Grid->GridAddRowCount;
}

// Initialize aggregate
$Grid->RowType = ROWTYPE_AGGREGATEINIT;
$Grid->resetAttributes();
$Grid->renderRow();
if ($Grid->isGridAdd())
    $Grid->RowIndex = 0;
if ($Grid->isGridEdit())
    $Grid->RowIndex = 0;
while ($Grid->RecordCount < $Grid->StopRecord) {
    $Grid->RecordCount++;
    if ($Grid->RecordCount >= $Grid->StartRecord) {
        $Grid->RowCount++;
        if ($Grid->isGridAdd() || $Grid->isGridEdit() || $Grid->isConfirm()) {
            $Grid->RowIndex++;
            $CurrentForm->Index = $Grid->RowIndex;
            if ($CurrentForm->hasValue($Grid->FormActionName) && ($Grid->isConfirm() || $Grid->EventCancelled)) {
                $Grid->RowAction = strval($CurrentForm->getValue($Grid->FormActionName));
            } elseif ($Grid->isGridAdd()) {
                $Grid->RowAction = "insert";
            } else {
                $Grid->RowAction = "";
            }
        }

        // Set up key count
        $Grid->KeyCount = $Grid->RowIndex;

        // Init row class and style
        $Grid->resetAttributes();
        $Grid->CssClass = "";
        if ($Grid->isGridAdd()) {
            if ($Grid->CurrentMode == "copy") {
                $Grid->loadRowValues($Grid->Recordset); // Load row values
                $Grid->OldKey = $Grid->getKey(true); // Get from CurrentValue
            } else {
                $Grid->loadRowValues(); // Load default values
                $Grid->OldKey = "";
            }
        } else {
            $Grid->loadRowValues($Grid->Recordset); // Load row values
            $Grid->OldKey = $Grid->getKey(true); // Get from CurrentValue
        }
        $Grid->setKey($Grid->OldKey);
        $Grid->RowType = ROWTYPE_VIEW; // Render view
        if ($Grid->isGridAdd()) { // Grid add
            $Grid->RowType = ROWTYPE_ADD; // Render add
        }
        if ($Grid->isGridAdd() && $Grid->EventCancelled && !$CurrentForm->hasValue("k_blankrow")) { // Insert failed
            $Grid->restoreCurrentRowFormValues($Grid->RowIndex); // Restore form values
        }
        if ($Grid->isGridEdit()) { // Grid edit
            if ($Grid->EventCancelled) {
                $Grid->restoreCurrentRowFormValues($Grid->RowIndex); // Restore form values
            }
            if ($Grid->RowAction == "insert") {
                $Grid->RowType = ROWTYPE_ADD; // Render add
            } else {
                $Grid->RowType = ROWTYPE_EDIT; // Render edit
            }
        }
        if ($Grid->isGridEdit() && ($Grid->RowType == ROWTYPE_EDIT || $Grid->RowType == ROWTYPE_ADD) && $Grid->EventCancelled) { // Update failed
            $Grid->restoreCurrentRowFormValues($Grid->RowIndex); // Restore form values
        }
        if ($Grid->RowType == ROWTYPE_EDIT) { // Edit row
            $Grid->EditRowCount++;
        }
        if ($Grid->isConfirm()) { // Confirm row
            $Grid->restoreCurrentRowFormValues($Grid->RowIndex); // Restore form values
        }

        // Set up row id / data-rowindex
        $Grid->RowAttrs->merge(["data-rowindex" => $Grid->RowCount, "id" => "r" . $Grid->RowCount . "_kuliah", "data-rowtype" => $Grid->RowType]);

        // Render row
        $Grid->renderRow();

        // Render list options
        $Grid->renderListOptions();

        // Skip delete row / empty row for confirm page
        if ($Grid->RowAction != "delete" && $Grid->RowAction != "insertdelete" && !($Grid->RowAction == "insert" && $Grid->isConfirm() && $Grid->emptyRow())) {
?>
    <tr <?= $Grid->rowAttributes() ?>>
<?php
// Render list options (body, left)
$Grid->ListOptions->render("body", "left", $Grid->RowCount);
?>
    <?php if ($Grid->id_kuliah->Visible) { // id_kuliah ?>
        <td data-name="id_kuliah" <?= $Grid->id_kuliah->cellAttributes() ?>>
<?php if ($Grid->RowType == ROWTYPE_ADD) { // Add record ?>
<span id="el<?= $Grid->RowCount ?>_kuliah_id_kuliah" class="form-group"></span>
<input type="hidden" data-table="kuliah" data-field="x_id_kuliah" data-hidden="1" name="o<?= $Grid->RowIndex ?>_id_kuliah" id="o<?= $Grid->RowIndex ?>_id_kuliah" value="<?= HtmlEncode($Grid->id_kuliah->OldValue) ?>">
<?php } ?>
<?php if ($Grid->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Grid->RowCount ?>_kuliah_id_kuliah" class="form-group">
<span<?= $Grid->id_kuliah->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Grid->id_kuliah->getDisplayValue($Grid->id_kuliah->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="kuliah" data-field="x_id_kuliah" data-hidden="1" name="x<?= $Grid->RowIndex ?>_id_kuliah" id="x<?= $Grid->RowIndex ?>_id_kuliah" value="<?= HtmlEncode($Grid->id_kuliah->CurrentValue) ?>">
<?php } ?>
<?php if ($Grid->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Grid->RowCount ?>_kuliah_id_kuliah">
<span<?= $Grid->id_kuliah->viewAttributes() ?>>
<?= $Grid->id_kuliah->getViewValue() ?></span>
</span>
<?php if ($Grid->isConfirm()) { ?>
<input type="hidden" data-table="kuliah" data-field="x_id_kuliah" data-hidden="1" name="fkuliahgrid$x<?= $Grid->RowIndex ?>_id_kuliah" id="fkuliahgrid$x<?= $Grid->RowIndex ?>_id_kuliah" value="<?= HtmlEncode($Grid->id_kuliah->FormValue) ?>">
<input type="hidden" data-table="kuliah" data-field="x_id_kuliah" data-hidden="1" name="fkuliahgrid$o<?= $Grid->RowIndex ?>_id_kuliah" id="fkuliahgrid$o<?= $Grid->RowIndex ?>_id_kuliah" value="<?= HtmlEncode($Grid->id_kuliah->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
    <?php } else { ?>
            <input type="hidden" data-table="kuliah" data-field="x_id_kuliah" data-hidden="1" name="x<?= $Grid->RowIndex ?>_id_kuliah" id="x<?= $Grid->RowIndex ?>_id_kuliah" value="<?= HtmlEncode($Grid->id_kuliah->CurrentValue) ?>">
    <?php } ?>
    <?php if ($Grid->id_mk->Visible) { // id_mk ?>
        <td data-name="id_mk" <?= $Grid->id_mk->cellAttributes() ?>>
<?php if ($Grid->RowType == ROWTYPE_ADD) { // Add record ?>
<span id="el<?= $Grid->RowCount ?>_kuliah_id_mk" class="form-group">
<?php
$onchange = $Grid->id_mk->EditAttrs->prepend("onchange", "");
$onchange = ($onchange) ? ' onchange="' . JsEncode($onchange) . '"' : '';
$Grid->id_mk->EditAttrs["onchange"] = "";
?>
<span id="as_x<?= $Grid->RowIndex ?>_id_mk" class="ew-auto-suggest">
    <div class="input-group flex-nowrap">
        <input type="<?= $Grid->id_mk->getInputTextType() ?>" class="form-control" name="sv_x<?= $Grid->RowIndex ?>_id_mk" id="sv_x<?= $Grid->RowIndex ?>_id_mk" value="<?= RemoveHtml($Grid->id_mk->EditValue) ?>" size="30" placeholder="<?= HtmlEncode($Grid->id_mk->getPlaceHolder()) ?>" data-placeholder="<?= HtmlEncode($Grid->id_mk->getPlaceHolder()) ?>"<?= $Grid->id_mk->editAttributes() ?>>
        <div class="input-group-append">
            <button type="button" title="<?= HtmlEncode(str_replace("%s", RemoveHtml($Grid->id_mk->caption()), $Language->phrase("LookupLink", true))) ?>" onclick="ew.modalLookupShow({lnk:this,el:'x<?= $Grid->RowIndex ?>_id_mk',m:0,n:10,srch:true});" class="ew-lookup-btn btn btn-default"<?= ($Grid->id_mk->ReadOnly || $Grid->id_mk->Disabled) ? " disabled" : "" ?>><i class="fas fa-search ew-icon"></i></button>
        </div>
    </div>
</span>
<input type="hidden" is="selection-list" class="form-control" data-table="kuliah" data-field="x_id_mk" data-input="sv_x<?= $Grid->RowIndex ?>_id_mk" data-type="text" data-multiple="0" data-lookup="1" data-value-separator="<?= $Grid->id_mk->displayValueSeparatorAttribute() ?>" name="x<?= $Grid->RowIndex ?>_id_mk" id="x<?= $Grid->RowIndex ?>_id_mk" value="<?= HtmlEncode($Grid->id_mk->CurrentValue) ?>"<?= $onchange ?>>
<div class="invalid-feedback"><?= $Grid->id_mk->getErrorMessage() ?></div>
<script>
loadjs.ready(["fkuliahgrid"], function() {
    fkuliahgrid.createAutoSuggest(Object.assign({"id":"x<?= $Grid->RowIndex ?>_id_mk","forceSelect":false}, ew.vars.tables.kuliah.fields.id_mk.autoSuggestOptions));
});
</script>
<?= $Grid->id_mk->Lookup->getParamTag($Grid, "p_x" . $Grid->RowIndex . "_id_mk") ?>
</span>
<input type="hidden" data-table="kuliah" data-field="x_id_mk" data-hidden="1" name="o<?= $Grid->RowIndex ?>_id_mk" id="o<?= $Grid->RowIndex ?>_id_mk" value="<?= HtmlEncode($Grid->id_mk->OldValue) ?>">
<?php } ?>
<?php if ($Grid->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Grid->RowCount ?>_kuliah_id_mk" class="form-group">
<?php
$onchange = $Grid->id_mk->EditAttrs->prepend("onchange", "");
$onchange = ($onchange) ? ' onchange="' . JsEncode($onchange) . '"' : '';
$Grid->id_mk->EditAttrs["onchange"] = "";
?>
<span id="as_x<?= $Grid->RowIndex ?>_id_mk" class="ew-auto-suggest">
    <div class="input-group flex-nowrap">
        <input type="<?= $Grid->id_mk->getInputTextType() ?>" class="form-control" name="sv_x<?= $Grid->RowIndex ?>_id_mk" id="sv_x<?= $Grid->RowIndex ?>_id_mk" value="<?= RemoveHtml($Grid->id_mk->EditValue) ?>" size="30" placeholder="<?= HtmlEncode($Grid->id_mk->getPlaceHolder()) ?>" data-placeholder="<?= HtmlEncode($Grid->id_mk->getPlaceHolder()) ?>"<?= $Grid->id_mk->editAttributes() ?>>
        <div class="input-group-append">
            <button type="button" title="<?= HtmlEncode(str_replace("%s", RemoveHtml($Grid->id_mk->caption()), $Language->phrase("LookupLink", true))) ?>" onclick="ew.modalLookupShow({lnk:this,el:'x<?= $Grid->RowIndex ?>_id_mk',m:0,n:10,srch:true});" class="ew-lookup-btn btn btn-default"<?= ($Grid->id_mk->ReadOnly || $Grid->id_mk->Disabled) ? " disabled" : "" ?>><i class="fas fa-search ew-icon"></i></button>
        </div>
    </div>
</span>
<input type="hidden" is="selection-list" class="form-control" data-table="kuliah" data-field="x_id_mk" data-input="sv_x<?= $Grid->RowIndex ?>_id_mk" data-type="text" data-multiple="0" data-lookup="1" data-value-separator="<?= $Grid->id_mk->displayValueSeparatorAttribute() ?>" name="x<?= $Grid->RowIndex ?>_id_mk" id="x<?= $Grid->RowIndex ?>_id_mk" value="<?= HtmlEncode($Grid->id_mk->CurrentValue) ?>"<?= $onchange ?>>
<div class="invalid-feedback"><?= $Grid->id_mk->getErrorMessage() ?></div>
<script>
loadjs.ready(["fkuliahgrid"], function() {
    fkuliahgrid.createAutoSuggest(Object.assign({"id":"x<?= $Grid->RowIndex ?>_id_mk","forceSelect":false}, ew.vars.tables.kuliah.fields.id_mk.autoSuggestOptions));
});
</script>
<?= $Grid->id_mk->Lookup->getParamTag($Grid, "p_x" . $Grid->RowIndex . "_id_mk") ?>
</span>
<?php } ?>
<?php if ($Grid->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Grid->RowCount ?>_kuliah_id_mk">
<span<?= $Grid->id_mk->viewAttributes() ?>>
<?= $Grid->id_mk->getViewValue() ?></span>
</span>
<?php if ($Grid->isConfirm()) { ?>
<input type="hidden" data-table="kuliah" data-field="x_id_mk" data-hidden="1" name="fkuliahgrid$x<?= $Grid->RowIndex ?>_id_mk" id="fkuliahgrid$x<?= $Grid->RowIndex ?>_id_mk" value="<?= HtmlEncode($Grid->id_mk->FormValue) ?>">
<input type="hidden" data-table="kuliah" data-field="x_id_mk" data-hidden="1" name="fkuliahgrid$o<?= $Grid->RowIndex ?>_id_mk" id="fkuliahgrid$o<?= $Grid->RowIndex ?>_id_mk" value="<?= HtmlEncode($Grid->id_mk->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Grid->tahun_ajaran->Visible) { // tahun_ajaran ?>
        <td data-name="tahun_ajaran" <?= $Grid->tahun_ajaran->cellAttributes() ?>>
<?php if ($Grid->RowType == ROWTYPE_ADD) { // Add record ?>
<span id="el<?= $Grid->RowCount ?>_kuliah_tahun_ajaran" class="form-group">
<input type="<?= $Grid->tahun_ajaran->getInputTextType() ?>" data-table="kuliah" data-field="x_tahun_ajaran" name="x<?= $Grid->RowIndex ?>_tahun_ajaran" id="x<?= $Grid->RowIndex ?>_tahun_ajaran" size="30" maxlength="255" placeholder="<?= HtmlEncode($Grid->tahun_ajaran->getPlaceHolder()) ?>" value="<?= $Grid->tahun_ajaran->EditValue ?>"<?= $Grid->tahun_ajaran->editAttributes() ?>>
<div class="invalid-feedback"><?= $Grid->tahun_ajaran->getErrorMessage() ?></div>
</span>
<input type="hidden" data-table="kuliah" data-field="x_tahun_ajaran" data-hidden="1" name="o<?= $Grid->RowIndex ?>_tahun_ajaran" id="o<?= $Grid->RowIndex ?>_tahun_ajaran" value="<?= HtmlEncode($Grid->tahun_ajaran->OldValue) ?>">
<?php } ?>
<?php if ($Grid->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Grid->RowCount ?>_kuliah_tahun_ajaran" class="form-group">
<input type="<?= $Grid->tahun_ajaran->getInputTextType() ?>" data-table="kuliah" data-field="x_tahun_ajaran" name="x<?= $Grid->RowIndex ?>_tahun_ajaran" id="x<?= $Grid->RowIndex ?>_tahun_ajaran" size="30" maxlength="255" placeholder="<?= HtmlEncode($Grid->tahun_ajaran->getPlaceHolder()) ?>" value="<?= $Grid->tahun_ajaran->EditValue ?>"<?= $Grid->tahun_ajaran->editAttributes() ?>>
<div class="invalid-feedback"><?= $Grid->tahun_ajaran->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Grid->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Grid->RowCount ?>_kuliah_tahun_ajaran">
<span<?= $Grid->tahun_ajaran->viewAttributes() ?>>
<?= $Grid->tahun_ajaran->getViewValue() ?></span>
</span>
<?php if ($Grid->isConfirm()) { ?>
<input type="hidden" data-table="kuliah" data-field="x_tahun_ajaran" data-hidden="1" name="fkuliahgrid$x<?= $Grid->RowIndex ?>_tahun_ajaran" id="fkuliahgrid$x<?= $Grid->RowIndex ?>_tahun_ajaran" value="<?= HtmlEncode($Grid->tahun_ajaran->FormValue) ?>">
<input type="hidden" data-table="kuliah" data-field="x_tahun_ajaran" data-hidden="1" name="fkuliahgrid$o<?= $Grid->RowIndex ?>_tahun_ajaran" id="fkuliahgrid$o<?= $Grid->RowIndex ?>_tahun_ajaran" value="<?= HtmlEncode($Grid->tahun_ajaran->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
    <?php } ?>
<?php
// Render list options (body, right)
$Grid->ListOptions->render("body", "right", $Grid->RowCount);
?>
    </tr>
<?php if ($Grid->RowType == ROWTYPE_ADD || $Grid->RowType == ROWTYPE_EDIT) { ?>
<script>
loadjs.ready(["fkuliahgrid","load"], function () {
    fkuliahgrid.updateLists(<?= $Grid->RowIndex ?>);
});
</script>
<?php } ?>
<?php
    }
    } // End delete row checking
    if (!$Grid->isGridAdd() || $Grid->CurrentMode == "copy")
        if (!$Grid->Recordset->EOF) {
            $Grid->Recordset->moveNext();
        }
}
?>
<?php
    if ($Grid->CurrentMode == "add" || $Grid->CurrentMode == "copy" || $Grid->CurrentMode == "edit") {
        $Grid->RowIndex = '$rowindex$';
        $Grid->loadRowValues();

        // Set row properties
        $Grid->resetAttributes();
        $Grid->RowAttrs->merge(["data-rowindex" => $Grid->RowIndex, "id" => "r0_kuliah", "data-rowtype" => ROWTYPE_ADD]);
        $Grid->RowAttrs->appendClass("ew-template");
        $Grid->RowType = ROWTYPE_ADD;

        // Render row
        $Grid->renderRow();

        // Render list options
        $Grid->renderListOptions();
        $Grid->StartRowCount = 0;
?>
    <tr <?= $Grid->rowAttributes() ?>>
<?php
// Render list options (body, left)
$Grid->ListOptions->render("body", "left", $Grid->RowIndex);
?>
    <?php if ($Grid->id_kuliah->Visible) { // id_kuliah ?>
        <td data-name="id_kuliah">
<?php if (!$Grid->isConfirm()) { ?>
<span id="el$rowindex$_kuliah_id_kuliah" class="form-group kuliah_id_kuliah"></span>
<?php } else { ?>
<span id="el$rowindex$_kuliah_id_kuliah" class="form-group kuliah_id_kuliah">
<span<?= $Grid->id_kuliah->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Grid->id_kuliah->getDisplayValue($Grid->id_kuliah->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="kuliah" data-field="x_id_kuliah" data-hidden="1" name="x<?= $Grid->RowIndex ?>_id_kuliah" id="x<?= $Grid->RowIndex ?>_id_kuliah" value="<?= HtmlEncode($Grid->id_kuliah->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="kuliah" data-field="x_id_kuliah" data-hidden="1" name="o<?= $Grid->RowIndex ?>_id_kuliah" id="o<?= $Grid->RowIndex ?>_id_kuliah" value="<?= HtmlEncode($Grid->id_kuliah->OldValue) ?>">
</td>
    <?php } ?>
    <?php if ($Grid->id_mk->Visible) { // id_mk ?>
        <td data-name="id_mk">
<?php if (!$Grid->isConfirm()) { ?>
<span id="el$rowindex$_kuliah_id_mk" class="form-group kuliah_id_mk">
<?php
$onchange = $Grid->id_mk->EditAttrs->prepend("onchange", "");
$onchange = ($onchange) ? ' onchange="' . JsEncode($onchange) . '"' : '';
$Grid->id_mk->EditAttrs["onchange"] = "";
?>
<span id="as_x<?= $Grid->RowIndex ?>_id_mk" class="ew-auto-suggest">
    <div class="input-group flex-nowrap">
        <input type="<?= $Grid->id_mk->getInputTextType() ?>" class="form-control" name="sv_x<?= $Grid->RowIndex ?>_id_mk" id="sv_x<?= $Grid->RowIndex ?>_id_mk" value="<?= RemoveHtml($Grid->id_mk->EditValue) ?>" size="30" placeholder="<?= HtmlEncode($Grid->id_mk->getPlaceHolder()) ?>" data-placeholder="<?= HtmlEncode($Grid->id_mk->getPlaceHolder()) ?>"<?= $Grid->id_mk->editAttributes() ?>>
        <div class="input-group-append">
            <button type="button" title="<?= HtmlEncode(str_replace("%s", RemoveHtml($Grid->id_mk->caption()), $Language->phrase("LookupLink", true))) ?>" onclick="ew.modalLookupShow({lnk:this,el:'x<?= $Grid->RowIndex ?>_id_mk',m:0,n:10,srch:true});" class="ew-lookup-btn btn btn-default"<?= ($Grid->id_mk->ReadOnly || $Grid->id_mk->Disabled) ? " disabled" : "" ?>><i class="fas fa-search ew-icon"></i></button>
        </div>
    </div>
</span>
<input type="hidden" is="selection-list" class="form-control" data-table="kuliah" data-field="x_id_mk" data-input="sv_x<?= $Grid->RowIndex ?>_id_mk" data-type="text" data-multiple="0" data-lookup="1" data-value-separator="<?= $Grid->id_mk->displayValueSeparatorAttribute() ?>" name="x<?= $Grid->RowIndex ?>_id_mk" id="x<?= $Grid->RowIndex ?>_id_mk" value="<?= HtmlEncode($Grid->id_mk->CurrentValue) ?>"<?= $onchange ?>>
<div class="invalid-feedback"><?= $Grid->id_mk->getErrorMessage() ?></div>
<script>
loadjs.ready(["fkuliahgrid"], function() {
    fkuliahgrid.createAutoSuggest(Object.assign({"id":"x<?= $Grid->RowIndex ?>_id_mk","forceSelect":false}, ew.vars.tables.kuliah.fields.id_mk.autoSuggestOptions));
});
</script>
<?= $Grid->id_mk->Lookup->getParamTag($Grid, "p_x" . $Grid->RowIndex . "_id_mk") ?>
</span>
<?php } else { ?>
<span id="el$rowindex$_kuliah_id_mk" class="form-group kuliah_id_mk">
<span<?= $Grid->id_mk->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Grid->id_mk->getDisplayValue($Grid->id_mk->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="kuliah" data-field="x_id_mk" data-hidden="1" name="x<?= $Grid->RowIndex ?>_id_mk" id="x<?= $Grid->RowIndex ?>_id_mk" value="<?= HtmlEncode($Grid->id_mk->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="kuliah" data-field="x_id_mk" data-hidden="1" name="o<?= $Grid->RowIndex ?>_id_mk" id="o<?= $Grid->RowIndex ?>_id_mk" value="<?= HtmlEncode($Grid->id_mk->OldValue) ?>">
</td>
    <?php } ?>
    <?php if ($Grid->tahun_ajaran->Visible) { // tahun_ajaran ?>
        <td data-name="tahun_ajaran">
<?php if (!$Grid->isConfirm()) { ?>
<span id="el$rowindex$_kuliah_tahun_ajaran" class="form-group kuliah_tahun_ajaran">
<input type="<?= $Grid->tahun_ajaran->getInputTextType() ?>" data-table="kuliah" data-field="x_tahun_ajaran" name="x<?= $Grid->RowIndex ?>_tahun_ajaran" id="x<?= $Grid->RowIndex ?>_tahun_ajaran" size="30" maxlength="255" placeholder="<?= HtmlEncode($Grid->tahun_ajaran->getPlaceHolder()) ?>" value="<?= $Grid->tahun_ajaran->EditValue ?>"<?= $Grid->tahun_ajaran->editAttributes() ?>>
<div class="invalid-feedback"><?= $Grid->tahun_ajaran->getErrorMessage() ?></div>
</span>
<?php } else { ?>
<span id="el$rowindex$_kuliah_tahun_ajaran" class="form-group kuliah_tahun_ajaran">
<span<?= $Grid->tahun_ajaran->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Grid->tahun_ajaran->getDisplayValue($Grid->tahun_ajaran->ViewValue))) ?>"></span>
</span>
<input type="hidden" data-table="kuliah" data-field="x_tahun_ajaran" data-hidden="1" name="x<?= $Grid->RowIndex ?>_tahun_ajaran" id="x<?= $Grid->RowIndex ?>_tahun_ajaran" value="<?= HtmlEncode($Grid->tahun_ajaran->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="kuliah" data-field="x_tahun_ajaran" data-hidden="1" name="o<?= $Grid->RowIndex ?>_tahun_ajaran" id="o<?= $Grid->RowIndex ?>_tahun_ajaran" value="<?= HtmlEncode($Grid->tahun_ajaran->OldValue) ?>">
</td>
    <?php } ?>
<?php
// Render list options (body, right)
$Grid->ListOptions->render("body", "right", $Grid->RowIndex);
?>
<script>
loadjs.ready(["fkuliahgrid","load"], function() {
    fkuliahgrid.updateLists(<?= $Grid->RowIndex ?>);
});
</script>
    </tr>
<?php
    }
?>
</tbody>
</table><!-- /.ew-table -->
</div><!-- /.ew-grid-middle-panel -->
<?php if ($Grid->CurrentMode == "add" || $Grid->CurrentMode == "copy") { ?>
<input type="hidden" name="<?= $Grid->FormKeyCountName ?>" id="<?= $Grid->FormKeyCountName ?>" value="<?= $Grid->KeyCount ?>">
<?= $Grid->MultiSelectKey ?>
<?php } ?>
<?php if ($Grid->CurrentMode == "edit") { ?>
<input type="hidden" name="<?= $Grid->FormKeyCountName ?>" id="<?= $Grid->FormKeyCountName ?>" value="<?= $Grid->KeyCount ?>">
<?= $Grid->MultiSelectKey ?>
<?php } ?>
<?php if ($Grid->CurrentMode == "") { ?>
<input type="hidden" name="action" id="action" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="fkuliahgrid">
</div><!-- /.ew-list-form -->
<?php
// Close recordset
if ($Grid->Recordset) {
    $Grid->Recordset->close();
}
?>
<?php if ($Grid->ShowOtherOptions) { ?>
<div class="card-footer ew-grid-lower-panel">
<?php $Grid->OtherOptions->render("body", "bottom") ?>
</div>
<div class="clearfix"></div>
<?php } ?>
</div><!-- /.ew-grid -->
<?php } ?>
<?php if ($Grid->TotalRecords == 0 && !$Grid->CurrentAction) { // Show other options ?>
<div class="ew-list-other-options">
<?php $Grid->OtherOptions->render("body") ?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if (!$Grid->isExport()) { ?>
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
<?php } ?>
