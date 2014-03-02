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
DisableProgramGroupPage=auto
AlwaysUsePersonalGroup=True
AlwaysShowGroupOnReadyPage=True
AlwaysShowDirOnReadyPage=True
DefaultGroupName=Psmith

[Files]
; The x64 binary
Source: "D:\work\TT\pi\bin\tracker\psmith32.exe"; DestDir: "{app}"; DestName: "psmith.exe"; Flags: solidbreak promptifolder 64bit recursesubdirs; Check: Is64BitInstallMode

; The x86 binary
Source: "D:\work\TT\pi\bin\tracker\psmith32.exe"; DestDir: "{app}"; DestName: "psmith.exe"; Flags: solidbreak promptifolder 32bit replacesameversion recursesubdirs; Check: not Is64BitInstallMode

; The zlib library, required for gzip compression
Source: "D:\work\TT\pi\bin\tracker\zlib1.dll"; DestDir: "{app}"

; Open source fonts
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
Filename: "{app}\psmith.exe"; Flags: postinstall nowait runascurrentuser

[LangOptions]
DialogFontName=Open Sans
WelcomeFontName=Open Sans Light
TitleFontName=Open Sans Semibold
CopyrightFontName=Open Sans Condensed Light

[UninstallDelete]
Type: filesandordirs; Name: "{app}"


[INI]
Filename: "{app}\Psmith.url"; Section: "InternetShortcut"; Key: "URL"; String: "http://kromaviews.no/psmith/"

[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"
Name: "brazilianportuguese"; MessagesFile: "compiler:Languages\BrazilianPortuguese.isl"
Name: "catalan"; MessagesFile: "compiler:Languages\Catalan.isl"
Name: "corsican"; MessagesFile: "compiler:Languages\Corsican.isl"
Name: "czech"; MessagesFile: "compiler:Languages\Czech.isl"
Name: "danish"; MessagesFile: "compiler:Languages\Danish.isl"
Name: "dutch"; MessagesFile: "compiler:Languages\Dutch.isl"
Name: "finnish"; MessagesFile: "compiler:Languages\Finnish.isl"
Name: "french"; MessagesFile: "compiler:Languages\French.isl"
Name: "german"; MessagesFile: "compiler:Languages\German.isl"
Name: "greek"; MessagesFile: "compiler:Languages\Greek.isl"
Name: "hebrew"; MessagesFile: "compiler:Languages\Hebrew.isl"
Name: "hungarian"; MessagesFile: "compiler:Languages\Hungarian.isl"
Name: "italian"; MessagesFile: "compiler:Languages\Italian.isl"
Name: "japanese"; MessagesFile: "compiler:Languages\Japanese.isl"
Name: "nepali"; MessagesFile: "compiler:Languages\Nepali.islu"
Name: "norwegian"; MessagesFile: "compiler:Languages\Norwegian.isl"
Name: "polish"; MessagesFile: "compiler:Languages\Polish.isl"
Name: "portuguese"; MessagesFile: "compiler:Languages\Portuguese.isl"
Name: "russian"; MessagesFile: "compiler:Languages\Russian.isl"
Name: "serbiancyrillic"; MessagesFile: "compiler:Languages\SerbianCyrillic.isl"
Name: "serbianlatin"; MessagesFile: "compiler:Languages\SerbianLatin.isl"
Name: "slovenian"; MessagesFile: "compiler:Languages\Slovenian.isl"
Name: "spanish"; MessagesFile: "compiler:Languages\Spanish.isl"
Name: "ukrainian"; MessagesFile: "compiler:Languages\Ukrainian.isl"
