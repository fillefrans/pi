; -- 64BitTwoArch.iss --
; Demonstrates how to install a program built for two different
; architectures (x86 and x64) using a single installer.

; SEE THE DOCUMENTATION FOR DETAILS ON CREATING .ISS SCRIPT FILES!

[Setup]
AppName=Psmith
AppVersion=1
DefaultDirName={pf}\Psmith
UninstallDisplayIcon={app}\psmith.exe
Compression=lzma2/ultra64
SolidCompression=True
OutputDir=D:\work\TT\pi\bin\tracker\setup
AppCopyright=Views AS
PrivilegesRequired=lowest
AppId={{7DABEDA8-7644-49D6-9DF5-DF801EA58227}
AppPublisher=Views AS
UninstallDisplayName=Psmith
VersionInfoVersion=1
VersionInfoCopyright=Views AS
VersionInfoProductName=Psmith
VersionInfoProductVersion=1
SourceDir=D:\work\TT\pi\bin\tracker
InternalCompressLevel=ultra
AppMutex=Psmith

ArchitecturesInstallIn64BitMode=x64
; Note: We don't set ProcessorsAllowed because we want this
; installation to run on all architectures (including Itanium,
; since it's capable of running 32-bit code too).
WizardImageFile=D:\work\TT\pi\cli\assets\ico\psmith-install-image.bmp
WizardSmallImageFile=D:\work\TT\pi\cli\assets\ico\psmith-install-image-small.bmp
WizardImageBackColor=clWhite
DisableWelcomePage=True
DisableReadyPage=True
DisableReadyMemo=True
DisableFinishedPage=True
AppPublisherURL=http://kromaviews.no/psmith
AppContact=Johan Telstad
UserInfoPage=False
VersionInfoCompany=Views AS
SetupIconFile=D:\work\TT\pi\cli\assets\ico\psmith-ico-48.ico
AppendDefaultDirName=False
DisableProgramGroupPage=yes

[Files]
; The x64 binary
Source: "D:\work\TT\pi\bin\tracker\psmith32.exe"; DestDir: "{app}"; DestName: "psmith.exe"; Flags: solidbreak promptifolder 64bit recursesubdirs; Check: Is64BitInstallMode

; The x86 binary
Source: "D:\work\TT\pi\bin\tracker\psmith32.exe"; DestDir: "{app}"; DestName: "psmith.exe"; Flags: solidbreak promptifolder 32bit replacesameversion recursesubdirs; Check: not Is64BitInstallMode

; The zlib library, required for gzip compression
Source: "D:\work\TT\pi\bin\tracker\zlib1.dll"; DestDir: "{app}"

; Open source fonts
Source: "C:\Windows\Fonts\Ubuntu-B.ttf"; DestDir: "{fonts}"; Flags: onlyifdoesntexist uninsneveruninstall; FontInstall: "Ubuntu Bold"
Source: "C:\Windows\Fonts\Ubuntu-BI.ttf"; DestDir: "{fonts}"; Flags: onlyifdoesntexist uninsneveruninstall; FontInstall: "Ubuntu Bold Italic"
Source: "C:\Windows\Fonts\Ubuntu-C.ttf"; DestDir: "{fonts}"; Flags: onlyifdoesntexist uninsneveruninstall; FontInstall: "Ubuntu Condensed"
Source: "C:\Windows\Fonts\Ubuntu-L.ttf"; DestDir: "{fonts}"; Flags: onlyifdoesntexist uninsneveruninstall; FontInstall: "Ubuntu Light"
Source: "C:\Windows\Fonts\Ubuntu-M.ttf"; DestDir: "{fonts}"; Flags: onlyifdoesntexist uninsneveruninstall; FontInstall: "Ubuntu Light Italic"
Source: "C:\Windows\Fonts\Ubuntu-MI.ttf"; DestDir: "{fonts}"; Flags: onlyifdoesntexist uninsneveruninstall; FontInstall: "Ubuntu Medium Italic"
Source: "C:\Windows\Fonts\Ubuntu-R.ttf"; DestDir: "{fonts}"; Flags: onlyifdoesntexist uninsneveruninstall; FontInstall: "Ubuntu"
Source: "C:\Windows\Fonts\UbuntuMono-B.ttf"; DestDir: "{fonts}"; Flags: onlyifdoesntexist uninsneveruninstall; FontInstall: "Ubuntu Italic"
Source: "C:\Windows\Fonts\OpenSans-Bold_0.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Bold Italic"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-Bold_1.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Bold"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-CondBold.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Condensed Bold"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-CondLight.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Condensed Light"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-ExtraBoldItalic.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Condensed Light Italic"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-ExtraBoldItalic_0.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Extrabold Italic"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-Italic_0.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Extrabold"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-LightItalic.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Italic"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-Light_0.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Light Italic"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-Regular_0.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Light"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-SemiboldItalic.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-Semibold_0.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Semibold Italic"; Flags: onlyifdoesntexist uninsneveruninstall
Source: "C:\Windows\Fonts\OpenSans-Semibold_1.ttf"; DestDir: "{fonts}"; FontInstall: "Open Sans Semibold"; Flags: onlyifdoesntexist uninsneveruninstall

[Icons]
Name: "{group}\Psmith"; Filename: "{app}\psmith.exe"; IconFilename: "{app}\psmith.exe"; IconIndex: 0

; run on startup
Name: "{userstartup}\Psmith"; Filename: "{app}\psmith.exe"
Name: "{group}\{cm:UninstallProgram, Psmith}"; Filename: "{uninstallexe}"

[Run]
; when install completed, start application
Filename: "{app}\psmith.exe"; Flags: postinstall

[LangOptions]
LanguageID=$0809
DialogFontName=Ubuntu
WelcomeFontName=Ubuntu Light
TitleFontName=Ubuntu Condensed
CopyrightFontName=Ubuntu

[UninstallDelete]
Type: filesandordirs; Name: "{app}"


[INI]
Filename: "{app}\Psmith.url"; Section: "InternetShortcut"; Key: "URL"; String: "http://kromaviews.no/psmith/"
