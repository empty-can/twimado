Add-Type -AssemblyName System.Windows.Forms

[System.Windows.Forms.Cursor]::Position.X # マウスのX座標
[System.Windows.Forms.Cursor]::Position.Y # マウスのY座標
# 110
# 94
# exit

# Win32API をいじるC#クラス
# C#のソースを変数に格納
$source = Get-Content Win32SendKey.cs | Out-String

# 規定では.NET FrameworkのSystem.Windows.Formsアセンブリ読み込まれないので追加する
Add-Type -Language CSharp -TypeDefinition $source -ReferencedAssemblies System.Windows.Forms 

[Win32SendKey]::LeftClick(120, 15)  # test.phpのタブをクリック

[System.Windows.Forms.SendKeys]::SendWait("{F5}")

Start-Sleep -s 1

[Win32SendKey]::LeftClick(600, 600)  # test.phpのページをクリック

[System.Windows.Forms.SendKeys]::SendWait("^a")
[System.Windows.Forms.SendKeys]::SendWait("^c")

Start-Sleep -s 1

[Win32SendKey]::LeftClick(356, 15)  # モーメント編集ページのタブをクリック
Start-Sleep -s 1
[Win32SendKey]::LeftClick(1163, 131) # モーメント編集ページを開く
Start-Sleep -s 1
[Win32SendKey]::LeftClick(1073, 183) # モーメント編集を選択
Start-Sleep -s 3
[Win32SendKey]::LeftClick(150, 400) # ポップアップを消す
Start-Sleep -s 3
[System.Windows.Forms.SendKeys]::SendWait("^f")
[System.Windows.Forms.SendKeys]::SendWait("{ENTER}")
Start-Sleep -s 3
[Win32SendKey]::LeftClick(1200, 580) # リンクからをクリック
Start-Sleep -s 1
[System.Windows.Forms.SendKeys]::SendWait("{END}")
Start-Sleep -s 1

$ids = Get-Clipboard -Format Text 
$ids -split "\r\n" | ForEach-Object -Process {
    Set-Clipboard $_
    [Win32SendKey]::LeftClick(900, 860)  # モーメント編集ページの入力バーをクリック
    [System.Windows.Forms.SendKeys]::SendWait("^a")
    [System.Windows.Forms.SendKeys]::SendWait("{DEL}")
    [System.Windows.Forms.SendKeys]::SendWait("^v")
    Start-Sleep -s 1
    [Win32SendKey]::LeftClick(1230, 860)  # ツイートを登録
    Start-Sleep -s 1
    [System.Windows.Forms.SendKeys]::SendWait("^{END}")
    Start-Sleep -s 1
}

[Win32SendKey]::LeftClick(1514, 126)  # 完了ボタンを押して完了
Start-Sleep -s 2
# [Win32SendKey]::LeftClick(878, 136)  # 後でボタンを押して完了
# Start-Sleep -s 2

[Win32SendKey]::LeftClick(530, 15)    # 拡張機能のタブをクリック
Start-Sleep -s 2
[Win32SendKey]::LeftClick(1122, 421)   # 拡張機能を有効にする
Start-Sleep -s 2

[Win32SendKey]::LeftClick(356, 15)  # モーメント編集ページのタブをクリック
Start-Sleep -s 1
[Win32SendKey]::LeftClick(1163, 131) # モーメント編集ページを開く
Start-Sleep -s 1
[Win32SendKey]::LeftClick(1073, 183) # モーメント編集を選択

Start-Sleep -s 5

[Win32SendKey]::LeftClick(530, 15)    # 拡張機能のタブをクリック
Start-Sleep -s 2
[Win32SendKey]::LeftClick(1122, 421)   # 拡張機能を無効にする
Start-Sleep -s 2

Start-Sleep -s 1800	# リサイズが終わるのを待つ

[Win32SendKey]::LeftClick(356, 15)  # モーメント編集ページのタブをクリック
Start-Sleep -s 1

exit