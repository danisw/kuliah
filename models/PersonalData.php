<?php

namespace PHPMaker2021\project1;

use Doctrine\DBAL\ParameterType;

/**
 * Page class
 */
class PersonalData
{
    use MessagesTrait;

    // Page ID
    public $PageID = "personal_data";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Page object name
    public $PageObjName = "PersonalData";

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

        // Start timer
        $DebugTimer = Container("timer");

        // Debug message
        LoadDebugMessage();

        // Open connection
        $GLOBALS["Conn"] = $GLOBALS["Conn"] ?? GetConnection();

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

        // Global Page Unloaded event (in userfn*.php)
        Page_Unloaded();

        // Export

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
            SaveDebugMessage();
            Redirect(GetUrl($url));
        }
        return; // Return to controller
    }

    // Properties
    public $Password;

    /**
     * Page run
     *
     * @return void
     */
    public function run()
    {
        global $ExportType, $CustomExportType, $ExportFileName, $UserProfile, $Language, $Security, $CurrentForm,
            $Breadcrumb;

        // Create Password field object (used by validation only)
        $this->Password = new DbField("personaldata", "personaldata", "password", "password", "password", "", 202, 255, 0, false, "", false, false, false);
        $this->Password->EditAttrs->appendClass("form-control ew-control");

        // Global Page Loading event (in userfn*.php)
        Page_Loading();
        $Breadcrumb = new Breadcrumb("index");
        $Breadcrumb->add("personal_data", "PersonalDataTitle", CurrentUrl(), "ew-personal-data", "", true);
        $this->Heading = $Language->phrase("PersonalDataTitle");
        $cmd = Get("cmd", "");
        if (SameText($cmd, "Download")) {
            if ($this->personalDataResult()) {
                $this->terminate();
                return;
            }
        } elseif (SameText($cmd, "Delete") && IsPost()) {
            if ($this->deletePersonalData()) {
                $this->terminate(GetUrl("logout?deleted=1"));
                return;
            }
        }

        // Set LoginStatus / Page_Rendering / Page_Render
        if (!IsApi() && !$this->isTerminated()) {
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

    /**
     * Write personal data as JSON
     *
     * @return void
     */
    protected function personalDataResult()
    {
        global $UserTable;
        $result = [];
        $fldNames = [];
        $UserTable = Container("usertable");
        $filter = GetUserFilter(Config("LOGIN_USERNAME_FIELD_NAME"), CurrentUserName());
        $sql = $UserTable->getSql($filter);
        if ($row = Conn($UserTable->Dbid)->fetchAssoc($sql)) {
            foreach ($fldNames as $fldName) {
                if (array_key_exists($fldName, $row)) {
                    $result[$fldName] = GetUserInfo($fldName, $row);
                }
            }

            // Call PersonalData_Downloading event
            PersonalData_Downloading($result);
            $personalDataFileName = Get("_personaldatafilename", "personaldata.json");
            AddHeader("Content-Disposition", "attachment; filename=\"" . $personalDataFileName . "\"");
            WriteJson($result);
            return true;
        } else {
            $this->setFailureMessage($Language->phrase("NoRecord")); // No record found
            return false;
        }
    }

    /**
     * Delete personal data
     *
     * @return bool
     */
    protected function deletePersonalData()
    {
        global $UserTable, $Language;
        $UserTable = Container("usertable");
        $filter = GetUserFilter(Config("LOGIN_USERNAME_FIELD_NAME"), CurrentUserName());
        $sql = $UserTable->getSql($filter);
        $pwd = Post($this->Password->FieldVar, "");
        if ($row = Conn($UserTable->Dbid)->fetchAssoc($sql)) {
            if (ComparePassword(GetUserInfo(Config("LOGIN_PASSWORD_FIELD_NAME"), $row), $pwd)) {
                if (Config("DELETE_UPLOADED_FILES")) // Delete old files
                    $UserTable->deleteUploadedFiles($row);
                if ($UserTable->delete($row)) {
                    // Call PersonalData_Deleted event
                    PersonalData_Deleted($row);
                    return true;
                }
                $this->setFailureMessage($Language->phrase("PersonalDataDeleteFailure"));
                return false;
            } else {
                $this->Password->addErrorMessage($Language->phrase("InvalidPassword"));
                return false;
            }
        } else {
            $this->setFailureMessage($Language->phrase("NoRecord"));
            return false;
        }
    }
}
