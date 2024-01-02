<?php

namespace PHPMaker2021\project1;

use Doctrine\DBAL\ParameterType;

/**
 * Page class
 */
class KuliahAdd extends Kuliah
{
    use MessagesTrait;

    // Page ID
    public $PageID = "add";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'kuliah';

    // Page object name
    public $PageObjName = "KuliahAdd";

    // Rendering View
    public $RenderingView = false;

    // Page headings
    public $Heading = "";
    public $Subheading = "";
    public $PageHeader;
    public $PageFooter;

    // Page terminated
    private $terminated = false;

    // Page heading
    public function pageHeading()
    {
        global $Language;
        if ($this->Heading != "") {
            return $this->Heading;
        }
        if (method_exists($this, "tableCaption")) {
            return $this->tableCaption();
        }
        return "";
    }

    // Page subheading
    public function pageSubheading()
    {
        global $Language;
        if ($this->Subheading != "") {
            return $this->Subheading;
        }
        if ($this->TableName) {
            return $Language->phrase($this->PageID);
        }
        return "";
    }

    // Page name
    public function pageName()
    {
        return CurrentPageName();
    }

    // Page URL
    public function pageUrl()
    {
        $url = ScriptName() . "?";
        if ($this->UseTokenInUrl) {
            $url .= "t=" . $this->TableVar . "&"; // Add page token
        }
        return $url;
    }

    // Show Page Header
    public function showPageHeader()
    {
        $header = $this->PageHeader;
        $this->pageDataRendering($header);
        if ($header != "") { // Header exists, display
            echo '<p id="ew-page-header">' . $header . '</p>';
        }
    }

    // Show Page Footer
    public function showPageFooter()
    {
        $footer = $this->PageFooter;
        $this->pageDataRendered($footer);
        if ($footer != "") { // Footer exists, display
            echo '<p id="ew-page-footer">' . $footer . '</p>';
        }
    }

    // Validate page request
    protected function isPageRequest()
    {
        global $CurrentForm;
        if ($this->UseTokenInUrl) {
            if ($CurrentForm) {
                return ($this->TableVar == $CurrentForm->getValue("t"));
            }
            if (Get("t") !== null) {
                return ($this->TableVar == Get("t"));
            }
        }
        return true;
    }

    // Constructor
    public function __construct()
    {
        global $Language, $DashboardReport, $DebugTimer;
        global $UserTable;

        // Initialize
        $GLOBALS["Page"] = &$this;

        // Language object
        $Language = Container("language");

        // Parent constuctor
        parent::__construct();

        // Table object (kuliah)
        if (!isset($GLOBALS["kuliah"]) || get_class($GLOBALS["kuliah"]) == PROJECT_NAMESPACE . "kuliah") {
            $GLOBALS["kuliah"] = &$this;
        }

        // Page URL
        $pageUrl = $this->pageUrl();

        // Table name (for backward compatibility only)
        if (!defined(PROJECT_NAMESPACE . "TABLE_NAME")) {
            define(PROJECT_NAMESPACE . "TABLE_NAME", 'kuliah');
        }

        // Start timer
        $DebugTimer = Container("timer");

        // Debug message
        LoadDebugMessage();

        // Open connection
        $GLOBALS["Conn"] = $GLOBALS["Conn"] ?? $this->getConnection();

        // User table object
        $UserTable = Container("usertable");
    }

    // Get content from stream
    public function getContents($stream = null): string
    {
        global $Response;
        return is_object($Response) ? $Response->getBody() : ob_get_clean();
    }

    // Is lookup
    public function isLookup()
    {
        return SameText(Route(0), Config("API_LOOKUP_ACTION"));
    }

    // Is AutoFill
    public function isAutoFill()
    {
        return $this->isLookup() && SameText(Post("ajax"), "autofill");
    }

    // Is AutoSuggest
    public function isAutoSuggest()
    {
        return $this->isLookup() && SameText(Post("ajax"), "autosuggest");
    }

    // Is modal lookup
    public function isModalLookup()
    {
        return $this->isLookup() && SameText(Post("ajax"), "modal");
    }

    // Is terminated
    public function isTerminated()
    {
        return $this->terminated;
    }

    /**
     * Terminate page
     *
     * @param string $url URL for direction
     * @return void
     */
    public function terminate($url = "")
    {
        if ($this->terminated) {
            return;
        }
        global $ExportFileName, $TempImages, $DashboardReport, $Response;

        // Page is terminated
        $this->terminated = true;

         // Page Unload event
        if (method_exists($this, "pageUnload")) {
            $this->pageUnload();
        }

        // Global Page Unloaded event (in userfn*.php)
        Page_Unloaded();

        // Export
        if ($this->CustomExport && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, Config("EXPORT_CLASSES"))) {
            $content = $this->getContents();
            if ($ExportFileName == "") {
                $ExportFileName = $this->TableVar;
            }
            $class = PROJECT_NAMESPACE . Config("EXPORT_CLASSES." . $this->CustomExport);
            if (class_exists($class)) {
                $doc = new $class(Container("kuliah"));
                $doc->Text = @$content;
                if ($this->isExport("email")) {
                    echo $this->exportEmail($doc->Text);
                } else {
                    $doc->export();
                }
                DeleteTempImages(); // Delete temp images
                return;
            }
        }
        if (!IsApi() && method_exists($this, "pageRedirecting")) {
            $this->pageRedirecting($url);
        }

        // Close connection
        CloseConnections();

        // Return for API
        if (IsApi()) {
            $res = $url === true;
            if (!$res) { // Show error
                WriteJson(array_merge(["success" => false], $this->getMessages()));
            }
            return;
        } else { // Check if response is JSON
            if (StartsString("application/json", $Response->getHeaderLine("Content-type")) && $Response->getBody()->getSize()) { // With JSON response
                $this->clearMessages();
                return;
            }
        }

        // Go to URL if specified
        if ($url != "") {
            if (!Config("DEBUG") && ob_get_length()) {
                ob_end_clean();
            }

            // Handle modal response
            if ($this->IsModal) { // Show as modal
                $row = ["url" => GetUrl($url), "modal" => "1"];
                $pageName = GetPageName($url);
                if ($pageName != $this->getListUrl()) { // Not List page
                    $row["caption"] = $this->getModalCaption($pageName);
                    if ($pageName == "kuliahview") {
                        $row["view"] = "1";
                    }
                } else { // List page should not be shown as modal => error
                    $row["error"] = $this->getFailureMessage();
                    $this->clearFailureMessage();
                }
                WriteJson($row);
            } else {
                SaveDebugMessage();
                Redirect(GetUrl($url));
            }
        }
        return; // Return to controller
    }

    // Get records from recordset
    protected function getRecordsFromRecordset($rs, $current = false)
    {
        $rows = [];
        if (is_object($rs)) { // Recordset
            while ($rs && !$rs->EOF) {
                $this->loadRowValues($rs); // Set up DbValue/CurrentValue
                $row = $this->getRecordFromArray($rs->fields);
                if ($current) {
                    return $row;
                } else {
                    $rows[] = $row;
                }
                $rs->moveNext();
            }
        } elseif (is_array($rs)) {
            foreach ($rs as $ar) {
                $row = $this->getRecordFromArray($ar);
                if ($current) {
                    return $row;
                } else {
                    $rows[] = $row;
                }
            }
        }
        return $rows;
    }

    // Get record from array
    protected function getRecordFromArray($ar)
    {
        $row = [];
        if (is_array($ar)) {
            foreach ($ar as $fldname => $val) {
                if (array_key_exists($fldname, $this->Fields) && ($this->Fields[$fldname]->Visible || $this->Fields[$fldname]->IsPrimaryKey)) { // Primary key or Visible
                    $fld = &$this->Fields[$fldname];
                    if ($fld->HtmlTag == "FILE") { // Upload field
                        if (EmptyValue($val)) {
                            $row[$fldname] = null;
                        } else {
                            if ($fld->DataType == DATATYPE_BLOB) {
                                $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                    "/" . $fld->TableVar . "/" . $fld->Param . "/" . rawurlencode($this->getRecordKeyValue($ar))));
                                $row[$fldname] = ["type" => ContentType($val), "url" => $url, "name" => $fld->Param . ContentExtension($val)];
                            } elseif (!$fld->UploadMultiple || !ContainsString($val, Config("MULTIPLE_UPLOAD_SEPARATOR"))) { // Single file
                                $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                    "/" . $fld->TableVar . "/" . Encrypt($fld->physicalUploadPath() . $val)));
                                $row[$fldname] = ["type" => MimeContentType($val), "url" => $url, "name" => $val];
                            } else { // Multiple files
                                $files = explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $val);
                                $ar = [];
                                foreach ($files as $file) {
                                    $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                        "/" . $fld->TableVar . "/" . Encrypt($fld->physicalUploadPath() . $file)));
                                    if (!EmptyValue($file)) {
                                        $ar[] = ["type" => MimeContentType($file), "url" => $url, "name" => $file];
                                    }
                                }
                                $row[$fldname] = $ar;
                            }
                        }
                    } else {
                        $row[$fldname] = $val;
                    }
                }
            }
        }
        return $row;
    }

    // Get record key value from array
    protected function getRecordKeyValue($ar)
    {
        $key = "";
        if (is_array($ar)) {
            $key .= @$ar['id_kuliah'];
        }
        return $key;
    }

    /**
     * Hide fields for add/edit
     *
     * @return void
     */
    protected function hideFieldsForAddEdit()
    {
        if ($this->isAdd() || $this->isCopy() || $this->isGridAdd()) {
            $this->id_kuliah->Visible = false;
        }
    }

    // Lookup data
    public function lookup()
    {
        global $Language, $Security;

        // Get lookup object
        $fieldName = Post("field");
        $lookup = $this->Fields[$fieldName]->Lookup;

        // Get lookup parameters
        $lookupType = Post("ajax", "unknown");
        $pageSize = -1;
        $offset = -1;
        $searchValue = "";
        if (SameText($lookupType, "modal")) {
            $searchValue = Post("sv", "");
            $pageSize = Post("recperpage", 10);
            $offset = Post("start", 0);
        } elseif (SameText($lookupType, "autosuggest")) {
            $searchValue = Param("q", "");
            $pageSize = Param("n", -1);
            $pageSize = is_numeric($pageSize) ? (int)$pageSize : -1;
            if ($pageSize <= 0) {
                $pageSize = Config("AUTO_SUGGEST_MAX_ENTRIES");
            }
            $start = Param("start", -1);
            $start = is_numeric($start) ? (int)$start : -1;
            $page = Param("page", -1);
            $page = is_numeric($page) ? (int)$page : -1;
            $offset = $start >= 0 ? $start : ($page > 0 && $pageSize > 0 ? ($page - 1) * $pageSize : 0);
        }
        $userSelect = Decrypt(Post("s", ""));
        $userFilter = Decrypt(Post("f", ""));
        $userOrderBy = Decrypt(Post("o", ""));
        $keys = Post("keys");
        $lookup->LookupType = $lookupType; // Lookup type
        if ($keys !== null) { // Selected records from modal
            if (is_array($keys)) {
                $keys = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $keys);
            }
            $lookup->FilterFields = []; // Skip parent fields if any
            $lookup->FilterValues[] = $keys; // Lookup values
            $pageSize = -1; // Show all records
        } else { // Lookup values
            $lookup->FilterValues[] = Post("v0", Post("lookupValue", ""));
        }
        $cnt = is_array($lookup->FilterFields) ? count($lookup->FilterFields) : 0;
        for ($i = 1; $i <= $cnt; $i++) {
            $lookup->FilterValues[] = Post("v" . $i, "");
        }
        $lookup->SearchValue = $searchValue;
        $lookup->PageSize = $pageSize;
        $lookup->Offset = $offset;
        if ($userSelect != "") {
            $lookup->UserSelect = $userSelect;
        }
        if ($userFilter != "") {
            $lookup->UserFilter = $userFilter;
        }
        if ($userOrderBy != "") {
            $lookup->UserOrderBy = $userOrderBy;
        }
        $lookup->toJson($this); // Use settings from current page
    }
    public $FormClassName = "ew-horizontal ew-form ew-add-form";
    public $IsModal = false;
    public $IsMobileOrModal = false;
    public $DbMasterFilter = "";
    public $DbDetailFilter = "";
    public $StartRecord;
    public $Priv = 0;
    public $OldRecordset;
    public $CopyRecord;

    /**
     * Page run
     *
     * @return void
     */
    public function run()
    {
        global $ExportType, $CustomExportType, $ExportFileName, $UserProfile, $Language, $Security, $CurrentForm,
            $SkipHeaderFooter;

        // Is modal
        $this->IsModal = Param("modal") == "1";

        // Create form object
        $CurrentForm = new HttpForm();
        $this->CurrentAction = Param("action"); // Set up current action
        $this->id_kuliah->Visible = false;
        $this->id_mk->setVisibility();
        $this->id_mhs->setVisibility();
        $this->tahun_ajaran->setVisibility();
        $this->hideFieldsForAddEdit();

        // Do not use lookup cache
        $this->setUseLookupCache(false);

        // Global Page Loading event (in userfn*.php)
        Page_Loading();

        // Page Load event
        if (method_exists($this, "pageLoad")) {
            $this->pageLoad();
        }

        // Set up lookup cache
        $this->setupLookupOptions($this->id_mk);
        $this->setupLookupOptions($this->id_mhs);

        // Check modal
        if ($this->IsModal) {
            $SkipHeaderFooter = true;
        }
        $this->IsMobileOrModal = IsMobile() || $this->IsModal;
        $this->FormClassName = "ew-form ew-add-form ew-horizontal";
        $postBack = false;

        // Set up current action
        if (IsApi()) {
            $this->CurrentAction = "insert"; // Add record directly
            $postBack = true;
        } elseif (Post("action") !== null) {
            $this->CurrentAction = Post("action"); // Get form action
            $this->setKey(Post($this->OldKeyName));
            $postBack = true;
        } else {
            // Load key values from QueryString
            if (($keyValue = Get("id_kuliah") ?? Route("id_kuliah")) !== null) {
                $this->id_kuliah->setQueryStringValue($keyValue);
            }
            $this->OldKey = $this->getKey(true); // Get from CurrentValue
            $this->CopyRecord = !EmptyValue($this->OldKey);
            if ($this->CopyRecord) {
                $this->CurrentAction = "copy"; // Copy record
            } else {
                $this->CurrentAction = "show"; // Display blank record
            }
        }

        // Load old record / default values
        $loaded = $this->loadOldRecord();

        // Set up master/detail parameters
        // NOTE: must be after loadOldRecord to prevent master key values overwritten
        $this->setupMasterParms();

        // Load form values
        if ($postBack) {
            $this->loadFormValues(); // Load form values
        }

        // Validate form if post back
        if ($postBack) {
            if (!$this->validateForm()) {
                $this->EventCancelled = true; // Event cancelled
                $this->restoreFormValues(); // Restore form values
                if (IsApi()) {
                    $this->terminate();
                    return;
                } else {
                    $this->CurrentAction = "show"; // Form error, reset action
                }
            }
        }

        // Perform current action
        switch ($this->CurrentAction) {
            case "copy": // Copy an existing record
                if (!$loaded) { // Record not loaded
                    if ($this->getFailureMessage() == "") {
                        $this->setFailureMessage($Language->phrase("NoRecord")); // No record found
                    }
                    $this->terminate("kuliahlist"); // No matching record, return to list
                    return;
                }
                break;
            case "insert": // Add new record
                $this->SendEmail = true; // Send email on add success
                if ($this->addRow($this->OldRecordset)) { // Add successful
                    if ($this->getSuccessMessage() == "" && Post("addopt") != "1") { // Skip success message for addopt (done in JavaScript)
                        $this->setSuccessMessage($Language->phrase("AddSuccess")); // Set up success message
                    }
                    $returnUrl = $this->getReturnUrl();
                    if (GetPageName($returnUrl) == "kuliahlist") {
                        $returnUrl = $this->addMasterUrl($returnUrl); // List page, return to List page with correct master key if necessary
                    } elseif (GetPageName($returnUrl) == "kuliahview") {
                        $returnUrl = $this->getViewUrl(); // View page, return to View page with keyurl directly
                    }
                    if (IsApi()) { // Return to caller
                        $this->terminate(true);
                        return;
                    } else {
                        $this->terminate($returnUrl);
                        return;
                    }
                } elseif (IsApi()) { // API request, return
                    $this->terminate();
                    return;
                } else {
                    $this->EventCancelled = true; // Event cancelled
                    $this->restoreFormValues(); // Add failed, restore form values
                }
        }

        // Set up Breadcrumb
        $this->setupBreadcrumb();

        // Render row based on row type
        if ($this->isConfirm()) { // Confirm page
            $this->RowType = ROWTYPE_VIEW; // Render view type
        } else {
            $this->RowType = ROWTYPE_ADD; // Render add type
        }

        // Render row
        $this->resetAttributes();
        $this->renderRow();

        // Set LoginStatus / Page_Rendering / Page_Render
        if (!IsApi() && !$this->isTerminated()) {
            // Pass table and field properties to client side
            $this->toClientVar(["tableCaption"], ["caption", "Visible", "Required", "IsInvalid", "Raw"]);

            // Setup login status
            SetupLoginStatus();

            // Pass login status to client side
            SetClientVar("login", LoginStatus());

            // Global Page Rendering event (in userfn*.php)
            Page_Rendering();

            // Page Render event
            if (method_exists($this, "pageRender")) {
                $this->pageRender();
            }
        }
    }

    // Get upload files
    protected function getUploadFiles()
    {
        global $CurrentForm, $Language;
    }

    // Load default values
    protected function loadDefaultValues()
    {
        $this->id_kuliah->CurrentValue = null;
        $this->id_kuliah->OldValue = $this->id_kuliah->CurrentValue;
        $this->id_mk->CurrentValue = null;
        $this->id_mk->OldValue = $this->id_mk->CurrentValue;
        $this->id_mhs->CurrentValue = null;
        $this->id_mhs->OldValue = $this->id_mhs->CurrentValue;
        $this->tahun_ajaran->CurrentValue = null;
        $this->tahun_ajaran->OldValue = $this->tahun_ajaran->CurrentValue;
    }

    // Load form values
    protected function loadFormValues()
    {
        // Load from form
        global $CurrentForm;

        // Check field name 'id_mk' first before field var 'x_id_mk'
        $val = $CurrentForm->hasValue("id_mk") ? $CurrentForm->getValue("id_mk") : $CurrentForm->getValue("x_id_mk");
        if (!$this->id_mk->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->id_mk->Visible = false; // Disable update for API request
            } else {
                $this->id_mk->setFormValue($val);
            }
        }

        // Check field name 'id_mhs' first before field var 'x_id_mhs'
        $val = $CurrentForm->hasValue("id_mhs") ? $CurrentForm->getValue("id_mhs") : $CurrentForm->getValue("x_id_mhs");
        if (!$this->id_mhs->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->id_mhs->Visible = false; // Disable update for API request
            } else {
                $this->id_mhs->setFormValue($val);
            }
        }

        // Check field name 'tahun_ajaran' first before field var 'x_tahun_ajaran'
        $val = $CurrentForm->hasValue("tahun_ajaran") ? $CurrentForm->getValue("tahun_ajaran") : $CurrentForm->getValue("x_tahun_ajaran");
        if (!$this->tahun_ajaran->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->tahun_ajaran->Visible = false; // Disable update for API request
            } else {
                $this->tahun_ajaran->setFormValue($val);
            }
        }

        // Check field name 'id_kuliah' first before field var 'x_id_kuliah'
        $val = $CurrentForm->hasValue("id_kuliah") ? $CurrentForm->getValue("id_kuliah") : $CurrentForm->getValue("x_id_kuliah");
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        $this->id_mk->CurrentValue = $this->id_mk->FormValue;
        $this->id_mhs->CurrentValue = $this->id_mhs->FormValue;
        $this->tahun_ajaran->CurrentValue = $this->tahun_ajaran->FormValue;
    }

    /**
     * Load row based on key values
     *
     * @return void
     */
    public function loadRow()
    {
        global $Security, $Language;
        $filter = $this->getRecordFilter();

        // Call Row Selecting event
        $this->rowSelecting($filter);

        // Load SQL based on filter
        $this->CurrentFilter = $filter;
        $sql = $this->getCurrentSql();
        $conn = $this->getConnection();
        $res = false;
        $row = $conn->fetchAssoc($sql);
        if ($row) {
            $res = true;
            $this->loadRowValues($row); // Load row values
        }
        return $res;
    }

    /**
     * Load row values from recordset or record
     *
     * @param Recordset|array $rs Record
     * @return void
     */
    public function loadRowValues($rs = null)
    {
        if (is_array($rs)) {
            $row = $rs;
        } elseif ($rs && property_exists($rs, "fields")) { // Recordset
            $row = $rs->fields;
        } else {
            $row = $this->newRow();
        }

        // Call Row Selected event
        $this->rowSelected($row);
        if (!$rs) {
            return;
        }
        $this->id_kuliah->setDbValue($row['id_kuliah']);
        $this->id_mk->setDbValue($row['id_mk']);
        $this->id_mhs->setDbValue($row['id_mhs']);
        $this->tahun_ajaran->setDbValue($row['tahun_ajaran']);
    }

    // Return a row with default values
    protected function newRow()
    {
        $this->loadDefaultValues();
        $row = [];
        $row['id_kuliah'] = $this->id_kuliah->CurrentValue;
        $row['id_mk'] = $this->id_mk->CurrentValue;
        $row['id_mhs'] = $this->id_mhs->CurrentValue;
        $row['tahun_ajaran'] = $this->tahun_ajaran->CurrentValue;
        return $row;
    }

    // Load old record
    protected function loadOldRecord()
    {
        // Load old record
        $this->OldRecordset = null;
        $validKey = $this->OldKey != "";
        if ($validKey) {
            $this->CurrentFilter = $this->getRecordFilter();
            $sql = $this->getCurrentSql();
            $conn = $this->getConnection();
            $this->OldRecordset = LoadRecordset($sql, $conn);
        }
        $this->loadRowValues($this->OldRecordset); // Load row values
        return $validKey;
    }

    // Render row values based on field settings
    public function renderRow()
    {
        global $Security, $Language, $CurrentLanguage;

        // Initialize URLs

        // Call Row_Rendering event
        $this->rowRendering();

        // Common render codes for all row types

        // id_kuliah

        // id_mk

        // id_mhs

        // tahun_ajaran
        if ($this->RowType == ROWTYPE_VIEW) {
            // id_kuliah
            $this->id_kuliah->ViewValue = $this->id_kuliah->CurrentValue;
            $this->id_kuliah->ViewCustomAttributes = "";

            // id_mk
            $this->id_mk->ViewValue = $this->id_mk->CurrentValue;
            $curVal = trim(strval($this->id_mk->CurrentValue));
            if ($curVal != "") {
                $this->id_mk->ViewValue = $this->id_mk->lookupCacheOption($curVal);
                if ($this->id_mk->ViewValue === null) { // Lookup from database
                    $filterWrk = "`id_mk`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                    $sqlWrk = $this->id_mk->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $rswrk = Conn()->executeQuery($sqlWrk)->fetchAll(\PDO::FETCH_BOTH);
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->id_mk->Lookup->renderViewRow($rswrk[0]);
                        $this->id_mk->ViewValue = $this->id_mk->displayValue($arwrk);
                    } else {
                        $this->id_mk->ViewValue = $this->id_mk->CurrentValue;
                    }
                }
            } else {
                $this->id_mk->ViewValue = null;
            }
            $this->id_mk->ViewCustomAttributes = "";

            // id_mhs
            $this->id_mhs->ViewValue = $this->id_mhs->CurrentValue;
            $curVal = trim(strval($this->id_mhs->CurrentValue));
            if ($curVal != "") {
                $this->id_mhs->ViewValue = $this->id_mhs->lookupCacheOption($curVal);
                if ($this->id_mhs->ViewValue === null) { // Lookup from database
                    $filterWrk = "`id_mhs`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                    $sqlWrk = $this->id_mhs->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $rswrk = Conn()->executeQuery($sqlWrk)->fetchAll(\PDO::FETCH_BOTH);
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->id_mhs->Lookup->renderViewRow($rswrk[0]);
                        $this->id_mhs->ViewValue = $this->id_mhs->displayValue($arwrk);
                    } else {
                        $this->id_mhs->ViewValue = $this->id_mhs->CurrentValue;
                    }
                }
            } else {
                $this->id_mhs->ViewValue = null;
            }
            $this->id_mhs->ViewCustomAttributes = "";

            // tahun_ajaran
            $this->tahun_ajaran->ViewValue = $this->tahun_ajaran->CurrentValue;
            $this->tahun_ajaran->ViewCustomAttributes = "";

            // id_mk
            $this->id_mk->LinkCustomAttributes = "";
            $this->id_mk->HrefValue = "";
            $this->id_mk->TooltipValue = "";

            // id_mhs
            $this->id_mhs->LinkCustomAttributes = "";
            $this->id_mhs->HrefValue = "";
            $this->id_mhs->TooltipValue = "";

            // tahun_ajaran
            $this->tahun_ajaran->LinkCustomAttributes = "";
            $this->tahun_ajaran->HrefValue = "";
            $this->tahun_ajaran->TooltipValue = "";
        } elseif ($this->RowType == ROWTYPE_ADD) {
            // id_mk
            $this->id_mk->EditAttrs["class"] = "form-control";
            $this->id_mk->EditCustomAttributes = "";
            $this->id_mk->EditValue = HtmlEncode($this->id_mk->CurrentValue);
            $curVal = trim(strval($this->id_mk->CurrentValue));
            if ($curVal != "") {
                $this->id_mk->EditValue = $this->id_mk->lookupCacheOption($curVal);
                if ($this->id_mk->EditValue === null) { // Lookup from database
                    $filterWrk = "`id_mk`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                    $sqlWrk = $this->id_mk->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $rswrk = Conn()->executeQuery($sqlWrk)->fetchAll(\PDO::FETCH_BOTH);
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->id_mk->Lookup->renderViewRow($rswrk[0]);
                        $this->id_mk->EditValue = $this->id_mk->displayValue($arwrk);
                    } else {
                        $this->id_mk->EditValue = HtmlEncode($this->id_mk->CurrentValue);
                    }
                }
            } else {
                $this->id_mk->EditValue = null;
            }
            $this->id_mk->PlaceHolder = RemoveHtml($this->id_mk->caption());

            // id_mhs
            $this->id_mhs->EditAttrs["class"] = "form-control";
            $this->id_mhs->EditCustomAttributes = "";
            if ($this->id_mhs->getSessionValue() != "") {
                $this->id_mhs->CurrentValue = GetForeignKeyValue($this->id_mhs->getSessionValue());
                $this->id_mhs->ViewValue = $this->id_mhs->CurrentValue;
                $curVal = trim(strval($this->id_mhs->CurrentValue));
                if ($curVal != "") {
                    $this->id_mhs->ViewValue = $this->id_mhs->lookupCacheOption($curVal);
                    if ($this->id_mhs->ViewValue === null) { // Lookup from database
                        $filterWrk = "`id_mhs`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                        $sqlWrk = $this->id_mhs->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                        $rswrk = Conn()->executeQuery($sqlWrk)->fetchAll(\PDO::FETCH_BOTH);
                        $ari = count($rswrk);
                        if ($ari > 0) { // Lookup values found
                            $arwrk = $this->id_mhs->Lookup->renderViewRow($rswrk[0]);
                            $this->id_mhs->ViewValue = $this->id_mhs->displayValue($arwrk);
                        } else {
                            $this->id_mhs->ViewValue = $this->id_mhs->CurrentValue;
                        }
                    }
                } else {
                    $this->id_mhs->ViewValue = null;
                }
                $this->id_mhs->ViewCustomAttributes = "";
            } else {
                $this->id_mhs->EditValue = HtmlEncode($this->id_mhs->CurrentValue);
                $curVal = trim(strval($this->id_mhs->CurrentValue));
                if ($curVal != "") {
                    $this->id_mhs->EditValue = $this->id_mhs->lookupCacheOption($curVal);
                    if ($this->id_mhs->EditValue === null) { // Lookup from database
                        $filterWrk = "`id_mhs`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                        $sqlWrk = $this->id_mhs->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                        $rswrk = Conn()->executeQuery($sqlWrk)->fetchAll(\PDO::FETCH_BOTH);
                        $ari = count($rswrk);
                        if ($ari > 0) { // Lookup values found
                            $arwrk = $this->id_mhs->Lookup->renderViewRow($rswrk[0]);
                            $this->id_mhs->EditValue = $this->id_mhs->displayValue($arwrk);
                        } else {
                            $this->id_mhs->EditValue = HtmlEncode($this->id_mhs->CurrentValue);
                        }
                    }
                } else {
                    $this->id_mhs->EditValue = null;
                }
                $this->id_mhs->PlaceHolder = RemoveHtml($this->id_mhs->caption());
            }

            // tahun_ajaran
            $this->tahun_ajaran->EditAttrs["class"] = "form-control";
            $this->tahun_ajaran->EditCustomAttributes = "";
            if (!$this->tahun_ajaran->Raw) {
                $this->tahun_ajaran->CurrentValue = HtmlDecode($this->tahun_ajaran->CurrentValue);
            }
            $this->tahun_ajaran->EditValue = HtmlEncode($this->tahun_ajaran->CurrentValue);
            $this->tahun_ajaran->PlaceHolder = RemoveHtml($this->tahun_ajaran->caption());

            // Add refer script

            // id_mk
            $this->id_mk->LinkCustomAttributes = "";
            $this->id_mk->HrefValue = "";

            // id_mhs
            $this->id_mhs->LinkCustomAttributes = "";
            $this->id_mhs->HrefValue = "";

            // tahun_ajaran
            $this->tahun_ajaran->LinkCustomAttributes = "";
            $this->tahun_ajaran->HrefValue = "";
        }
        if ($this->RowType == ROWTYPE_ADD || $this->RowType == ROWTYPE_EDIT || $this->RowType == ROWTYPE_SEARCH) { // Add/Edit/Search row
            $this->setupFieldTitles();
        }

        // Call Row Rendered event
        if ($this->RowType != ROWTYPE_AGGREGATEINIT) {
            $this->rowRendered();
        }
    }

    // Validate form
    protected function validateForm()
    {
        global $Language;

        // Check if validation required
        if (!Config("SERVER_VALIDATE")) {
            return true;
        }
        if ($this->id_mk->Required) {
            if (!$this->id_mk->IsDetailKey && EmptyValue($this->id_mk->FormValue)) {
                $this->id_mk->addErrorMessage(str_replace("%s", $this->id_mk->caption(), $this->id_mk->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->id_mk->FormValue)) {
            $this->id_mk->addErrorMessage($this->id_mk->getErrorMessage(false));
        }
        if ($this->id_mhs->Required) {
            if (!$this->id_mhs->IsDetailKey && EmptyValue($this->id_mhs->FormValue)) {
                $this->id_mhs->addErrorMessage(str_replace("%s", $this->id_mhs->caption(), $this->id_mhs->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->id_mhs->FormValue)) {
            $this->id_mhs->addErrorMessage($this->id_mhs->getErrorMessage(false));
        }
        if ($this->tahun_ajaran->Required) {
            if (!$this->tahun_ajaran->IsDetailKey && EmptyValue($this->tahun_ajaran->FormValue)) {
                $this->tahun_ajaran->addErrorMessage(str_replace("%s", $this->tahun_ajaran->caption(), $this->tahun_ajaran->RequiredErrorMessage));
            }
        }

        // Return validate result
        $validateForm = !$this->hasInvalidFields();

        // Call Form_CustomValidate event
        $formCustomError = "";
        $validateForm = $validateForm && $this->formCustomValidate($formCustomError);
        if ($formCustomError != "") {
            $this->setFailureMessage($formCustomError);
        }
        return $validateForm;
    }

    // Add record
    protected function addRow($rsold = null)
    {
        global $Language, $Security;
        $conn = $this->getConnection();

        // Load db values from rsold
        $this->loadDbValues($rsold);
        if ($rsold) {
        }
        $rsnew = [];

        // id_mk
        $this->id_mk->setDbValueDef($rsnew, $this->id_mk->CurrentValue, 0, false);

        // id_mhs
        $this->id_mhs->setDbValueDef($rsnew, $this->id_mhs->CurrentValue, 0, false);

        // tahun_ajaran
        $this->tahun_ajaran->setDbValueDef($rsnew, $this->tahun_ajaran->CurrentValue, "", false);

        // Call Row Inserting event
        $insertRow = $this->rowInserting($rsold, $rsnew);
        $addRow = false;
        if ($insertRow) {
            try {
                $addRow = $this->insert($rsnew);
            } catch (\Exception $e) {
                $this->setFailureMessage($e->getMessage());
            }
            if ($addRow) {
            }
        } else {
            if ($this->getSuccessMessage() != "" || $this->getFailureMessage() != "") {
                // Use the message, do nothing
            } elseif ($this->CancelMessage != "") {
                $this->setFailureMessage($this->CancelMessage);
                $this->CancelMessage = "";
            } else {
                $this->setFailureMessage($Language->phrase("InsertCancelled"));
            }
            $addRow = false;
        }
        if ($addRow) {
            // Call Row Inserted event
            $this->rowInserted($rsold, $rsnew);
        }

        // Clean upload path if any
        if ($addRow) {
        }

        // Write JSON for API request
        if (IsApi() && $addRow) {
            $row = $this->getRecordsFromRecordset([$rsnew], true);
            WriteJson(["success" => true, $this->TableVar => $row]);
        }
        return $addRow;
    }

    // Set up master/detail based on QueryString
    protected function setupMasterParms()
    {
        $validMaster = false;
        // Get the keys for master table
        if (($master = Get(Config("TABLE_SHOW_MASTER"), Get(Config("TABLE_MASTER")))) !== null) {
            $masterTblVar = $master;
            if ($masterTblVar == "") {
                $validMaster = true;
                $this->DbMasterFilter = "";
                $this->DbDetailFilter = "";
            }
            if ($masterTblVar == "v_mhs") {
                $validMaster = true;
                $masterTbl = Container("v_mhs");
                if (($parm = Get("fk_id_mhs", Get("id_mhs"))) !== null) {
                    $masterTbl->id_mhs->setQueryStringValue($parm);
                    $this->id_mhs->setQueryStringValue($masterTbl->id_mhs->QueryStringValue);
                    $this->id_mhs->setSessionValue($this->id_mhs->QueryStringValue);
                    if (!is_numeric($masterTbl->id_mhs->QueryStringValue)) {
                        $validMaster = false;
                    }
                } else {
                    $validMaster = false;
                }
            }
        } elseif (($master = Post(Config("TABLE_SHOW_MASTER"), Post(Config("TABLE_MASTER")))) !== null) {
            $masterTblVar = $master;
            if ($masterTblVar == "") {
                    $validMaster = true;
                    $this->DbMasterFilter = "";
                    $this->DbDetailFilter = "";
            }
            if ($masterTblVar == "v_mhs") {
                $validMaster = true;
                $masterTbl = Container("v_mhs");
                if (($parm = Post("fk_id_mhs", Post("id_mhs"))) !== null) {
                    $masterTbl->id_mhs->setFormValue($parm);
                    $this->id_mhs->setFormValue($masterTbl->id_mhs->FormValue);
                    $this->id_mhs->setSessionValue($this->id_mhs->FormValue);
                    if (!is_numeric($masterTbl->id_mhs->FormValue)) {
                        $validMaster = false;
                    }
                } else {
                    $validMaster = false;
                }
            }
        }
        if ($validMaster) {
            // Save current master table
            $this->setCurrentMasterTable($masterTblVar);

            // Reset start record counter (new master key)
            if (!$this->isAddOrEdit()) {
                $this->StartRecord = 1;
                $this->setStartRecordNumber($this->StartRecord);
            }

            // Clear previous master key from Session
            if ($masterTblVar != "v_mhs") {
                if ($this->id_mhs->CurrentValue == "") {
                    $this->id_mhs->setSessionValue("");
                }
            }
        }
        $this->DbMasterFilter = $this->getMasterFilter(); // Get master filter
        $this->DbDetailFilter = $this->getDetailFilter(); // Get detail filter
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb()
    {
        global $Breadcrumb, $Language;
        $Breadcrumb = new Breadcrumb("index");
        $url = CurrentUrl();
        $Breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("kuliahlist"), "", $this->TableVar, true);
        $pageId = ($this->isCopy()) ? "Copy" : "Add";
        $Breadcrumb->add("add", $pageId, $url);
    }

    // Setup lookup options
    public function setupLookupOptions($fld)
    {
        if ($fld->Lookup !== null && $fld->Lookup->Options === null) {
            // Get default connection and filter
            $conn = $this->getConnection();
            $lookupFilter = "";

            // No need to check any more
            $fld->Lookup->Options = [];

            // Set up lookup SQL and connection
            switch ($fld->FieldVar) {
                case "x_id_mk":
                    break;
                case "x_id_mhs":
                    break;
                default:
                    $lookupFilter = "";
                    break;
            }

            // Always call to Lookup->getSql so that user can setup Lookup->Options in Lookup_Selecting server event
            $sql = $fld->Lookup->getSql(false, "", $lookupFilter, $this);

            // Set up lookup cache
            if ($fld->UseLookupCache && $sql != "" && count($fld->Lookup->Options) == 0) {
                $totalCnt = $this->getRecordCount($sql, $conn);
                if ($totalCnt > $fld->LookupCacheCount) { // Total count > cache count, do not cache
                    return;
                }
                $rows = $conn->executeQuery($sql)->fetchAll(\PDO::FETCH_BOTH);
                $ar = [];
                foreach ($rows as $row) {
                    $row = $fld->Lookup->renderViewRow($row);
                    $ar[strval($row[0])] = $row;
                }
                $fld->Lookup->Options = $ar;
            }
        }
    }

    // Page Load event
    public function pageLoad()
    {
        //Log("Page Load");
    }

    // Page Unload event
    public function pageUnload()
    {
        //Log("Page Unload");
    }

    // Page Redirecting event
    public function pageRedirecting(&$url)
    {
        // Example:
        //$url = "your URL";
    }

    // Message Showing event
    // $type = ''|'success'|'failure'|'warning'
    public function messageShowing(&$msg, $type)
    {
        if ($type == 'success') {
            //$msg = "your success message";
        } elseif ($type == 'failure') {
            //$msg = "your failure message";
        } elseif ($type == 'warning') {
            //$msg = "your warning message";
        } else {
            //$msg = "your message";
        }
    }

    // Page Render event
    public function pageRender()
    {
        //Log("Page Render");
    }

    // Page Data Rendering event
    public function pageDataRendering(&$header)
    {
        // Example:
        //$header = "your header";
    }

    // Page Data Rendered event
    public function pageDataRendered(&$footer)
    {
        // Example:
        //$footer = "your footer";
    }

    // Form Custom Validate event
    public function formCustomValidate(&$customError)
    {
        // Return error message in CustomError
        return true;
    }
}
