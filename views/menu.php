<?php

namespace PHPMaker2021\project1;

// Menu Language
if ($Language && function_exists(PROJECT_NAMESPACE . "Config") && $Language->LanguageFolder == Config("LANGUAGE_FOLDER")) {
    $MenuRelativePath = "";
    $MenuLanguage = &$Language;
} else { // Compat reports
    $LANGUAGE_FOLDER = "../lang/";
    $MenuRelativePath = "../";
    $MenuLanguage = Container("language");
}

// Navbar menu
$topMenu = new Menu("navbar", true, true);
echo $topMenu->toScript();

// Sidebar menu
$sideMenu = new Menu("menu", true, false);
$sideMenu->addMenuItem(3, "mi_pengguna", $MenuLanguage->MenuPhrase("3", "MenuText"), $MenuRelativePath . "penggunalist", -1, "", AllowListMenu('{553B464A-AE3F-48D9-93ED-53082B8EA5F0}pengguna'), false, false, "", "", false);
$sideMenu->addMenuItem(8, "mci_Transaksi_Data", $MenuLanguage->MenuPhrase("8", "MenuText"), "", -1, "", true, false, true, "", "", false);
$sideMenu->addMenuItem(2, "mi_mata_kuliah", $MenuLanguage->MenuPhrase("2", "MenuText"), $MenuRelativePath . "matakuliahlist", 8, "", AllowListMenu('{553B464A-AE3F-48D9-93ED-53082B8EA5F0}mata_kuliah'), false, false, "", "", false);
$sideMenu->addMenuItem(1, "mi_mahasiswa", $MenuLanguage->MenuPhrase("1", "MenuText"), $MenuRelativePath . "mahasiswalist", 8, "", AllowListMenu('{553B464A-AE3F-48D9-93ED-53082B8EA5F0}mahasiswa'), false, false, "", "", false);
$sideMenu->addMenuItem(7, "mi_v_kuliah", $MenuLanguage->MenuPhrase("7", "MenuText"), $MenuRelativePath . "vkuliahlist", 8, "", AllowListMenu('{553B464A-AE3F-48D9-93ED-53082B8EA5F0}v_kuliah'), false, false, "", "", false);
$sideMenu->addMenuItem(9, "mci_Laporan", $MenuLanguage->MenuPhrase("9", "MenuText"), "", -1, "", true, false, true, "", "", false);
$sideMenu->addMenuItem(5, "mi_v_mhs", $MenuLanguage->MenuPhrase("5", "MenuText"), $MenuRelativePath . "vmhslist", 9, "", AllowListMenu('{553B464A-AE3F-48D9-93ED-53082B8EA5F0}v_mhs'), false, false, "", "", false);
echo $sideMenu->toScript();
