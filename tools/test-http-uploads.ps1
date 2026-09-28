$ErrorActionPreference = 'Stop'
$workspaceRoot = Split-Path -Parent $PSScriptRoot
$phpBinary = (Get-Command php).Source
$fixtureRoot = Join-Path $workspaceRoot 'tests/http'
$storageRoot = Join-Path $workspaceRoot 'build/http-upload-storage'
New-Item -ItemType Directory -Force -Path $storageRoot | Out-Null
$probe = [System.Net.Sockets.TcpClient]::new()
try { $probe.Connect('127.0.0.1', 13369); throw 'Upload fixture port already in use.' }
catch [System.Net.Sockets.SocketException] { }
finally { $probe.Dispose() }
$nonceBytes = [byte[]]::new(32)
[System.Security.Cryptography.RandomNumberGenerator]::Fill($nonceBytes)
$nonceValue = [Convert]::ToHexString($nonceBytes).ToLowerInvariant()
@{nonce=$nonceValue; storage=$storageRoot} | ConvertTo-Json | Set-Content -LiteralPath (Join-Path $workspaceRoot 'build/upload-test.json') -Encoding utf8
$server = Start-Process -FilePath $phpBinary -ArgumentList @('-S', '127.0.0.1:13369', '-t', $fixtureRoot) -WindowStyle Hidden -PassThru -RedirectStandardOutput (Join-Path $workspaceRoot 'build/upload-server.log') -RedirectStandardError (Join-Path $workspaceRoot 'build/upload-server-errors.log')
try {
    $ready = $false
    for ($attempt = 0; $attempt -lt 30; $attempt++) {
        $connection = [System.Net.Sockets.TcpClient]::new()
        try { $connection.Connect('127.0.0.1', 13369); $ready = $true; break }
        catch [System.Net.Sockets.SocketException] { Start-Sleep -Milliseconds 100 }
        finally { $connection.Dispose() }
    }
    if (-not $ready) { throw 'Upload test server failed to start.' }
    & $phpBinary (Join-Path $workspaceRoot 'tests/http-uploads.php')
    if ($LASTEXITCODE -ne 0) { throw 'HTTP upload assertions failed.' }
} finally {
    $runningServer = Get-Process -Id $server.Id -ErrorAction SilentlyContinue
    if ($null -ne $runningServer -and $runningServer.Path -eq $phpBinary) { Stop-Process -Id $server.Id }
}
