unit main;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils, FileUtil, Forms, Controls, Graphics, Dialogs, ComCtrls,
  ShellCtrls, EditBtn;

type

  { Tmainform }

  Tmainform = class(TForm)
    DirectoryEdit1: TDirectoryEdit;
    ShellListView1: TShellListView;
    ShellTreeView1: TShellTreeView;
    procedure DirectoryEdit1AcceptDirectory(Sender: TObject; var Value: String);
  private
    { private declarations }
  public
    { public declarations }
  end;

var
  mainform: Tmainform;

implementation

{$R *.lfm}

{ Tmainform }

procedure Tmainform.DirectoryEdit1AcceptDirectory(Sender: TObject;
  var Value: String);
begin
  ShellTreeView1.Root := Value;
end;

end.

