program piserver;

{$mode objfpc}{$H+}

uses
  {$IFDEF UNIX}{$IFDEF UseCThreads}
  cthreads,
  {$ENDIF}{$ENDIF}
  Classes, SysUtils, CustApp,
  rd_protocol;

type

  { TPiServer }

  TPiServer = class(TCustomApplication)
  protected
    Redis : TRedisIO;

    procedure Say( msg: string);

    procedure DoRun; override;
    function ConnectRedis : boolean;
  public
    constructor Create(TheOwner: TComponent); override;
    destructor Destroy; override;
    procedure WriteHelp; virtual;

    procedure OnRedisConnect(Sender: TObject);
  end;


  {$R *.res}


var
  pi: TPiServer;




procedure TPiServer.Say( msg: string);
begin
  Writeln (msg);
end;

procedure TPiServer.OnRedisConnect(Sender: TObject);
begin
  if Redis.Connected then
    Say('Connected to Redis...')
  else
    Say('Unable to connect to Redis.');

end;

function TPiServer.ConnectRedis : boolean;
begin
  Redis.TargetHost := '127.0.0.1';
  Redis.TargetPort := '6379';
  Redis.Connect;
end;

procedure TPiServer.DoRun;
var
  ErrorMsg: String;
begin
  // quick check parameters
  ErrorMsg := CheckOptions('h','help');
  if ErrorMsg <> '' then begin
    ShowException(Exception.Create(ErrorMsg));
    Terminate;
    Exit;
  end;

  // parse parameters
  if HasOption('h','help') then begin
    WriteHelp;
    Terminate;
    Exit;
  end;

  ConnectRedis;


end;

constructor TPiServer.Create(TheOwner: TComponent);
begin
  inherited Create(TheOwner);
  StopOnException := True;

  Redis := TRedisIO.Create;
  Redis.OnAfterConnect := @OnRedisConnect;

end;

destructor TPiServer.Destroy;
begin
  Redis.Free;
  inherited Destroy;
end;

procedure TPiServer.WriteHelp;
begin
  { add your help code here }
  writeln('Usage: ',ExeName,' -h');
end;


begin
  pi := TPiServer.Create(nil);
  pi.Title:='Pi Server';
  pi.Run;
  pi.Free;
end.

