program pi;

{$mode objfpc}{$H+}

uses
  {$IFDEF UNIX}{$IFDEF UseCThreads}
  cthreads,
  {$ENDIF}{$ENDIF}
  Classes,

  BClasses, BSysUtils, CustomServer2,WebSocket2,
  rd_types,rd_protocol,rd_commands;


{$R *.res}

begin






end.

