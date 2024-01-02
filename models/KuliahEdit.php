<?php

namespace PHPMaker2021\project1;

use Doctrine\DBAL\ParameterType;

/**
 * Page class
 */
class KuliahEdit extends Kuliah
{
    use MessagesTrait;

    // Page ID
    public $PageID = "edit";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'kuliah';

    // Page object name
    public $PageObjName = "KuliahEdit";

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
    public $FormClassName = "ew-horizontal ew-form ew-edit-form";
    public $IsModal = false;
    public $IsMobileOrModal = false;
    public $DbMasterFilter;
    public $DbDetailFilter;
    public $HashValue; // Hash Value
    public $DisplayRecords = 1;
    public $StartRecord;
    public $StopRecord;
    public $TotalRecords = 0;
    public $RecordRange = 10;
    public $RecordCount;

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
        $this->id_kuliah->setVisibility();
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
        $this->FormClassName = "ew-form ew-edit-form ew-horizontal";
        $loaded = false;
        $postBack = false;

        // Set up current action and primary key
        if (IsApi()) {
            // Load key values
            $loaded = true;
            if (($keyValue = Get("id_kuliah") ?? Key(0) ?? Route(2)) !== null) {
                $this->id_kuliah->setQueryStringValue($keyValue);
                $this->id_kuliah->setOldValue($this->id_kuliah->QueryStringValue);
            } elseif (Post("id_kuliah") !== null) {
                $this->id_kuliah->setFormValue(Post("id_kuliah"));
                $this->id_kuliah->setOldValue($this->id_kuliah->FormValue);
            } else {
                $loaded = false; // Unable to load key
            }

            // Load record
            if ($loaded) {
                $loaded = $this->loadRow();
            }
            if (!$loaded) {
                $this->setFailureMessage($Language->phrase("NoRecord")); // Set no record message
                $this->terminate();
                return;
            }
            $this->CurrentAction = "update"; // Update record directly
            $this->OldKey = $this->getKey(true); // Get from CurrentValue
            $postBack = true;
        } else {
            if (Post("action") !== null) {
                $this->CurrentAction = Post("action"); // Get action code
                if (!$this->isShow()) { // Not reload record, handle as postback
                    $postBack = true;
                }

                // Get key from Form
                $this->setKey(Post($this->OldKeyName), $this->isShow());
            } else {
                $this->CurrentAction = "show"; // Default action is display

                // Load key from QueryString
                $loadByQuery = false;
                if (($keyValue = Get("id_kuliah") ?? Route("id_kuliah")) !== null) {
                    $this->id_kuliah->setQueryStringValue($keyValue);
                    $loadByQuery = true;
                } else {
                    $this->id_kuliah->CurrentValue = null;
                }
            }

            // Set up master detail parameters
            $this->setupMasterParms();

            // Load recordset
            if ($this->isShow()) {
                // Load current record
                $loaded = $this->loadRow();
                $this->OldKey = $loaded ? $this->getKey(true) : ""; // Get from CurrentValue
            }
        }

        // Process form if post back
        if ($postBack) {
            $this->loadFormValues(); // Get form values
        }

        // Validate form if post back
        if ($postBack) {
            if (!$this->validateForm()) {
                $this->EventCancelled = true; // Event cancelled
                $this->restoreFormValues();
                if (IsApi()) {
                    $this->terminate();
                    return;
                } else {
                    $this->CurrentAction = ""; // Form error, reset action
                }
            }
        }

        // Perform current action
        switch ($this->CurrentAction) {
            case "show": // Get a record to display
                if (!$loaded) { // Load record based on key
                    if ($this->getFailureMessage() == "") {
                        $this->setFailureMessage($Language->phrase("NoRecord")); // No record found
                    }
                    $this->terminate("kuliahlist"); // No matching record, return to list
                    return;
                }
                break;
            case "update": // Update
                $returnUrl = $this->getReturnUrl();
                if (GetPageName($returnUrl) == "kuliahlist") {
                    $returnUrl = $this->addMasterUrl($returnUrl); // List page, return to List page with correct master key if necessary
                }
                $this->SendEmail = true; // Send email on update success
                if ($this->editRow()) { // Update record based on key
                    if ($this->getSuccessMessage() == "") {
                        $this->setSuccessMessage($Language->phrase("UpdateSuccess")); // Update success
                    }
                    if (IsApi()) {
                        $this->terminate(true);
                        return;
                    } else {
                        $this->terminate($returnUrl); // Return to caller
                        return;
                    }
                } elseif (IsApi()) { // API request, return
                    $this->terminate();
                    return;
                } elseif ($this->getFailureMessage() == $Language->phrase("NoRecord")) {
                    $this->terminate($returnUrl); // Return to caller
                    return;
                } else {
                    $this->EventCancelled = true; // Event cancelled
                    $this->restoreFormValues(); // Restore form values if update failed
                }
        }

        // Set up Breadcrumb
        $this->setupBreadcrumb();

        // Render the record
        if ($this->isConfirm()) { // Confirm page
            $this->RowType = ROWTYPE_VIEW; // Render as View
        } else {
            $this->RowType = ROWTYPE_EDIT; // Render as Edit
        }
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

    // Load form values
    protected function loadFormValues()
    {
        // Load from form
        global $CurrentForm;

        // Check field name 'id_kuliah' first before field var 'x_id_kuliah'
        $val = $CurrentForm->hasValue("id_kuliah") ? $CurrentForm->getValue("id_kuliah") : $CurrentForm->getValue("x_id_kuliah");
        if (!$this->id_kuliah->IsDetailKey) {
            $this->id_kuliah->setFormValue($val);
        }

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
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        $this->id_kuliah->CurrentValue = $this->id_kuliah->FormValue;
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
        $row = [];
        $row['id_kuliah'] = null;
        $row['id_mk'] = null;
        $row['id_mhs'] = null;
        $row['tahun_ajaran'] = null;
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

            // id_kuliah
            $this->id_kuliah->LinkCustomAttributes = "";
            $this->id_kuliah->HrefValue = "";
            $this->id_kuliah->TooltipValue = "";

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
        } elseif ($this->RowType == ROWTYPE_EDIT) {
            // id_kuliah
            $this->id_kuliah->EditAttrs["class"] = "form-control";
            $this->id_kuliah->EditCustomAttributes = "";
            $this->id_kuliah->EditValue = $this->id_kuliah->CurrentValue;
            $this->id_kuliah->ViewCustomAttributes = "";

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

            // Edit refer script

            // id_kuliah
            $this->id_kuliah->LinkCustomAttributes = "";
            $this->id_kuliah->HrefValue = "";

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
        if ($this->id_kuliah->Required) {
            if (!$this->id_kuliah->IsDetailKey && EmptyValue($this->id_kuliah->FormValue)) {
                $this->id_kuliah->addErrorMessage(str_replace("%s", $this->id_kuliah->caption(), $this->id_kuliah->RequiredErrorMessage));
            }
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

    // Update record based on key values
    protected function editRow()
    {
        global $Security, $Language;
        $oldKeyFilter = $this->getRecordFilter();
        $filter = $this->applyUserIDFilters($oldKeyFilter);
        $conn = $this->getConnection();
        $this->CurrentFilter = $filter;
        $sql = $this->getCurrentSql();
        $rsold = $conn->fetchAssoc($sql);
        $editRow = false;
        if (!$rsold) {
            $this->setFailureMessage($Language->phrase("NoRecord")); // Set no record message
            $editRow = false; // Update Failed
        } else {
            // Save old values
            $this->loadDbValues($rsold);
            $rsnew = [];

            // id_mk
            $this->id_mk->setDbValueDef($rsnew, $this->id_mk->CurrentValue, 0, $this->id_mk->ReadOnly);

            // id_mhs
            if ($this->id_mhs->getSessionValue() != "") {
                $this->id_mhs->ReadOnly = true;
            }
            $this->id_mhs->setDbValueDef($rsnew, $this->id_mhs->CurrentValue, 0, $this->id_mhs->ReadOnly);

            // tahun_ajaran
            $this->tahun_ajaran->setDbValueDef($rsnew, $this->tahun_ajaran->CurrentValue, "", $this->tahun_ajaran->ReadOnly);

            // Call Row Updating event
            $updateRow = $this->rowUpdating($rsold, $rsnew);
            if ($updateRow) {
                if (count($rsnew) > 0) {
                    try {
                        $editRow = $this->update($rsnew, "", $rsold);
                    } catch (\Exception $e) {
                        $this->setFailureMessage($e->getMessage());
                    }
                } else {
                    $editRow = true; // No field to update
                }
                if ($editRow) {
                }
            } else {
                if ($this->getSuccessMessage() != "" || $this->getFailureMessage() != "") {
                    // Use the message, do nothing
                } elseif ($this->CancelMessage != "") {
                    $this->setFailureMessage($this->CancelMessage);
                    $this->CancelMessage = "";
                } else {
                    $this->setFailureMessage($Language->phrase("UpdateCancelled"));
                }
                $editRow = false;
            }
        }

        // Call Row_Updated event
        if ($editRow) {
            $this->rowUpdated($rsold, $rsnew);
        }

        // Clean upload path if any
        if ($editRow) {
        }

        // Write JSON for API request
        if (IsApi() && $editRow) {
            $row = $this->getRecordsFromRecordset([$rsnew], true);
            WriteJson(["success" => true, $this->TableVar => $row]);
        }
        return $editRow;
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
            $this->setSessionWhere($this->getDetailFilter());

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
        $pageId = "edit";
        $Breadcrumb->add("edit", $pageId, $url);
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

    // Set up starting record parameters
    public function setupStartRecord()
    {
        if ($this->DisplayRecords == 0) {
            return;
        }
        if ($this->isPageRequest()) { // Validate request
            $startRec = Get(Config("TABLE_START_REC"));
            $pageNo = Get(Config("TABLE_PAGE_NO"));
            if ($pageNo !== null) { // Check for "pageno" parameter first
                if (is_numeric($pageNo)) {
                    $this->StartRecord = ($pageNo - 1) * $this->DisplayRecords + 1;
                    if ($this->StartRecord <= 0) {
                        $this->StartRecord = 1;
                    } elseif ($this->StartRecord >= (int)(($this->TotalRecords - 1) / $this->DisplayRecords) * $this->DisplayRecords + 1) {
                        $this->StartRecord = (int)(($this->TotalRecords - 1) / $this->DisplayRecords) * $this->DisplayRecords + 1;
                    }
                    $this->setStartRecordNumber($this->StartRecord);
                }
            } elseif ($startRec !== null) { // Check for "start" parameter
                $this->StartRecord = $startRec;
                $this->setStartRecordNumber($this->StartRecord);
            }
        }
        $this->StartRecord = $this->getStartRecordNumber();

        // Check if correct start record counter
        if (!is_numeric($this->StartRecord) || $this->StartRecord == "") { // Avoid invalid start record counter
            $this->StartRecord = 1; // Reset start record counter
            $this->setStartRecordNumber($this->StartRecord);
        } elseif ($this->StartRecord > $this->TotalRecords) { // Avoid starting record > total records
            $this->StartRecord = (int)(($this->TotalRecords - 1) / $this->DisplayRecords) * $this->DisplayRecords + 1; // Point to last page first record
            $this->setStartRecordNumber($this->StartRecord);
        } elseif (($this->StartRecord - 1) % $this->DisplayRecords != 0) {
            $this->StartRecord = (int)(($this->StartRecord - 1) / $this->DisplayRecords) * $this->DisplayRecords + 1; // Point to page boundary
            $this->setStartRecordNumber($this->StartRecord);
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
