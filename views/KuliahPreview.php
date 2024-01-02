<?php

namespace PHPMaker2021\project1;

// Page object
$KuliahPreview = &$Page;
?>
<script>
if (!ew.vars.tables.kuliah) ew.vars.tables.kuliah = <?= JsonEncode(GetClientVar("tables", "kuliah")) ?>;
</script>
<?php $Page->showPageHeader(); ?>
<?php if ($Page->TotalRecords > 0) { ?>
<div class="card ew-grid kuliah"><!-- .card -->
<div class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel ew-preview-middle-panel"><!-- .table-responsive -->
<table class="table ew-table ew-preview-table"><!-- .table -->
    <thead><!-- Table header -->
        <tr class="ew-table-header">
<?php
// Render list options
$Page->renderListOptions();

// Render list options (header, left)
$Page->ListOptions->render("header", "left");
?>
<?php if ($Page->id_kuliah->Visible) { // id_kuliah ?>
    <?php if ($Page->SortUrl($Page->id_kuliah) == "") { ?>
        <th class="<?= $Page->id_kuliah->headerCellClass() ?>"><?= $Page->id_kuliah->caption() ?></th>
    <?php } else { ?>
        <th class="<?= $Page->id_kuliah->headerCellClass() ?>"><div class="ew-pointer" data-sort="<?= HtmlEncode($Page->id_kuliah->Name) ?>" data-sort-type="1" data-sort-order="<?= $Page->id_kuliah->getNextSort() ?>">
            <div class="ew-table-header-btn"><span class="ew-table-header-caption"><?= $Page->id_kuliah->caption() ?></span><span class="ew-table-header-sort"><?php if ($Page->id_kuliah->getSort() == "ASC") { ?><i class="fas fa-sort-up"></i><?php } elseif ($Page->id_kuliah->getSort() == "DESC") { ?><i class="fas fa-sort-down"></i><?php } ?></span>
        </div></div></th>
    <?php } ?>
<?php } ?>
<?php if ($Page->id_mk->Visible) { // id_mk ?>
    <?php if ($Page->SortUrl($Page->id_mk) == "") { ?>
        <th class="<?= $Page->id_mk->headerCellClass() ?>"><?= $Page->id_mk->caption() ?></th>
    <?php } else { ?>
        <th class="<?= $Page->id_mk->headerCellClass() ?>"><div class="ew-pointer" data-sort="<?= HtmlEncode($Page->id_mk->Name) ?>" data-sort-type="1" data-sort-order="<?= $Page->id_mk->getNextSort() ?>">
            <div class="ew-table-header-btn"><span class="ew-table-header-caption"><?= $Page->id_mk->caption() ?></span><span class="ew-table-header-sort"><?php if ($Page->id_mk->getSort() == "ASC") { ?><i class="fas fa-sort-up"></i><?php } elseif ($Page->id_mk->getSort() == "DESC") { ?><i class="fas fa-sort-down"></i><?php } ?></span>
        </div></div></th>
    <?php } ?>
<?php } ?>
<?php if ($Page->tahun_ajaran->Visible) { // tahun_ajaran ?>
    <?php if ($Page->SortUrl($Page->tahun_ajaran) == "") { ?>
        <th class="<?= $Page->tahun_ajaran->headerCellClass() ?>"><?= $Page->tahun_ajaran->caption() ?></th>
    <?php } else { ?>
        <th class="<?= $Page->tahun_ajaran->headerCellClass() ?>"><div class="ew-pointer" data-sort="<?= HtmlEncode($Page->tahun_ajaran->Name) ?>" data-sort-type="1" data-sort-order="<?= $Page->tahun_ajaran->getNextSort() ?>">
            <div class="ew-table-header-btn"><span class="ew-table-header-caption"><?= $Page->tahun_ajaran->caption() ?></span><span class="ew-table-header-sort"><?php if ($Page->tahun_ajaran->getSort() == "ASC") { ?><i class="fas fa-sort-up"></i><?php } elseif ($Page->tahun_ajaran->getSort() == "DESC") { ?><i class="fas fa-sort-down"></i><?php } ?></span>
        </div></div></th>
    <?php } ?>
<?php } ?>
<?php
// Render list options (header, right)
$Page->ListOptions->render("header", "right");
?>
        </tr>
    </thead>
    <tbody><!-- Table body -->
<?php
$Page->RecCount = 0;
$Page->RowCount = 0;
while ($Page->Recordset && !$Page->Recordset->EOF) {
    // Init row class and style
    $Page->RecCount++;
    $Page->RowCount++;
    $Page->CssStyle = "";
    $Page->loadListRowValues($Page->Recordset);

    // Render row
    $Page->RowType = ROWTYPE_PREVIEW; // Preview record
    $Page->resetAttributes();
    $Page->renderListRow();

    // Render list options
    $Page->renderListOptions();
?>
    <tr <?= $Page->rowAttributes() ?>>
<?php
// Render list options (body, left)
$Page->ListOptions->render("body", "left", $Page->RowCount);
?>
<?php if ($Page->id_kuliah->Visible) { // id_kuliah ?>
        <!-- id_kuliah -->
        <td<?= $Page->id_kuliah->cellAttributes() ?>>
<span<?= $Page->id_kuliah->viewAttributes() ?>>
<?= $Page->id_kuliah->getViewValue() ?></span>
</td>
<?php } ?>
<?php if ($Page->id_mk->Visible) { // id_mk ?>
        <!-- id_mk -->
        <td<?= $Page->id_mk->cellAttributes() ?>>
<span<?= $Page->id_mk->viewAttributes() ?>>
<?= $Page->id_mk->getViewValue() ?></span>
</td>
<?php } ?>
<?php if ($Page->tahun_ajaran->Visible) { // tahun_ajaran ?>
        <!-- tahun_ajaran -->
        <td<?= $Page->tahun_ajaran->cellAttributes() ?>>
<span<?= $Page->tahun_ajaran->viewAttributes() ?>>
<?= $Page->tahun_ajaran->getViewValue() ?></span>
</td>
<?php } ?>
<?php
// Render list options (body, right)
$Page->ListOptions->render("body", "right", $Page->RowCount);
?>
    </tr>
<?php
    $Page->Recordset->moveNext();
} // while
?>
    </tbody>
</table><!-- /.table -->
</div><!-- /.table-responsive -->
<div class="card-footer ew-grid-lower-panel ew-preview-lower-panel"><!-- .card-footer -->
<?= $Page->Pager->render() ?>
<?php } else { // No record ?>
<div class="card no-border">
<div class="ew-detail-count"><?= $Language->phrase("NoRecord") ?></div>
<?php } ?>
<div class="ew-preview-other-options">
<?php
    foreach ($Page->OtherOptions as $option)
        $option->render("body");
?>
</div>
<?php if ($Page->TotalRecords > 0) { ?>
<div class="clearfix"></div>
</div><!-- /.card-footer -->
<?php } ?>
</div><!-- /.card -->
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<?php
if ($Page->Recordset) {
    $Page->Recordset->close();
}
?>
